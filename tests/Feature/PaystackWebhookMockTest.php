<?php

namespace Tests\Feature;

use App\Jobs\ConfirmPaymentJob;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Payout;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Feature Test for Paystack Webhook Handling & Mocking.
 *
 * Demonstrates:
 * 1. Webhook HMAC-SHA512 Cryptographic Signature Verification.
 * 2. Testing Security Boundary (Tampered payloads, missing signatures).
 * 3. Queue Mocking (Queue::fake) to verify background jobs without real execution.
 * 4. Database state transitions on asynchronous financial events (transfer.success).
 */
class PaystackWebhookMockTest extends TestCase
{
    use RefreshDatabase;

    protected string $secret;

    protected function setUp(): void
    {
        parent::setUp();
        // Fallback secret used by PaystackService when env is testing
        $this->secret = config('services.paystack.secret_key') ?: 'test_paystack_secret';
    }

    /**
     * Helper to compute valid Paystack HMAC-SHA512 signature for a payload.
     */
    protected function computeSignature(array $payload): string
    {
        return hash_hmac('sha512', json_encode($payload), $this->secret);
    }

    /**
     * Test 1: Webhook rejects request without signature header.
     */
    public function test_webhook_rejects_missing_signature(): void
    {
        $payload = [
            'event' => 'charge.success',
            'data' => ['reference' => 'TEST-REF-001'],
        ];

        $response = $this->postJson(route('webhooks.paystack'), $payload);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid signature']);
    }

    /**
     * Test 2: Webhook rejects tampered or fraudulent signature.
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = [
            'event' => 'charge.success',
            'data' => ['reference' => 'FRAUD-REF-999'],
        ];

        $response = $this->postJson(route('webhooks.paystack'), $payload, [
            'x-paystack-signature' => 'invalid_bogus_signature_hash',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid signature']);
    }

    /**
     * Test 3: Successful charge dispatches ConfirmPaymentJob via Queue::fake.
     */
    public function test_charge_success_dispatches_confirm_payment_job(): void
    {
        // 1. ARRANGE: Mock the queue so real workers do not pick it up during test
        Queue::fake();

        $reference = 'PAY-TEST-' . time();
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $reference,
                'amount' => 5000000, // 50,000 NGN in kobo
                'currency' => 'NGN',
                'status' => 'success',
            ],
        ];

        $validSignature = $this->computeSignature($payload);

        // 2. ACT: Send webhook with correct cryptographic signature
        $response = $this->postJson(route('webhooks.paystack'), $payload, [
            'x-paystack-signature' => $validSignature,
        ]);

        // 3. ASSERT: Endpoint responds OK and job was dispatched with the reference
        $response->assertStatus(200);

        Queue::assertPushed(ConfirmPaymentJob::class, function ($job) use ($reference) {
            return $job->reference === $reference && $job->provider === 'paystack';
        });
    }

    /**
     * Test 4: Successful transfer event transitions payout and settlements to settled.
     */
    public function test_transfer_success_updates_payout_and_settlements(): void
    {
        // 1. ARRANGE: Create seller, invoice, settlement, and payout
        $seller = User::firstOrCreate(['email' => 'webhook_seller@example.com'], [
            'name' => 'Webhook Seller',
            'password' => bcrypt('password123'),
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-PO-' . time(),
            'buyer_id' => $seller->id,
            'seller_id' => $seller->id,
            'subtotal' => 50000.00,
            'total' => 50000.00,
            'commission' => 5000.00,
            'payment_method' => 'platform',
            'status' => 'paid',
        ]);

        $settlement = Settlement::create([
            'seller_id' => $seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 50000.00,
            'commission' => 5000.00,
            'refunds' => 0.00,
            'net_amount' => 45000.00,
            'status' => 'queued',
        ]);

        $payoutReference = 'PO-MOCK-' . time();
        $payout = Payout::create([
            'seller_id' => $seller->id,
            'reference' => $payoutReference,
            'amount' => 45000.00,
            'currency' => 'NGN',
            'provider' => 'paystack',
            'status' => 'processing',
        ]);

        $payout->settlements()->attach($settlement->id, ['amount' => 45000.00]);

        $payload = [
            'event' => 'transfer.success',
            'data' => [
                'reference' => $payoutReference,
                'amount' => 4500000,
                'status' => 'SUCCESS',
            ],
        ];

        $validSignature = $this->computeSignature($payload);

        // 2. ACT
        $response = $this->postJson(route('webhooks.paystack'), $payload, [
            'x-paystack-signature' => $validSignature,
        ]);

        // 3. ASSERT
        $response->assertStatus(200);

        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'successful',
        ]);

        $this->assertDatabaseHas('settlements', [
            'id' => $settlement->id,
            'status' => 'settled',
        ]);
    }
}
