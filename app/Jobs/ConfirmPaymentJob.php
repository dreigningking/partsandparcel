<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\Payment\EscrowService;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public string $reference,
        public ?string $provider = null,
        public array $gatewayData = []
    ) {}

    public function handle(
        EscrowService $escrowService,
        PaystackService $paystackService,
        FlutterwaveService $flutterwaveService
    ): void {
        $payment = Payment::where('reference', $this->reference)->first();

        if (! $payment) {
            Log::error("ConfirmPaymentJob: Payment reference '{$this->reference}' not found.");
            return;
        }

        if ($payment->status === 'successful') {
            Log::info("ConfirmPaymentJob: Payment '{$this->reference}' already marked successful.");
            return;
        }

        $provider = strtolower($this->provider ?? $payment->provider ?? 'paystack');
        $verification = [];

        if (! empty($this->gatewayData)) {
            // Use webhook-supplied data if verified
            $verification = $this->gatewayData;
            $isSuccess = ($verification['status'] ?? '') === 'success' || ($verification['status'] ?? '') === 'successful';
        } else {
            // Query gateway API directly
            if ($provider === 'flutterwave') {
                $verification = $flutterwaveService->verify($this->reference);
            } else {
                $verification = $paystackService->verify($this->reference);
            }

            $isSuccess = ! empty($verification['success']);
        }

        if ($isSuccess) {
            $escrowService->handlePaymentSuccessful($payment, $verification);
            Log::info("ConfirmPaymentJob: Payment '{$this->reference}' verified and handled successfully.");
        } else {
            Log::warning("ConfirmPaymentJob: Verification failed for reference '{$this->reference}'. Provider: {$provider}.");
            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], ['failed_verification' => $verification]),
            ]);
        }
    }
}
