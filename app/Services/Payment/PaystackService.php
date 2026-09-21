<?php

namespace App\Services\Payment;

use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = (string) config('services.paystack.secret', '');
    }

    /**
     * Initialize transaction on Paystack.
     */
    public function initialize(Payment $payment, ?string $callbackUrl = null): array
    {
        $payload = [
            'email' => $payment->user->email,
            'amount' => (int) round($payment->amount * 100), // In kobo/cents
            'currency' => strtoupper($payment->currency ?? 'NGN'),
            'reference' => $payment->reference,
            'callback_url' => $callbackUrl ?? url('/payment/callback?provider=paystack'),
            'metadata' => array_merge($payment->metadata ?? [], [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'user_id' => $payment->user_id,
            ]),
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/transaction/initialize", $payload);

            $data = $response->json();

            if ($response->successful() && ! empty($data['status'])) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'] ?? null,
                    'access_code' => $data['data']['access_code'] ?? null,
                    'reference' => $data['data']['reference'] ?? $payment->reference,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initialize Paystack payment.',
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack initialization exception: ' . $e->getMessage(), ['payment_id' => $payment->id]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify transaction on Paystack.
     */
    public function verify(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            $data = $response->json();

            if ($response->successful() && ! empty($data['status']) && ($data['data']['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'status' => 'successful',
                    'amount' => (float) (($data['data']['amount'] ?? 0) / 100),
                    'currency' => $data['data']['currency'] ?? 'NGN',
                    'reference' => $data['data']['reference'] ?? $reference,
                    'gateway_response' => $data['data']['gateway_response'] ?? '',
                    'channel' => $data['data']['channel'] ?? '',
                    'paid_at' => $data['data']['paid_at'] ?? now()->toIso8601String(),
                    'raw' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'status' => $data['data']['status'] ?? 'failed',
                'message' => $data['message'] ?? 'Payment verification failed or status not successful.',
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack verify exception: ' . $e->getMessage(), ['reference' => $reference]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund a transaction via Paystack.
     */
    public function refund(string $reference, float $amount, ?string $reason = null): array
    {
        $payload = [
            'transaction' => $reference,
            'amount' => (int) round($amount * 100),
            'merchant_note' => $reason ?: 'Order issue refund approved',
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/refund", $payload);

            $data = $response->json();

            if ($response->successful() && ! empty($data['status'])) {
                return [
                    'success' => true,
                    'refund_id' => $data['data']['id'] ?? null,
                    'raw' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Paystack refund request failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack refund exception: ' . $e->getMessage(), ['reference' => $reference]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Transfer Recipient for seller payouts.
     */
    public function createTransferRecipient(BankAccount $account): array
    {
        $payload = [
            'type' => 'nuban',
            'name' => $account->account_name,
            'account_number' => $account->account_number,
            'bank_code' => $account->bank_code,
            'currency' => $account->currency ?? 'NGN',
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/transferrecipient", $payload);

            $data = $response->json();

            if ($response->successful() && ! empty($data['status'])) {
                $recipientCode = $data['data']['recipient_code'] ?? null;
                if ($recipientCode) {
                    $account->update(['recipient_code' => $recipientCode, 'verified_at' => now()]);
                }

                return [
                    'success' => true,
                    'recipient_code' => $recipientCode,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to create Paystack transfer recipient.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate Transfer/Payout to seller recipient.
     */
    public function initiateTransfer(Payout $payout, string $recipientCode): array
    {
        $payload = [
            'source' => 'balance',
            'amount' => (int) round($payout->amount * 100),
            'recipient' => $recipientCode,
            'reason' => "Parts & Parcel Payout ref: {$payout->reference}",
            'reference' => $payout->reference,
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/transfer", $payload);

            $data = $response->json();

            if ($response->successful() && ! empty($data['status'])) {
                return [
                    'success' => true,
                    'transfer_code' => $data['data']['transfer_code'] ?? null,
                    'status' => $data['data']['status'] ?? 'pending',
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initiate transfer on Paystack.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate webhook signature from Paystack.
     */
    public function validateWebhookSignature(Request $request): bool
    {
        $signature = $request->header('x-paystack-signature');
        if (! $signature) {
            return false;
        }

        $secret = $this->secretKey ?: 'test_paystack_secret';
        $expected = hash_hmac('sha512', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        return $this->validateWebhookSignature($request);
    }
}
