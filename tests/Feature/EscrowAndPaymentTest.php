<?php

namespace Tests\Feature;

use App\Jobs\ConfirmPaymentJob;
use App\Jobs\RefundPaymentJob;
use App\Jobs\ReleasePaymentJob;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Role;
use App\Models\Settlement;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Payment\EscrowService;
use App\Services\PostSale\IssueResolutionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EscrowAndPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $buyer;
    protected User $seller;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::firstOrCreate(['email' => 'buyer_test@example.com'], [
            'name' => 'Test Buyer',
            'password' => bcrypt('password123'),
        ]);

        $this->seller = User::firstOrCreate(['email' => 'seller_test@example.com'], [
            'name' => 'Test Seller',
            'password' => bcrypt('password123'),
        ]);

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'permissions' => ['*' => true],
        ]);

        $this->admin = User::firstOrCreate(['email' => 'admin_test@example.com'], [
            'name' => 'Test Admin',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_payment_success_updates_invoice_records_revenue_and_creates_settlement(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));
        $payRef = 'PAY-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 10000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 10000.00,
            'commission' => 500.00,
            'payment_method' => 'platform',
            'status' => 'issued',
        ]);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'invoice_id' => $invoice->id,
            'reference' => $payRef,
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 10000.00,
            'currency' => 'NGN',
        ]);

        $escrowService = app(EscrowService::class);
        $escrowService->handlePaymentSuccessful($payment, ['channel' => 'card']);

        $payment->refresh();
        $invoice->refresh();

        $this->assertEquals('successful', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertEquals('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);

        // Platform revenue record created for 500 NGN commission
        $this->assertDatabaseHas('revenues', [
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'type' => 'commission',
            'amount' => 500.00,
        ]);

        // Seller settlement created in pending status
        $this->assertDatabaseHas('settlements', [
            'seller_id' => $this->seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 10000.00,
            'commission' => 500.00,
            'refunds' => 0.00,
            'net_amount' => 9500.00,
            'status' => 'pending',
        ]);
    }

    public function test_confirm_payment_job_processes_payment_successfully(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));
        $payRef = 'PAY-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 5000.00,
            'total' => 5000.00,
            'commission' => 250.00,
            'payment_method' => 'platform',
            'status' => 'issued',
        ]);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'invoice_id' => $invoice->id,
            'reference' => $payRef,
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 5000.00,
            'currency' => 'NGN',
        ]);

        $job = new ConfirmPaymentJob($payRef, 'paystack', ['status' => 'success']);
        $job->handle(app(EscrowService::class), app(\App\Services\Payment\PaystackService::class), app(\App\Services\Payment\FlutterwaveService::class));

        $payment->refresh();
        $this->assertEquals('successful', $payment->status);
    }

    public function test_inspection_warranty_window_sets_settlement_eligible_date(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 20000.00,
            'total' => 20000.00,
            'commission' => 1000.00,
            'payment_method' => 'platform',
            'status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'type' => 'item',
            'description' => 'Original iPhone 13 Screen',
            'quantity' => 1,
            'unit_price' => 20000.00,
            'amount' => 20000.00,
            'warranty_period_days' => 7, // 7 days warranty
        ]);

        $settlement = Settlement::create([
            'seller_id' => $this->seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 20000.00,
            'commission' => 1000.00,
            'refunds' => 0.00,
            'net_amount' => 19000.00,
            'status' => 'pending',
        ]);

        $escrowService = app(EscrowService::class);
        $escrowService->startInspectionWarrantyWindow($invoice);

        $settlement->refresh();
        $this->assertNotNull($settlement->eligible_at);
        $this->assertTrue($settlement->eligible_at->isFuture());
        // Settlement is not yet eligible since 7 days haven't elapsed
        $this->assertFalse($escrowService->canReleaseSettlement($settlement));
    }

    public function test_release_payment_job_releases_only_when_timer_elapsed_and_no_disputes(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 15000.00,
            'total' => 15000.00,
            'commission' => 750.00,
            'payment_method' => 'platform',
            'status' => 'paid',
        ]);

        $settlement = Settlement::create([
            'seller_id' => $this->seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 15000.00,
            'commission' => 750.00,
            'refunds' => 0.00,
            'net_amount' => 14250.00,
            'status' => 'pending',
            'eligible_at' => now()->subMinutes(10), // Passed the warranty window
        ]);

        $job = new ReleasePaymentJob($settlement->id);
        $job->handle(app(EscrowService::class));

        $settlement->refresh();
        $invoice->refresh();

        $this->assertEquals('eligible', $settlement->status);
        $this->assertNotNull($invoice->completed_at);
    }

    public function test_issue_reporting_freezes_settlement_and_acceptance_issues_refund(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 30000.00,
            'total' => 30000.00,
            'commission' => 1500.00,
            'payment_method' => 'platform',
            'status' => 'paid',
        ]);

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'type' => 'item',
            'description' => 'Toyota Camry Alternator',
            'quantity' => 1,
            'unit_price' => 30000.00,
            'amount' => 30000.00,
        ]);

        $settlement = Settlement::create([
            'seller_id' => $this->seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 30000.00,
            'commission' => 1500.00,
            'refunds' => 0.00,
            'net_amount' => 28500.00,
            'status' => 'pending',
            'eligible_at' => now()->subHour(),
        ]);

        $issueService = app(IssueResolutionService::class);

        // Buyer reports damaged alternator
        $issue = $issueService->reportIssue(
            $invoice,
            $this->buyer,
            'damaged',
            'The alternator arrived cracked and does not output voltage',
            [['invoice_item_id' => $item->id, 'reason' => 'Cracked casing']]
        );

        $settlement->refresh();
        $this->assertEquals('disputed', $settlement->status);

        // Release job should be blocked while under dispute
        $escrowService = app(EscrowService::class);
        $this->assertFalse($escrowService->canReleaseSettlement($settlement));

        // Seller accepts responsibility
        $refund = $issueService->sellerAcceptsIssue($issue, $this->seller, 'refund');

        $this->assertNotNull($refund);
        $this->assertEquals(30000.00, (float) $refund->amount);
        $this->assertEquals('resolved', $issue->fresh()->status);

        // Execute refund job
        $refundJob = new RefundPaymentJob($refund->id);
        $refundJob->handle($escrowService, app(\App\Services\Payment\PaystackService::class), app(\App\Services\Payment\FlutterwaveService::class));

        $settlement->refresh();
        $this->assertEquals(30000.00, (float) $settlement->refunds);
        $this->assertEquals(0.00, (float) $settlement->net_amount);
    }

    public function test_dispute_escalation_and_admin_resolution(): void
    {
        $invNum = 'INV-' . strtoupper(bin2hex(random_bytes(6)));

        $invoice = Invoice::create([
            'invoice_number' => $invNum,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 12000.00,
            'total' => 12000.00,
            'commission' => 600.00,
            'payment_method' => 'platform',
            'status' => 'paid',
        ]);

        $settlement = Settlement::create([
            'seller_id' => $this->seller->id,
            'invoice_id' => $invoice->id,
            'gross_amount' => 12000.00,
            'commission' => 600.00,
            'refunds' => 0.00,
            'net_amount' => 11400.00,
            'status' => 'pending',
            'eligible_at' => now()->subDay(),
        ]);

        $issueService = app(IssueResolutionService::class);

        $issue = $issueService->reportIssue(
            $invoice,
            $this->buyer,
            'wrong_item',
            'Sent wrong model part'
        );

        // Escalated to Dispute
        $dispute = $issueService->openDispute(
            $issue,
            $this->seller,
            'Seller claims part was correctly identified per listing pictures'
        );

        $this->assertEquals('open', $dispute->status);

        // Admin resolves dispute in buyer's favor with partial refund
        $resolvedDispute = $issueService->adminResolveDispute(
            $dispute,
            $this->admin,
            'buyer_favor',
            6000.00,
            'Admin decided 50% partial refund for compatibility miscommunication'
        );

        $this->assertEquals('resolved', $resolvedDispute->status);
        $this->assertEquals('resolved', $issue->fresh()->status);
    }

    public function test_paystack_webhook_endpoint_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('webhooks.paystack'), [
            'event' => 'charge.success',
            'data' => ['reference' => 'INVALID-REF'],
        ], [
            'x-paystack-signature' => 'invalid_signature_hash',
        ]);

        $response->assertStatus(400);
    }

    public function test_flutterwave_webhook_endpoint_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('webhooks.flutterwave'), [
            'event' => 'charge.completed',
            'data' => ['tx_ref' => 'INVALID-TX-REF', 'status' => 'successful'],
        ], [
            'verif-hash' => 'invalid_secret_hash',
        ]);

        $response->assertStatus(400);
    }
}
