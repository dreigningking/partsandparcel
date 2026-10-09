<?php

namespace Tests\Feature;

use App\Jobs\ConfirmPaymentJob;
use App\Jobs\ProcessOrderFulfillmentTimelinesJob;
use App\Jobs\RefundPaymentJob;
use App\Livewire\Dashboard\Invoices\InvoiceView;
use App\Livewire\Marketplace\CheckoutPage;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Refund;
use App\Models\User;
use App\Services\Commercial\CartService;
use App\Services\Commercial\NegotiationService;
use App\Services\Payment\EscrowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ConcurrencyAndInventoryRestorationTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        Country::firstOrCreate(
            ['code' => 'NG'],
            ['name' => 'Nigeria', 'currency' => 'NGN', 'is_default' => true]
        );

        $this->buyer = User::factory()->create([
            'name' => 'Buyer Concurrent',
            'email' => 'buyer_concur@example.com',
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Seller Stock',
            'email' => 'seller_stock@example.com',
        ]);

        $category = Category::firstOrCreate(['slug' => 'processors'], [
            'name' => 'Processors',
        ]);

        $model = DeviceModel::firstOrCreate(['slug' => 'intel-i7-9700k'], [
            'name' => 'Intel Core i7 9700K',
            'category_id' => $category->id,
        ]);

        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'title' => 'Intel Core i7 9700K CPU',
            'price' => 85000.00,
            'quantity' => 1,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
            'condition' => 'used',
            'status' => 'active',
            'allow_shipping' => true,
        ]);
    }

    public function test_checkout_fails_gracefully_when_stock_becomes_unavailable(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        // Simulate concurrent checkout that reserves or takes the last item
        $this->listing->update(['reserved_quantity' => 1]);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'platform')
            ->call('placeOrder');

        $component->assertSee('no longer available');

        // Verify no invoice was created for this buyer
        $this->assertEquals(0, Invoice::where('buyer_id', $this->buyer->id)->count());

        // Verify cart is still intact
        $cart = Cart::where('buyer_id', $this->buyer->id)->where('seller_id', $this->seller->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(1, $cart->items()->count());
    }

    public function test_accept_offer_fails_and_rolls_back_when_listing_quantity_insufficient(): void
    {
        $offer = Offer::create([
            'sender_id' => $this->seller->id,
            'recipient_id' => $this->buyer->id,
            'delivery_method' => 'buyer_responsible',
            'discount' => 0,
            'status' => 'pending',
            'expires_at' => now()->addDays(2),
        ]);

        OfferItem::create([
            'offer_id' => $offer->id,
            'listing_id' => $this->listing->id,
            'description' => $this->listing->title,
            'type' => 'item',
            'quantity' => 2, // Requests 2 when only 1 is available
            'unit_price' => 80000.00,
        ]);

        $service = app(NegotiationService::class);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("no longer available in the requested quantity");

        try {
            $service->acceptOffer($this->buyer, $offer);
        } finally {
            // Verify stock was not modified
            $this->listing->refresh();
            $this->assertEquals(0, $this->listing->reserved_quantity);
            $this->assertEquals(0, $this->listing->sold_quantity);
            // Verify no invoice was created
            $this->assertEquals(0, Invoice::where('offer_id', $offer->id)->count());
            $offer->refresh();
            $this->assertEquals('pending', $offer->status);
        }
    }

    public function test_unpaid_invoice_cancellation_restores_reserved_inventory(): void
    {
        // Issue invoice for 1 unit of listing
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-RESTORE-1',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
            'subtotal' => 85000.00,
            'total' => 85000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Listing::class,
            'itemable_id' => $this->listing->id,
            'type' => 'item',
            'description' => $this->listing->title,
            'quantity' => 1,
            'unit_price' => 85000.00,
            'amount' => 85000.00,
        ]);

        // Simulating the reserved state
        $this->listing->update(['reserved_quantity' => 1]);
        $this->assertEquals(0, $this->listing->availableQuantity());

        $this->actingAs($this->buyer);

        Livewire::test(InvoiceView::class, ['invoiceId' => $invoice->id])
            ->set('cancelReason', 'Changed my mind before payment')
            ->call('confirmCancelOrder');

        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);

        $this->listing->refresh();
        $this->assertEquals(0, $this->listing->reserved_quantity);
        $this->assertEquals(1, $this->listing->availableQuantity());
    }

    public function test_paid_invoice_cancellation_restores_sold_inventory(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-RESTORE-PAID',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
            'subtotal' => 85000.00,
            'total' => 85000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'paid',
            'paid_at' => now(),
            'issued_at' => now(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Listing::class,
            'itemable_id' => $this->listing->id,
            'type' => 'item',
            'description' => $this->listing->title,
            'quantity' => 1,
            'unit_price' => 85000.00,
            'amount' => 85000.00,
        ]);

        // Simulating the sold state
        $this->listing->update(['sold_quantity' => 1]);
        $this->assertEquals(0, $this->listing->availableQuantity());

        $this->actingAs($this->buyer);

        Livewire::test(InvoiceView::class, ['invoiceId' => $invoice->id])
            ->set('cancelReason', 'Seller cannot fulfill order in time')
            ->call('confirmCancelOrder');

        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);

        $this->listing->refresh();
        $this->assertEquals(0, $this->listing->sold_quantity);
        $this->assertEquals(1, $this->listing->availableQuantity());
    }

    public function test_restore_inventory_is_idempotent(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-IDEMPOTENT',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
            'subtotal' => 85000.00,
            'total' => 85000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Listing::class,
            'itemable_id' => $this->listing->id,
            'type' => 'item',
            'description' => $this->listing->title,
            'quantity' => 1,
            'unit_price' => 85000.00,
            'amount' => 85000.00,
        ]);

        $this->listing->update(['reserved_quantity' => 1]);

        // First call restores stock
        $invoice->restoreInventory();
        $this->listing->refresh();
        $this->assertEquals(0, $this->listing->reserved_quantity);

        // Second call must NOT decrement below 0
        $invoice->restoreInventory();
        $this->listing->refresh();
        $this->assertEquals(0, $this->listing->reserved_quantity);
    }

    public function test_confirm_payment_job_is_idempotent_and_thread_safe(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-JOB-LOCK',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
            'subtotal' => 85000.00,
            'total' => 85000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-LOCK-TEST-12345',
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 85000.00,
            'escrow_fee' => 0.00,
            'currency' => 'NGN',
            'metadata' => ['invoice_id' => $invoice->id],
        ]);

        $gatewayData = [
            'status' => 'success',
            'reference' => $payment->reference,
            'amount' => 8500000,
        ];

        // First dispatch
        ConfirmPaymentJob::dispatchSync($payment->reference, 'paystack', $gatewayData);

        $payment->refresh();
        $this->assertEquals('successful', $payment->status);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);

        // Second dispatch (simulating duplicate webhook replay)
        ConfirmPaymentJob::dispatchSync($payment->reference, 'paystack', $gatewayData);

        $payment->refresh();
        $this->assertEquals('successful', $payment->status);
    }

    public function test_paystack_webhook_transfer_failed_updates_payout_safely(): void
    {
        $payout = Payout::create([
            'reference' => 'PO-FAIL-TEST-999',
            'seller_id' => $this->seller->id,
            'amount' => 50000.00,
            'currency' => 'NGN',
            'status' => 'pending',
            'metadata' => ['original' => true],
        ]);

        $payload = [
            'event' => 'transfer.failed',
            'data' => [
                'reference' => 'PO-FAIL-TEST-999',
                'reason' => 'Account number does not exist',
            ],
        ];

        $signature = hash_hmac('sha512', json_encode($payload), config('services.paystack.secret') ?: 'test_paystack_secret');

        $response = $this->postJson(route('webhooks.paystack'), $payload, [
            'x-paystack-signature' => $signature,
        ]);

        $response->assertStatus(200);

        $payout->refresh();
        $this->assertEquals('failed', $payout->status);
        $this->assertArrayHasKey('failure_webhook', $payout->metadata);
        $this->assertEquals('Account number does not exist', $payout->metadata['failure_webhook']['reason']);
    }

    public function test_fulfillment_timeline_auto_cancellation_restores_inventory(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-AUTO-CANCEL',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'seller_responsible',
            'subtotal' => 85000.00,
            'total' => 85000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'paid',
            'paid_at' => now()->subHours(100), // Beyond 72 hour limit
            'issued_at' => now()->subHours(101),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Listing::class,
            'itemable_id' => $this->listing->id,
            'type' => 'item',
            'description' => $this->listing->title,
            'quantity' => 1,
            'unit_price' => 85000.00,
            'amount' => 85000.00,
        ]);

        $this->listing->update(['sold_quantity' => 1]);
        $this->assertEquals(0, $this->listing->availableQuantity());

        // Run the timeline job
        $job = new ProcessOrderFulfillmentTimelinesJob();
        $job->handle(app(EscrowService::class));

        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);
        $this->assertNotNull($invoice->cancelled_at);

        $this->listing->refresh();
        $this->assertEquals(0, $this->listing->sold_quantity);
        $this->assertEquals(1, $this->listing->availableQuantity());
    }
}
