<?php

namespace App\Jobs;

use App\Models\Refund;
use App\Services\Payment\EscrowService;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 15;

    public function __construct(
        public int $refundId
    ) {}

    public function handle(
        EscrowService $escrowService,
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): void {
        $refund = Refund::with(['invoice.settlement', 'payment'])->find($this->refundId);

        if (! $refund) {
            Log::error("RefundPaymentJob: Refund ID {$this->refundId} not found.");
            return;
        }

        if ($refund->status === 'completed') {
            Log::info("RefundPaymentJob: Refund ID {$this->refundId} already completed.");
            return;
        }

        $payment = $refund->payment;
        $provider = strtolower($payment?->provider ?? 'paystack');
        $gatewayResult = ['success' => true]; // Default for manual or mocked

        // Process refund through payment gateway if payment was made through platform gateway
        if ($payment && $payment->status === 'successful') {
            if ($provider === 'flutterwave') {
                // If payment has transaction_id or flw_ref
                $flwId = $payment->metadata['flw_ref'] ?? $payment->metadata['transaction_id'] ?? $payment->reference;
                $gatewayResult = $flutterwaveService->refund($flwId, (float) $refund->amount);
            } elseif ($provider === 'paystack') {
                $gatewayResult = $paystackService->refund($payment->reference, (float) $refund->amount);
            }
        }

        DB::transaction(function () use ($refund, $escrowService, $gatewayResult) {
            $refund->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // Deduct from seller settlement if settlement exists
            if ($refund->invoice && $refund->invoice->settlement) {
                $escrowService->deductRefundFromSettlement($refund->invoice->settlement, (float) $refund->amount);
            }

            // If total refunded matches or exceeds invoice total, mark invoice refunded
            $totalRefunded = $refund->invoice->refunds()->where('status', 'completed')->sum('amount');
            if ($totalRefunded >= (float) $refund->invoice->total) {
                $refund->invoice->update([
                    'status' => 'cancelled', // or refunded
                ]);
            }
        });

        Log::info("RefundPaymentJob: Refund ID {$this->refundId} completed successfully.");
    }
}
