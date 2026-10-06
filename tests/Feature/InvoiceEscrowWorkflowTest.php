<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Invoices\InvoiceView;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Settlement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Issue;
use App\Models\Item;
use App\Models\ReturnRecord;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceEscrowWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Country $country;
    protected DeviceModel $model;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::firstOrCreate(['code' => 'NG'], [
            'name' => 'Nigeria',
            'phone_code' => '+234',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'timezone' => 'Africa/Lagos',
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->buyer = User::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $this->seller = User::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $category = \App\Models\Category::firstOrCreate(['slug' => 'auto-parts'], [
            'name' => 'Auto Parts',
        ]);

        $this->model = DeviceModel::firstOrCreate(['slug' => 'civic-2020'], [
            'category_id' => $category->id,
            'name' => 'Civic 2020',
        ]);

        $this->item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->model->id,
            'name' => 'Original Brake Booster',
            'item_type' => 'part',
            'condition_status' => 'used',
        ]);
    }

    public function test_unpaid_invoice_renders_buyer_and_seller_perspective(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-UNPAID-01',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 250.00,
            'subtotal' => 250.00,
            'currency' => 'NGN',
            'status' => 'issued',
            'payment_method' => 'platform',
            'due_at' => now()->addDays(3),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Item::class,
            'itemable_id' => $this->item->id,
            'type' => 'item',
            'description' => $this->item->name,
            'quantity' => 1,
            'unit_price' => 250.00,
            'amount' => 250.00,
        ]);

        // Buyer perspective
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->assertSee('Payment Required')
            ->assertSee('Pay')
            ->assertDontSee('Shipment Tracking');

        // Seller perspective
        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->assertSee('Payment Pending from Buyer')
            ->assertDontSee('Dispatch & Ship Items');
    }

    public function test_seller_marks_shipment_dispatched_with_evidence(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-DISPATCH-01',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 350.00,
            'subtotal' => 350.00,
            'currency' => 'NGN',
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'platform',
            'due_at' => now()->addDays(3),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Item::class,
            'itemable_id' => $this->item->id,
            'type' => 'item',
            'description' => $this->item->name,
            'quantity' => 1,
            'unit_price' => 350.00,
            'amount' => 350.00,
        ]);

        Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $this->seller->id,
            'gross_amount' => 350.00,
            'commission' => 15.00,
            'net_amount' => 335.00,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->assertSee('Package Ready to Ship')
            ->set('carrierName', 'GIG Logistics')
            ->set('trackingNumber', 'GIG-789012')
            ->set('dispatchNotes', 'Dispatched in reinforced carton box.')
            ->call('markAsShipped');

        $shipment = Shipment::where('sender_id', $this->seller->id)->latest()->first();
        $this->assertNotNull($shipment);
        $this->assertNotEmpty($shipment->slug);
        $this->assertEquals('GIG-789012', $shipment->tracking_number);
        $this->assertEquals('dispatched', $shipment->status);
    }

    public function test_buyer_confirms_package_received_and_completes_deal(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-CONFIRM-01',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 400.00,
            'subtotal' => 400.00,
            'currency' => 'NGN',
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'platform',
            'due_at' => now()->addDays(3),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Item::class,
            'itemable_id' => $this->item->id,
            'type' => 'item',
            'description' => $this->item->name,
            'quantity' => 1,
            'unit_price' => 400.00,
            'amount' => 400.00,
        ]);

        Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $this->seller->id,
            'gross_amount' => 400.00,
            'commission' => 20.00,
            'net_amount' => 380.00,
            'status' => 'pending',
        ]);

        $shipment = Shipment::create([
            'sender_id' => $this->seller->id,
            'receiver_id' => $this->buyer->id,
            'provider_name' => 'DHL Express',
            'tracking_number' => 'DHL-555666',
            'status' => 'dispatched',
            'origin_address_line_1' => '12 Seller Street',
            'origin_city' => 'Lagos',
            'destination_address_line_1' => '34 Buyer Road',
            'destination_city' => 'Lagos',
        ]);

        // Buyer receives package
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->call('confirmPackageReceived');

        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);

        // Buyer inspects and completes deal
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->call('completeDeal');

        $invoice->refresh();
        $this->assertEquals('accepted', $invoice->status);

        // Reviews section is now unlocked
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->assertSee('Review Your Experience')
            ->set('rating', 5)
            ->set('reviewComment', 'Super fast shipping and exact part!')
            ->call('submitReview');

        $this->assertDatabaseHas('listing_reviews', [
            'user_id' => $this->buyer->id,
            'rating' => 5,
        ]);
    }

    public function test_issue_reporting_reverse_return_and_seller_confirmation(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-ISSUE-01',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 600.00,
            'subtotal' => 600.00,
            'currency' => 'NGN',
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'platform',
            'due_at' => now()->addDays(3),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Item::class,
            'itemable_id' => $this->item->id,
            'type' => 'item',
            'description' => $this->item->name,
            'quantity' => 1,
            'unit_price' => 600.00,
            'amount' => 600.00,
        ]);

        Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $this->seller->id,
            'gross_amount' => 600.00,
            'commission' => 30.00,
            'net_amount' => 570.00,
            'status' => 'pending',
        ]);

        $shipment = Shipment::create([
            'sender_id' => $this->seller->id,
            'receiver_id' => $this->buyer->id,
            'provider_name' => 'SpeedPost',
            'tracking_number' => 'SP-12345',
            'status' => 'delivered',
            'delivered_at' => now(),
            'origin_address_line_1' => '12 Seller Street',
            'origin_city' => 'Lagos',
            'destination_address_line_1' => '34 Buyer Road',
            'destination_city' => 'Lagos',
        ]);

        // Buyer reports issue
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->set('issueType', 'damaged')
            ->set('issueDescription', 'Item cracked upon inspection.')
            ->call('submitReportIssue');

        $issue = Issue::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($issue);
        $this->assertEquals('damaged', $issue->type);

        // Seller responds accepting return & replacement
        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->set('activeIssueId', $issue->id)
            ->set('sellerResolutionType', 'replacement')
            ->set('sellerRequiresReturn', true)
            ->set('sellerReturnMethod', 'shipment')
            ->set('sellerResponseNotes', 'Please return it and I will send a new unit.')
            ->call('sellerRespondToIssue');

        $returnRecord = ReturnRecord::where('issue_id', $issue->id)->first();
        $this->assertNotNull($returnRecord);

        $returnShipment = Shipment::find($returnRecord->shipment_id);
        $this->assertNotNull($returnShipment);
        $this->assertNotEmpty($returnShipment->slug);
        $this->assertEquals($this->buyer->id, $returnShipment->sender_id);
        $this->assertEquals($this->seller->id, $returnShipment->receiver_id);

        // Buyer dispatches return
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->set('activeReturnId', $returnRecord->id)
            ->set('returnCarrier', 'DHL Express')
            ->set('returnTrackingNumber', 'DHL-RET-999')
            ->call('buyerConfirmReturnShipped');

        $returnShipment->refresh();
        $this->assertEquals('dispatched', $returnShipment->status);

        // Seller confirms receiving return
        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->call('sellerConfirmReturnReceived', $returnRecord->id);

        $returnShipment->refresh();
        $returnRecord->refresh();
        $this->assertEquals('delivered', $returnShipment->status);
        $this->assertEquals('received', $returnRecord->status);
    }

    public function test_buyer_can_submit_warranty_claim_and_seller_resolves_it(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-WARR-01',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 800.00,
            'subtotal' => 800.00,
            'currency' => 'NGN',
            'status' => 'accepted',
            'paid_at' => now(),
            'accepted_at' => now(),
            'payment_method' => 'platform',
            'due_at' => now()->addDays(3),
        ]);

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Item::class,
            'itemable_id' => $this->item->id,
            'type' => 'item',
            'description' => $this->item->name,
            'quantity' => 1,
            'unit_price' => 800.00,
            'amount' => 800.00,
            'warranty_period_days' => 30,
            'warranty_terms' => 'Covers manufacturing faults within 30 days',
            'warranty_starts_at' => now(),
            'warranty_ends_at' => now()->addDays(30),
        ]);

        Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $this->seller->id,
            'gross_amount' => 800.00,
            'commission' => 40.00,
            'net_amount' => 760.00,
            'status' => 'pending',
        ]);

        // Buyer sees active warranty section and files claim
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->assertSee('Active Inspection & Item Warranty')
            ->assertSee('Claim Warranty')
            ->set('warrantyItemId', $item->id)
            ->set('warrantyReason', 'Part stopped functioning after 5 days of normal driving.')
            ->set('warrantyEvidence', 'Diagnostic report attached.')
            ->call('submitWarrantyClaim');

        $issue = Issue::where('invoice_id', $invoice->id)->where('type', 'warranty_claim')->first();
        $this->assertNotNull($issue);

        // Seller accepts warranty claim
        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice' => $invoice])
            ->set('activeWarrantyIssueId', $issue->id)
            ->set('warrantyResolutionDecision', 'accept')
            ->call('sellerRespondToWarrantyClaim');

        $issue->refresh();
        $this->assertEquals('resolved', $issue->status);
    }
}
