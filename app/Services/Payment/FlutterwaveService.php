<?php

namespace App\Services\Payment;

use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected string $secretKey;
    protected string $secretHash;
    protected string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = (string) config('services.flutterwave.secret', '');
        $this->secretHash = (string) config('services.flutterwave.webhook_hash', '');
    }

    /**
     * Initialize Flutterwave standard payment link.
     */
    public function initialize(Payment $payment, ?string $callbackUrl = null): array
    {
        $payload = [
            'tx_ref' => $payment->reference,
            'amount' => (float) $payment->amount,
            'currency' => strtoupper($payment->currency ?? 'NGN'),
            'redirect_url' => $callbackUrl ?? url('/payment/callback?provider=flutterwave'),
            'customer' => [
                'email' => $payment->user->email,
                'phonenumber' => $payment->user->phone ?? '',
                'name' => $payment->user->name,
            ],
            'customizations' => [
                'title' => 'Parts & Parcel Escrow Payment',
                'description' => "Payment for Invoice #{$payment->invoice?->invoice_number}",
                'logo' => asset('images/logo.png'),
            ],
            'meta' => [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'user_id' => $payment->user_id,
            ],
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/payments", $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['link'] ?? null,
                    'reference' => $payment->reference,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initialize Flutterwave payment.',
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave initialize exception: ' . $e->getMessage(), ['payment_id' => $payment->id]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Flutterwave payment by transaction reference.
     */
    public function verify(string $txRef): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->get("{$this->baseUrl}/transactions/verify_by_reference", [
                    'tx_ref' => $txRef,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success' && ($data['data']['status'] ?? '') === 'successful') {
                return [
                    'success' => true,
                    'status' => 'successful',
                    'amount' => (float) ($data['data']['amount'] ?? 0),
                    'currency' => $data['data']['currency'] ?? 'NGN',
                    'reference' => $data['data']['tx_ref'] ?? $txRef,
                    'flw_ref' => $data['data']['flw_ref'] ?? '',
                    'transaction_id' => $data['data']['id'] ?? null,
                    'paid_at' => $data['data']['created_at'] ?? now()->toIso8601String(),
                    'raw' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'status' => $data['data']['status'] ?? 'failed',
                'message' => $data['message'] ?? 'Flutterwave verification was not successful.',
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave verify exception: ' . $e->getMessage(), ['tx_ref' => $txRef]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund via Flutterwave.
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array
    {
        $payload = [
            'amount' => $amount,
            'comments' => $reason ?: 'Order issue refund approved',
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/transactions/{$transactionId}/refund", $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $data['data']['id'] ?? null,
                    'raw' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Flutterwave refund failed.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate Transfer/Payout via Flutterwave.
     */
    public function initiateTransfer(Payout $payout, BankAccount $account): array
    {
        $payload = [
            'account_bank' => $account->bank_code,
            'account_number' => $account->account_number,
            'amount' => (float) $payout->amount,
            'narration' => "Parts & Parcel Payout {$payout->reference}",
            'currency' => strtoupper($payout->currency ?? 'NGN'),
            'reference' => $payout->reference,
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/transfers", $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'transfer_id' => $data['data']['id'] ?? null,
                    'status' => $data['data']['status'] ?? 'pending',
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initiate transfer on Flutterwave.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate webhook signature/hash from Flutterwave.
     */
    public function validateWebhookSignature(Request $request): bool
    {
        $hash = (string) $request->header('verif-hash', '');
        if (! $hash) {
            return false;
        }

        $secret = $this->secretHash ?: 'test_flw_secret_hash';

        return hash_equals($secret, $hash);
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        return $this->validateWebhookSignature($request);
    }
}
