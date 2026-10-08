<?php

namespace App\Services\Payment;

use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Settlement;
use App\Models\Setting;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowService
{
    /**
     * Handle successful payment event for invoices and subscriptions.
     */
    public function handlePaymentSuccessful(Payment $payment, array $gatewayData = []): bool
    {
        if ($payment->status === 'successful') {
            Log::info("Payment {$payment->reference} already marked successful.");
            return true;
        }

        return DB::transaction(function () use ($payment, $gatewayData) {
            $payment->update([
                'status' => 'successful',
                'paid_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway_verification' => $gatewayData,
                ]),
            ]);

            // If payment is for an Invoice
            if ($payment->invoice_id && $payment->invoice) {
                $this->processInvoicePayment($payment);
            }

            // If payment is for a Subscription
            if ($payment->subscription_id && $payment->subscription) {
                $this->processSubscriptionPayment($payment);
            }

            return true;
        });
    }

    /**
     * Process invoice payment state and escrow notifications.
     * Note: Transaction commission is zero; only escrow fees apply.
     * Settlement creation is deferred until package acceptance.
     */
    protected function processInvoicePayment(Payment $payment): void
    {
        $invoice = $payment->invoice;

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'commission' => 0.00,
        ]);

        // Convert reserved quantity to sold quantity for listing invoice items
        foreach ($invoice->items as $invItem) {
            if ($invItem->itemable_type === \App\Models\Listing::class && $invItem->itemable_id) {
                $listing = \App\Models\Listing::find($invItem->itemable_id);
                if ($listing) {
                    $qty = (int) $invItem->quantity;
                    $listing->decrement('reserved_quantity', min($qty, (int) $listing->reserved_quantity));
                    $listing->increment('sold_quantity', $qty);
                }
            }
        }

        app(\App\Services\Commercial\NegotiationService::class)->handleInvoicePaid($invoice);

        // Record escrow fee as platform service fee revenue if fee was collected
        if ((float) $payment->escrow_fee > 0) {
            Revenue::firstOrCreate(
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'type' => 'service_fee',
                ],
                [
                    'amount' => $payment->escrow_fee,
                    'currency' => $payment->currency ?? 'NGN',
                ]
            );
        }

        // Notify seller that payment is securely held in platform escrow
        if ($invoice->isPlatformEscrow() && $invoice->seller) {
            $invoice->seller->notify(new \App\Notifications\PaymentHeldInEscrowNotification($invoice));
        }
    }

    /**
     * Create seller settlement record upon buyer package acceptance (or auto-acceptance).
     * Calculates seller net proceeds as payment amount minus platform escrow fee.
     * Snapshots seller bank details into settlement and calculates eligibility timeline.
     */
    public function createSettlementOnAcceptance(Invoice $invoice): ?Settlement
    {
        if (! $invoice->isPlatformEscrow()) {
            return null;
        }

        if ($invoice->settlement) {
            return $invoice->settlement;
        }

        $payment = $invoice->payments()->where('status', 'successful')->latest()->first() ?? $invoice->payment;
        $gross = $payment ? (float) $payment->amount : (float) $invoice->total;
        $escrowFee = $payment ? (float) $payment->escrow_fee : 0.0;
        $net = max(0.00, round($gross - $escrowFee, 2));

        $seller = $invoice->seller;
        $bankAccount = $seller
            ? ($seller->bankAccounts()->where('is_default', true)->first() ?? $seller->bankAccounts()->first())
            : null;

        $bankDetails = $bankAccount ? [
            'bank_name' => $bankAccount->bank_name,
            'bank_code' => $bankAccount->bank_code,
            'account_number' => $bankAccount->account_number,
            'account_name' => $bankAccount->account_name,
            'recipient_code' => $bankAccount->recipient_code,
            'currency' => $bankAccount->currency,
        ] : null;

        $eligibleHours = (int) Setting::getValue('order_accepted_to_settlement_eligible_hours', 24);
        $warrantyDays = $invoice->maxWarrantyDays();
        $eligibleAt = now()->addHours($eligibleHours);
        if ($warrantyDays && $warrantyDays > 0) {
            $eligibleAt = $eligibleAt->addDays($warrantyDays);
        }

        $settlement = Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $invoice->seller_id,
            'payment_id' => $payment?->id,
            'amount' => $net,
            'currency' => $payment?->currency ?? $invoice->currency ?? 'NGN',
            'status' => 'pending',
            'eligible_at' => $eligibleAt,
            'bank_details' => $bankDetails,
        ]);

        // Start warranty tracking dates on invoice items if configured
        foreach ($invoice->items as $item) {
            if ($item->warranty_period_days && $item->warranty_period_days > 0) {
                $item->update([
                    'warranty_starts_at' => now(),
                    'warranty_ends_at' => now()->addDays($item->warranty_period_days),
                ]);
            }
        }

        return $settlement;
    }

    /**
     * Start inspection & warranty countdown on delivery or item receipt.
     * Creates or updates settlement record for package acceptance.
     */
    public function startInspectionWarrantyWindow(Invoice $invoice): ?Settlement
    {
        return $this->createSettlementOnAcceptance($invoice);
    }

    /**
     * Check if a settlement is eligible for release.
     */
    public function canReleaseSettlement(Settlement $settlement): bool
    {
        // 0. Seller payout must not be frozen
        if ($settlement->seller && $settlement->seller->freeze_payout) {
            return false;
        }

        // 1. Must have an eligible_at date that has passed
        if (! $settlement->eligible_at || $settlement->eligible_at->isFuture()) {
            return false;
        }

        // 2. Must not be settled already or cancelled
        if (in_array($settlement->status, ['settled', 'disputed'])) {
            return false;
        }

        // 3. Must not have any active/open issues or disputes on the invoice
        $hasOpenIssues = Issue::where('invoice_id', $settlement->invoice_id)
            ->where('status', 'open')
            ->exists();

        if ($hasOpenIssues) {
            return false;
        }

        $hasOpenDisputes = Dispute::where('invoice_id', $settlement->invoice_id)
            ->whereIn('status', ['open', 'under_review'])
            ->exists();

        if ($hasOpenDisputes) {
            return false;
        }

        return true;
    }

    /**
     * Release escrow funds to the seller's available settlement balance.
     */
    public function releaseSettlement(Settlement $settlement): bool
    {
        if (! $this->canReleaseSettlement($settlement)) {
            Log::warning("Settlement #{$settlement->id} cannot be released due to warranty/inspection window or open disputes.");
            return false;
        }

        return DB::transaction(function () use ($settlement) {
            $settlement->update([
                'status' => 'eligible',
            ]);

            // Mark invoice completed timestamp if not already set
            if ($settlement->invoice && ! $settlement->invoice->completed_at) {
                $settlement->invoice->update([
                    'completed_at' => now(),
                ]);
            }

            return true;
        });
    }

    /**
     * Deduct approved refund amount from settlement.
     */
    public function deductRefundFromSettlement(Settlement $settlement, float $refundAmount): Settlement
    {
        $settlement->amount = max(0, (float) $settlement->amount - $refundAmount);
        $settlement->save();

        return $settlement;
    }

    /**
     * Freeze settlement due to an active dispute.
     */
    public function freezeForDispute(Settlement $settlement): void
    {
        $settlement->update([
            'status' => 'disputed',
        ]);
    }

    /**
     * Unfreeze settlement once dispute has been resolved.
     */
    public function unfreezeAfterDispute(Settlement $settlement): void
    {
        $newStatus = ($settlement->eligible_at && $settlement->eligible_at->isPast())
            ? 'eligible'
            : 'pending';

        $settlement->update([
            'status' => $newStatus,
        ]);
    }
}
