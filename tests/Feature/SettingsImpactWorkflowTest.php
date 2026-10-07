<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrderFulfillmentTimelinesJob;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Replacement;
use App\Models\ReturnRecord;
use App\Models\Setting;
use App\Models\Settlement;
use App\Models\User;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\PackageReadyForPickupNotification;
use App\Rules\ProhibitedWordsRule;
use App\Services\Payment\EscrowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SettingsImpactWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::setValue('prohibited_words', 'scam,fraud,fake,ponzi');
        Setting::setValue('order_processing_to_cancel_hours', '24');
        Setting::setValue('order_processing_to_auto_cancel_warning_hours', '48');
        Setting::setValue('order_processing_to_auto_cancel_hours', '72');
        Setting::setValue('order_pickup_allowance_hours', '48');
        Setting::setValue('order_shipped_to_delivery_hours', '168');
        Setting::setValue('order_delivered_to_auto_acceptance_hours', '72');
        Setting::setValue('order_rejected_to_returned_hours', '48');
        Setting::setValue('order_returned_to_auto_acceptance_hours', '48');
        Setting::setValue('order_replacement_to_auto_refund_hours', '48');
        Setting::setValue('order_accepted_to_settlement_eligible_hours', '24');
    }

    public function test_prohibited_words_rule_blocks_forbidden_text(): void
    {
        $rule = new ProhibitedWordsRule();

        $passed = true;
        $fail = function ($message) use (&$passed) {
            $passed = false;
        };

        $rule->validate('description', 'This is a normal description', $fail);
        $this->assertTrue($passed);

        $passed = true;
        $rule->validate('description', 'This is a total SCAM project', $fail);
        $this->assertFalse($passed);
    }

    public function test_escrow_settlement_creation_on_acceptance_with_zero_commission(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        // Create a bank account for the seller
        $seller->bankAccounts()->create([
            'bank_name' => 'First Bank',
            'bank_code' => '011',
            'account_number' => '3012345678',
            'account_name' => $seller->name,
            'is_default' => true,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 100000,
            'total' => 105000, // 100k + 5k escrow fee
            'commission' => 0.00,
            'status' => 'paid',
            'paid_at' => now()->subDay(),
        ]);

        $payment = Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-TEST-001',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 105000,
            'escrow_fee' => 5000,
            'currency' => 'NGN',
            'paid_at' => now()->subDay(),
        ]);

        $escrowService = app(EscrowService::class);

        // Before acceptance, settlement does not exist
        $this->assertNull(Settlement::where('invoice_id', $invoice->id)->first());

        // Create settlement upon acceptance
        $settlement = $escrowService->createSettlementOnAcceptance($invoice);

        $this->assertNotNull($settlement);
        // Net amount is exactly payment amount minus escrow fee (105000 - 5000 = 100000)
        $this->assertEquals(100000.00, (float) $settlement->amount);
        $this->assertEquals('pending', $settlement->status);
        $this->assertNotNull($settlement->eligible_at);

        // Verify seller bank snapshot
        $this->assertIsArray($settlement->bank_details);
        $this->assertEquals('First Bank', $settlement->bank_details['bank_name']);
        $this->assertEquals('3012345678', $settlement->bank_details['account_number']);

        // Check release eligibility: eligible_at is 24h into the future, so not yet eligible
        $this->assertFalse($escrowService->canReleaseSettlement($settlement));

        // Fast forward time past eligible_at
        $settlement->update(['eligible_at' => now()->subMinute()]);
        $this->assertTrue($escrowService->canReleaseSettlement($settlement));

        // When seller payout is frozen, release must be blocked
        $seller->update(['freeze_payout' => true]);
        $this->assertFalse($escrowService->canReleaseSettlement($settlement->fresh()));
    }

    public function test_fulfillment_warning_and_auto_cancellation(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        // Invoice approaching warning cutoff (e.g. 50 hours old)
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-WARN',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 20000,
            'total' => 21000,
            'status' => 'paid',
            'paid_at' => now()->subHours(50),
        ]);

        Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-TEST-WARN',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 21000,
            'escrow_fee' => 1000,
            'currency' => 'NGN',
            'paid_at' => now()->subHours(50),
        ]);

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertNotNull($invoice->auto_cancel_warned_at);

        // Now advance paid_at past auto-cancel threshold (75 hours)
        $invoice->update(['paid_at' => now()->subHours(75)]);

        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);
        $this->assertNotNull($invoice->cancelled_at);

        Notification::assertSentTo($buyer, OrderCancelledNotification::class);
        Notification::assertSentTo($seller, OrderCancelledNotification::class);
    }

    public function test_pickup_allowance_expiration_auto_marks_delivered(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-PICKUP',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'delivery_method' => 'self_pickup',
            'payment_method' => 'platform',
            'subtotal' => 15000,
            'total' => 15500,
            'status' => 'paid',
            'paid_at' => now()->subHours(60),
            'ready_for_pickup_at' => now()->subHours(50), // Exceeded 48h allowance
        ]);

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertNotNull($invoice->delivered_at);
    }

    public function test_delayed_shipment_alerts_administrators(): void
    {
        Notification::fake();

        $adminRole = \App\Models\Role::create(['name' => 'admin', 'slug' => 'admin', 'permissions' => []]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-DELAY',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 50000,
            'total' => 52500,
            'status' => 'paid',
            'paid_at' => now()->subHours(200),
            'shipped_at' => now()->subHours(180), // Exceeded 168h allowance
        ]);

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertNotNull($invoice->admin_followup_notified_at);
        Notification::assertSentTo($admin, \App\Notifications\AdminDelayedShipmentNotification::class);
    }

    public function test_delivered_order_auto_accepts_and_creates_settlement(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-AUTOACCEPT',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 40000,
            'total' => 42000,
            'status' => 'paid',
            'paid_at' => now()->subHours(100),
            'shipped_at' => now()->subHours(90),
            'delivered_at' => now()->subHours(80), // Exceeded 72h auto-acceptance
        ]);

        Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-AUTOACCEPT-01',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 42000,
            'escrow_fee' => 2000,
            'paid_at' => now()->subHours(100),
        ]);

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertEquals('accepted', $invoice->status);
        $this->assertNotNull($invoice->accepted_at);

        // Settlement must have been automatically created
        $settlement = Settlement::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($settlement);
        $this->assertEquals(40000.00, (float) $settlement->amount); // 42000 - 2000
    }

    public function test_unilateral_order_cancellation_within_allowed_hours(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-CANCEL',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 30000,
            'total' => 31500,
            'status' => 'paid',
            'paid_at' => now()->subHours(2), // Well within 24h cancellation window
        ]);

        Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-CANCEL-01',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 31500,
            'escrow_fee' => 1500,
            'paid_at' => now()->subHours(2),
        ]);

        $this->actingAs($buyer);

        \Livewire\Livewire::test(\App\Livewire\Dashboard\Invoices\InvoiceView::class, ['invoice' => $invoice])
            ->set('cancelReason', 'Changed my mind within allowed hours')
            ->call('confirmCancelOrder');

        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);
        $this->assertEquals($buyer->id, $invoice->cancelled_by);
        $this->assertEquals('Changed my mind within allowed hours', $invoice->cancellation_reason);

        // Counterparty (seller) is notified unilaterally
        Notification::assertSentTo($seller, OrderCancelledNotification::class);
    }

    public function test_return_window_expiration_when_buyer_does_not_ship(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-RET-EXP',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 10000,
            'total' => 10500,
            'status' => 'paid',
        ]);

        $return = ReturnRecord::create([
            'invoice_id' => $invoice->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'pending',
        ]);
        $return->timestamps = false;
        $return->created_at = now()->subHours(60);
        $return->save();

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $return->refresh();
        $this->assertEquals('expired', $return->status);
        Notification::assertSentTo($buyer, \App\Notifications\ReturnWindowExpiredNotification::class);
        Notification::assertSentTo($seller, \App\Notifications\ReturnWindowExpiredNotification::class);
    }

    public function test_return_auto_acceptance_triggers_refund_to_buyer(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-RET-ACCEPT',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 25000,
            'total' => 26000,
            'status' => 'paid',
        ]);

        Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-RET-01',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 26000,
            'escrow_fee' => 1000,
            'paid_at' => now()->subDays(5),
        ]);

        $return = ReturnRecord::create([
            'invoice_id' => $invoice->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'received',
            'received_at' => now()->subHours(55), // Exceeded 48h seller inspection window
        ]);

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $return->refresh();
        $this->assertEquals('accepted', $return->status);
        $this->assertNotNull($return->accepted_at);

        $refund = \App\Models\Refund::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals(26000.00, (float) $refund->amount);
    }

    public function test_replacement_timeout_auto_refunds_buyer(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-REP-REFUND',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'platform',
            'subtotal' => 35000,
            'total' => 36500,
            'status' => 'paid',
        ]);

        Payment::create([
            'user_id' => $buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-REP-01',
            'provider' => 'paystack',
            'status' => 'successful',
            'amount' => 36500,
            'escrow_fee' => 1500,
            'paid_at' => now()->subDays(6),
        ]);

        $replacement = Replacement::create([
            'invoice_id' => $invoice->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'pending',
            'sent_at' => null,
        ]);
        $replacement->timestamps = false;
        $replacement->created_at = now()->subHours(55);
        $replacement->save();

        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $replacement->refresh();
        $this->assertEquals('cancelled', $replacement->status);

        $refund = \App\Models\Refund::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals(36500.00, (float) $refund->amount);
    }
}
