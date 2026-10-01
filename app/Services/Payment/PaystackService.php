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

    public function getBanks(string $country = 'NG'): array
    {
        try {
            $secret = $this->secretKey ?: config('services.paystack.secret');
            if ($secret) {
                $response = Http::withToken($secret)
                    ->timeout(6)
                    ->get("{$this->baseUrl}/bank", ['country' => strtolower($country)]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (! empty($json['status']) && ! empty($json['data'])) {
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
            Log::warning('Paystack getBanks API failed: ' . $e->getMessage());
        }

        return $this->getFallbackBanks();
    }

    public function resolveAccount(string $bankCode, string $accountNumber): ?string
    {
        try {
            $secret = $this->secretKey ?: config('services.paystack.secret');
            if ($secret && strlen($accountNumber) === 10) {
                $response = Http::withToken($secret)
                    ->timeout(6)
                    ->get("{$this->baseUrl}/bank/resolve", [
                        'account_number' => $accountNumber,
                        'bank_code' => $bankCode,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (! empty($json['status']) && ! empty($json['data']['account_name'])) {
                        return $json['data']['account_name'];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Paystack resolveAccount error: ' . $e->getMessage());
        }

        return null;
    }

    public function getFallbackBanks(): array
    {
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

        if ($countryIso == 'ZA') {

            $response = Curl::to('https://api.paystack.co/bank/validate')
                ->withHeader('Authorization: Bearer ' . config('services.paystack.secret'))
                ->withHeader('Content-Type: application/json')
                ->withData(
                    [
                        'bank_code' => $bankCode,
                        "country_code" => "ZA",
                        "account_number" => $accountNumber,
                        "account_name" => $accountName,
                        "account_type" => $accountType,
                        "document_type" => $documentType,
                        "document_number" => $documentNumber
                    ]
                )
                ->asJsonResponse()
                ->post();
            if (!$response || !$response->status) {
                return false;
            }
            if ($response->data->accountHolderMatch && $response->data->verified && $response->data->accountOpen && $response->data->accountAcceptsCredits) {
                $bankAccount->verified_at = now();
                $bankAccount->account_status = true;
                $bankAccount->save();
                return true;
            }
            return false;
        } elseif ($countryIso == 'GH' || $countryIso == 'NG') {
            $response = Curl::to('https://api.paystack.co/bank/resolve')
                ->withHeader('Authorization: Bearer ' . config('services.paystack.secret'))
                ->withHeader('Content-Type: application/json')
                ->withData(array('account_number' => $accountNumber, "bank_code" => $bankCode))
                ->asJsonResponse()
                ->get();

            if (!$response || !$response->status) {
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

    protected function chargeCardWithAuthorizationCode($authorization_code, $amount, $email)
    {
        $response = Curl::to('https://api.paystack.co/transaction/charge_authorization')
            ->withHeader('Authorization: Bearer ' . config('services.paystack.secret'))
            ->withHeader('Content-Type: application/json')
            ->withData([
                "authorization_code" => $authorization_code,
                "email" => $email,
                "amount" => $amount * 100
            ])
            ->asJson()
            ->post();
        return $response;
    }
}
