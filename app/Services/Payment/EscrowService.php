<?php

namespace App\Services\Payment;

use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Settlement;
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
     * Process invoice payment state, platform revenue, and escrow settlement.
     */
    protected function processInvoicePayment(Payment $payment): void
    {
        $invoice = $payment->invoice;

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Record platform commission revenue
        if ((float) $invoice->commission > 0) {
            Revenue::firstOrCreate(
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'type' => 'commission',
                ],
                [
                    'amount' => $invoice->commission,
                    'currency' => $payment->currency ?? 'NGN',
                ]
            );
        }

        // If using platform escrow, initialize settlement record for seller
        if ($invoice->isPlatformEscrow()) {
            $gross = (float) $invoice->total;
            $commission = (float) $invoice->commission;
            $net = max(0, $gross - $commission);

            Settlement::firstOrCreate(
                [
                    'invoice_id' => $invoice->id,
                ],
                [
                    'seller_id' => $invoice->seller_id,
                    'gross_amount' => $gross,
                    'commission' => $commission,
                    'refunds' => 0.00,
                    'net_amount' => $net,
                    'status' => 'pending',
                    'eligible_at' => null, // Set when delivery/warranty period begins
                ]
            );

            if ($invoice->seller) {
                $invoice->seller->notify(new \App\Notifications\PaymentHeldInEscrowNotification($invoice));
            }
        }
    }

    /**
     * Process subscription plan payment.
     */
    protected function processSubscriptionPayment(Payment $payment): void
    {
        $subscription = $payment->subscription;
        $plan = $subscription->plan;

        $startsAt = now();
        $endsAt = match ($plan?->billing_interval) {
            'yearly' => now()->addYear(),
            'quarterly' => now()->addMonths(3),
            default => now()->addMonth(),
        };

        $subscription->update([
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'response_limit' => $plan?->response_limit ?? $subscription->response_limit,
        ]);

        Revenue::firstOrCreate(
            [
                'payment_id' => $payment->id,
                'type' => 'subscription',
            ],
            [
                'invoice_id' => null,
                'amount' => $payment->amount,
                'currency' => $payment->currency ?? 'NGN',
            ]
        );
    }

    /**
     * Start inspection & warranty countdown on delivery or item receipt.
     * Escrow release eligibility timer = delivered_at + max(warranty_days, default_hours).
     */
    public function startInspectionWarrantyWindow(Invoice $invoice): ?Settlement
    {
        $settlement = $invoice->settlement;
        if (! $settlement) {
            return null;
        }

        $warrantyDays = $invoice->maxWarrantyDays();

        if ($warrantyDays && $warrantyDays > 0) {
            $eligibleAt = now()->addDays($warrantyDays);
        } else {
            $defaultHours = (int) config('services.escrow.default_inspection_hours', 48);
            $eligibleAt = now()->addHours($defaultHours);
        }

        $settlement->update([
            'eligible_at' => $eligibleAt,
        ]);

        // Also update invoice items warranty start and end dates
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
     * Check if a settlement is eligible for release.
     */
    public function canReleaseSettlement(Settlement $settlement): bool
    {
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

        $hasOpenDisputes = Dispute::whereHas('issue', function ($query) use ($settlement) {
            $query->where('invoice_id', $settlement->invoice_id);
        })->where('status', 'open')->exists();

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
        $settlement->refunds = (float) $settlement->refunds + $refundAmount;
        $settlement->net_amount = max(0, (float) $settlement->gross_amount - (float) $settlement->commission - (float) $settlement->refunds);
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
