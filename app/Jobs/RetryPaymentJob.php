<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Payout;
use App\Services\Payment\EscrowService;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public int $id,
        public string $type = 'payment' // 'payment' or 'payout'
    ) {}

    public function handle(
        EscrowService $escrowService,
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): void {
        if ($this->type === 'payment') {
            $this->retryPayment($escrowService, $paystackService, $flutterwaveService);
        } elseif ($this->type === 'payout') {
            $this->retryPayout($paystackService, $flutterwaveService);
        }
    }

    protected function retryPayment(
        EscrowService $escrowService,
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): void {
        $payment = Payment::find($this->id);

        if (! $payment || $payment->status === 'successful') {
            return;
        }

        $provider = strtolower($payment->provider ?? 'paystack');

        $result = ($provider === 'flutterwave')
            ? $flutterwaveService->verify($payment->reference)
            : $paystackService->verify($payment->reference);

        if (! empty($result['success'])) {
            $escrowService->handlePaymentSuccessful($payment, $result);
            Log::info("RetryPaymentJob: Payment #{$payment->id} verified and marked successful on retry.");
        } else {
            Log::warning("RetryPaymentJob: Payment #{$payment->id} still pending or failed on retry.");
        }
    }

    protected function retryPayout(
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): void {
        $payout = Payout::find($this->id);

        if (! $payout || $payout->status === 'successful') {
            return;
        }

        $provider = strtolower($payout->provider ?? 'paystack');

        // Retrying payout initiates transfer if not already initiated
        $bankAccount = $payout->seller->bankAccounts()->where('is_active', true)->first();

        if (! $bankAccount) {
            Log::error("RetryPaymentJob: Payout #{$payout->id} seller has no active bank account.");
            return;
        }

        $result = ($provider === 'flutterwave')
            ? $flutterwaveService->initiateTransfer($payout, $bankAccount)
            : $paystackService->initiateTransfer($payout, $bankAccount);

        if (! empty($result['success'])) {
            $payout->update([
                'status' => 'processing',
                'metadata' => array_merge($payout->metadata ?? [], ['retry_transfer' => $result]),
            ]);
            Log::info("RetryPaymentJob: Payout #{$payout->id} transfer dispatched successfully.");
        } else {
            Log::warning("RetryPaymentJob: Payout #{$payout->id} transfer retry failed.");
        }
    }
}
