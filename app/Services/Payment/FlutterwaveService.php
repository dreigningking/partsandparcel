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

    public function getBanks(string $country = 'NG'): array
    {
        try {
            $secret = $this->secretKey ?: config('services.flutterwave.secret');
            if ($secret) {
                $response = Http::withToken($secret)
                    ->timeout(6)
                    ->get("{$this->baseUrl}/banks/" . strtoupper($country), ['include_provider_type' => 1]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (! empty($json['data']) && ($json['status'] ?? '') === 'success') {
                        return collect($json['data'])->map(function ($b) {
                            return [
                                'name' => $b['name'] ?? '',
                                'code' => (string) ($b['code'] ?? ''),
                            ];
                        })->filter(fn ($b) => ! empty($b['name']) && ! empty($b['code']))
                          ->sortBy('name')
                          ->values()
                          ->all();
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return [
            ['name' => 'Access Bank', 'code' => '044'],
            ['name' => 'Citibank Nigeria', 'code' => '023'],
            ['name' => 'Ecobank Nigeria', 'code' => '050'],
            ['name' => 'Fidelity Bank', 'code' => '070'],
            ['name' => 'First Bank of Nigeria', 'code' => '011'],
            ['name' => 'First City Monument Bank (FCMB)', 'code' => '214'],
            ['name' => 'Guaranty Trust Bank (GTBank)', 'code' => '058'],
            ['name' => 'Heritage Bank', 'code' => '030'],
            ['name' => 'Jaiz Bank', 'code' => '301'],
            ['name' => 'Keystone Bank', 'code' => '082'],
            ['name' => 'Kuda Bank', 'code' => '50211'],
            ['name' => 'Moniepoint Microfinance Bank', 'code' => '50515'],
            ['name' => 'OPay Digital Services', 'code' => '999992'],
            ['name' => 'Palmpay', 'code' => '999991'],
            ['name' => 'Polaris Bank', 'code' => '076'],
            ['name' => 'Stanbic IBTC Bank', 'code' => '221'],
            ['name' => 'Standard Chartered Bank', 'code' => '068'],
            ['name' => 'Sterling Bank', 'code' => '232'],
            ['name' => 'Taj Bank', 'code' => '302'],
            ['name' => 'Union Bank of Nigeria', 'code' => '032'],
            ['name' => 'United Bank for Africa (UBA)', 'code' => '033'],
            ['name' => 'Unity Bank', 'code' => '215'],
            ['name' => 'Wema Bank (ALAT)', 'code' => '035'],
            ['name' => 'Zenith Bank', 'code' => '057'],
        ];
    }

    public function resolveAccount(string $bankCode, string $accountNumber): ?string
    {
        try {
            $secret = $this->secretKey ?: config('services.flutterwave.secret');
            if ($secret && strlen($accountNumber) === 10) {
                $response = Http::withToken($secret)
                    ->timeout(6)
                    ->post("{$this->baseUrl}/accounts/resolve", [
                        'account_number' => $accountNumber,
                        'account_bank' => $bankCode,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (! empty($json['data']['account_name'])) {
                        return $json['data']['account_name'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    protected function resolveBankAccount(BankAccount $bankAccount)
    {

        $bankingFields = is_array($bankAccount->banking_fields) ? $bankAccount->banking_fields : (json_decode($bankAccount->banking_fields, true) ?: []);
        $bankCode = $bankingFields['bank_code'] ?? null;
        $accountNumber = $bankingFields['account_number'] ?? null;
        $accountName = $bankingFields['account_name'] ?? null;
        $accountType = $bankingFields['account_type'] ?? null;
        $documentType = $bankingFields['document_type'] ?? null;
        $documentNumber = $bankingFields['document_number'] ?? null;
        $countryIso = $bankAccount->accountable?->country?->iso2 ?? 'NG';
        $currency = $bankAccount->currency ?: ($bankAccount->accountable?->country?->currency_symbol ?? 'NGN');

        if (!$bankCode || !$accountNumber) {
            return false;
        }

        if ($countryIso == 'NG') {
            $response = Curl::to('https://api.flutterwave.com/banks/account-resolve')
                ->withHeader('Authorization: Bearer ' . config('services.flutterwave.secret'))
                ->withHeader('Content-Type: application/json')
                ->withData(['currency' => 'NGN', 'account' => ['number' => $accountNumber, "code" => $bankCode]])
                ->asJsonResponse()
                ->get();

            if (!$response || !isset($response->status) || $response->status != 'success') {
                return false;
            }
            $bankingFields['account_name'] = $response->data->account_name;
            $bankAccount->banking_fields = $bankingFields;
            $bankAccount->verified_at = now();
            $bankAccount->account_status = true;
            $bankAccount->save();
            return true;
        } else {
            return false;
        }
    }

    protected function chargeCardWithToken(string $token, float $amount, string $currency, string $reference)
    {


        $response = Curl::to('https://api.flutterwave.com/v3/tokenized-charges')
            ->withHeader('Authorization: Bearer ' . config('services.flutterwave.secret'))
            ->withData(array(
                'token' => $token,
                "currency" => strtoupper($currency),
                "country" => "NG",
                "amount" => $amount,
                "email" => $reference,
                "tx_ref" => "UNIQUE_PAYMENT_REFERENCE",
                "redirect_url" => "https://example_company.com/success",
                "trace_id" => "123456789",
                "is_unscheduled" => true,
                "narration" => "Monthly subscription"
            ))
            ->asJson()
            ->post();
        if ($response && $response->status == 'success')
            return $response;
        else return false;
    }
}
