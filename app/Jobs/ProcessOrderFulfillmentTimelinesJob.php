<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Refund;
use App\Models\Replacement;
use App\Models\ReturnRecord;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\AdminDelayedShipmentNotification;
use App\Notifications\OrderAutoAcceptedNotification;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\PackageAutoMarkedPickedNotification;
use App\Notifications\ReplacementAutoRefundedNotification;
use App\Notifications\ReturnAutoAcceptedNotification;
use App\Notifications\ReturnWindowExpiredNotification;
use App\Notifications\SellerFulfillmentWarningNotification;
use App\Services\Payment\EscrowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderFulfillmentTimelinesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EscrowService $escrowService): void
    {
        Log::info('ProcessOrderFulfillmentTimelinesJob: Starting execution of marketplace fulfillment timeline monitors.');

        $this->processFulfillmentWarnings();
        $this->processFulfillmentAutoCancellations();
        $this->processPickupAllowanceExpirations();
        $this->processDelayedShipments();
        $this->processDeliveredAutoAcceptances($escrowService);
        $this->processReturnWindowExpirations($escrowService);
        $this->processReturnAutoAcceptances();
        $this->processReplacementAutoRefunds();

        Log::info('ProcessOrderFulfillmentTimelinesJob: Completed fulfillment timeline monitors scan.');
    }

    /**
     * 1. Warn seller when paid order is approaching cancellation deadline without fulfillment.
     */
    protected function processFulfillmentWarnings(): void
    {
        $hours = (int) Setting::getValue('order_processing_to_auto_cancel_warning_hours', 48);
        $cutoff = now()->subHours($hours);

        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereNull('shipped_at')
            ->whereNull('ready_for_pickup_at')
            ->whereNull('auto_cancel_warned_at')
            ->where('paid_at', '<=', $cutoff)
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update(['auto_cancel_warned_at' => now()]);

            if ($invoice->seller) {
                $invoice->seller->notify(new SellerFulfillmentWarningNotification($invoice));
            }
        }

        if ($invoices->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Sent fulfillment warnings for {$invoices->count()} invoices.");
        }
    }

    /**
     * 2. Auto-cancel unfulfilled order when processing window expires.
     */
    protected function processFulfillmentAutoCancellations(): void
    {
        $hours = (int) Setting::getValue('order_processing_to_auto_cancel_hours', 72);
        $cutoff = now()->subHours($hours);

        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereNull('shipped_at')
            ->whereNull('ready_for_pickup_at')
            ->where('paid_at', '<=', $cutoff)
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => null,
                'cancellation_reason' => 'Fulfillment window expired: Seller failed to ship or prepare package within the required timeline.',
            ]);

            // Dispatch automatic buyer refund if paid
            $payment = $invoice->payments()->where('status', 'successful')->latest()->first() ?? $invoice->payment;
            if ($payment) {
                $refund = Refund::create([
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'buyer_id' => $invoice->buyer_id,
                    'seller_id' => $invoice->seller_id,
                    'amount' => (float) $invoice->total,
                    'status' => 'pending',
                    'reason' => 'Order auto-cancelled due to seller fulfillment timeout.',
                ]);
                RefundPaymentJob::dispatch($refund->id);
            }

            if ($invoice->buyer) {
                $invoice->buyer->notify(new OrderCancelledNotification(
                    $invoice,
                    null,
                    true,
                    'Order was automatically cancelled because the seller did not fulfill within the allowed timeframe. A full refund has been queued.'
                ));
            }

            if ($invoice->seller) {
                $invoice->seller->notify(new OrderCancelledNotification(
                    $invoice,
                    null,
                    true,
                    'Order was automatically cancelled due to expiration of the fulfillment timeframe.'
                ));
            }
        }

        if ($invoices->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Auto-cancelled {$invoices->count()} expired unfulfilled invoices.");
        }
    }

    /**
     * 3. Auto-mark ready pickup packages as picked up / delivered when allowance expires.
     */
    protected function processPickupAllowanceExpirations(): void
    {
        $hours = (int) Setting::getValue('order_pickup_allowance_hours', 48);
        $cutoff = now()->subHours($hours);

        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('ready_for_pickup_at')
            ->whereNull('delivered_at')
            ->where('ready_for_pickup_at', '<=', $cutoff)
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update([
                'delivered_at' => now(),
            ]);

            if ($invoice->buyer) {
                $invoice->buyer->notify(new PackageAutoMarkedPickedNotification($invoice));
            }
        }

        if ($invoices->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Auto-marked {$invoices->count()} packages as picked up.");
        }
    }

    /**
     * 4. Alert admins when shipped packages exceed expected transit time without delivery.
     */
    protected function processDelayedShipments(): void
    {
        $hours = (int) Setting::getValue('order_shipped_to_delivery_hours', 168);
        $cutoff = now()->subHours($hours);

        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('shipped_at')
            ->whereNull('delivered_at')
            ->whereNull('admin_followup_notified_at')
            ->where('shipped_at', '<=', $cutoff)
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        $admins = User::whereNotNull('role_id')->get();

        foreach ($invoices as $invoice) {
            $invoice->update(['admin_followup_notified_at' => now()]);

            foreach ($admins as $admin) {
                $admin->notify(new AdminDelayedShipmentNotification($invoice));
            }
        }

        Log::info("ProcessOrderFulfillmentTimelinesJob: Dispatched delayed shipment alerts for {$invoices->count()} orders to {$admins->count()} administrators.");
    }

    /**
     * 5. Auto-accept orders delivered when inspection window elapses without open dispute.
     */
    protected function processDeliveredAutoAcceptances(EscrowService $escrowService): void
    {
        $hours = (int) Setting::getValue('order_delivered_to_auto_acceptance_hours', 72);
        $cutoff = now()->subHours($hours);

        $invoices = Invoice::where('status', 'paid')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $cutoff)
            ->whereDoesntHave('issues', fn ($q) => $q->where('status', 'open'))
            ->whereDoesntHave('disputes', fn ($q) => $q->whereIn('status', ['open', 'under_review']))
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'completed_at' => now(),
            ]);

            // Finalize settlement record for seller
            $escrowService->createSettlementOnAcceptance($invoice);

            if ($invoice->buyer) {
                $invoice->buyer->notify(new OrderAutoAcceptedNotification($invoice));
            }

            if ($invoice->seller) {
                $invoice->seller->notify(new OrderAutoAcceptedNotification($invoice));
            }
        }

        if ($invoices->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Auto-accepted {$invoices->count()} delivered orders.");
        }
    }

    /**
     * 6. Expire pending returns where buyer failed to dispatch/ship return item.
     */
    protected function processReturnWindowExpirations(EscrowService $escrowService): void
    {
        $hours = (int) Setting::getValue('order_rejected_to_returned_hours', 48);
        $cutoff = now()->subHours($hours);

        $returns = ReturnRecord::where('status', 'pending')
            ->whereNull('received_at')
            ->whereNull('shipment_id')
            ->where('created_at', '<=', $cutoff)
            ->get();

        foreach ($returns as $return) {
            $return->update(['status' => 'expired']);

            if ($return->issue) {
                $return->issue->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            }

            // Unfreeze escrow settlement
            if ($return->invoice && $return->invoice->settlement) {
                $escrowService->unfreezeAfterDispute($return->invoice->settlement);
            }

            if ($return->buyer) {
                $return->buyer->notify(new ReturnWindowExpiredNotification($return));
            }

            if ($return->seller) {
                $return->seller->notify(new ReturnWindowExpiredNotification($return));
            }
        }

        if ($returns->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Expired {$returns->count()} unreturned return items.");
        }
    }

    /**
     * 7. Auto-accept seller-received return package when inspection timer expires.
     */
    protected function processReturnAutoAcceptances(): void
    {
        $hours = (int) Setting::getValue('order_returned_to_auto_acceptance_hours', 48);
        $cutoff = now()->subHours($hours);

        $returns = ReturnRecord::where('status', 'received')
            ->whereNotNull('received_at')
            ->where('received_at', '<=', $cutoff)
            ->get();

        foreach ($returns as $return) {
            $return->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // Dispatch buyer refund
            $payment = $return->invoice?->payments()->where('status', 'successful')->latest()->first()
                ?? $return->invoice?->payment;

            if ($payment) {
                $refund = Refund::create([
                    'invoice_id' => $return->invoice_id,
                    'payment_id' => $payment->id,
                    'buyer_id' => $return->buyer_id,
                    'seller_id' => $return->seller_id,
                    'amount' => (float) $return->invoice?->total,
                    'status' => 'pending',
                    'reason' => 'Return auto-accepted after seller return inspection timeframe expired.',
                ]);
                RefundPaymentJob::dispatch($refund->id);
            }

            if ($return->buyer) {
                $return->buyer->notify(new ReturnAutoAcceptedNotification($return));
            }

            if ($return->seller) {
                $return->seller->notify(new ReturnAutoAcceptedNotification($return));
            }
        }

        if ($returns->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Auto-accepted {$returns->count()} return records.");
        }
    }

    /**
     * 8. Auto-refund buyer when seller fails to dispatch approved replacement unit in time.
     */
    protected function processReplacementAutoRefunds(): void
    {
        $hours = (int) Setting::getValue('order_replacement_to_auto_refund_hours', 48);
        $cutoff = now()->subHours($hours);

        $replacements = Replacement::where('status', 'pending')
            ->whereNull('sent_at')
            ->where('created_at', '<=', $cutoff)
            ->get();

        foreach ($replacements as $replacement) {
            $replacement->update(['status' => 'cancelled']);

            // Dispatch buyer refund
            $payment = $replacement->invoice?->payments()->where('status', 'successful')->latest()->first()
                ?? $replacement->invoice?->payment;

            if ($payment) {
                $refund = Refund::create([
                    'invoice_id' => $replacement->invoice_id,
                    'payment_id' => $payment->id,
                    'buyer_id' => $replacement->buyer_id,
                    'seller_id' => $replacement->seller_id,
                    'amount' => (float) $replacement->invoice?->total,
                    'status' => 'pending',
                    'reason' => 'Replacement unit was not dispatched within the allowed timeframe. Buyer auto-refunded.',
                ]);
                RefundPaymentJob::dispatch($refund->id);
            }

            if ($replacement->buyer) {
                $replacement->buyer->notify(new ReplacementAutoRefundedNotification($replacement));
            }

            if ($replacement->seller) {
                $replacement->seller->notify(new ReplacementAutoRefundedNotification($replacement));
            }
        }

        if ($replacements->count() > 0) {
            Log::info("ProcessOrderFulfillmentTimelinesJob: Auto-refunded {$replacements->count()} unfulfilled replacement records.");
        }
    }
}
