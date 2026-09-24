<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use App\Services\Commercial\CartService;
use App\Services\Commercial\NegotiationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitCartAndOffersTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller1;
    protected User $seller2;
    protected Listing $listing1;
    protected Listing $listing2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::firstOrCreate(['email' => 'cart_buyer@example.com'], [
            'name' => 'Cart Buyer',
            'password' => bcrypt('password123'),
        ]);

        $this->seller1 = User::firstOrCreate(['email' => 'seller_adam@example.com'], [
            'name' => 'Adam Computers',
            'password' => bcrypt('password123'),
        ]);

        $this->seller2 = User::firstOrCreate(['email' => 'seller_seth@example.com'], [
            'name' => 'Seth Electronics',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::firstOrCreate(['slug' => 'laptops-test'], [
            'name' => 'Laptops Test',
        ]);

        $deviceModel = DeviceModel::firstOrCreate(['slug' => 'hp-elitebook-840-g5'], [
            'category_id' => $category->id,
            'name' => 'HP EliteBook 840 G5',
        ]);

        $item1 = Item::firstOrCreate(['serial_number' => 'SN-TEST-840-1'], [
            'user_id' => $this->seller1->id,
            'model_id' => $deviceModel->id,
            'condition_status' => 'working',
        ]);

        $item2 = Item::firstOrCreate(['serial_number' => 'SN-TEST-840-2'], [
            'user_id' => $this->seller2->id,
            'model_id' => $deviceModel->id,
            'condition_status' => 'working',
        ]);

        $this->listing1 = Listing::firstOrCreate(['slug' => 'hp-elitebook-840-g5-test'], [
            'user_id' => $this->seller1->id,
            'assetable_id' => $item1->id,
            'assetable_type' => Item::class,
            'title' => 'HP EliteBook 840 G5 Test',
            'price' => 250000.00,
            'quantity' => 1,
            'condition' => 'used',
            'status' => 'active',
        ]);

        $this->listing2 = Listing::firstOrCreate(['slug' => 'hp-battery-840-test'], [
            'user_id' => $this->seller2->id,
            'assetable_id' => $item2->id,
            'assetable_type' => Item::class,
            'title' => 'HP Battery 840 Test',
            'price' => 25000.00,
            'quantity' => 2,
            'condition' => 'used',
            'status' => 'active',
        ]);
    }

    public function test_cart_items_are_grouped_by_seller(): void
    {
        $cartService = app(CartService::class);

        // Add items from two different sellers
        $cartService->addToCart($this->buyer, $this->listing1, 1);
        $cartService->addToCart($this->buyer, $this->listing2, 2);

        $grouped = $cartService->getGroupedCarts($this->buyer);

        $this->assertCount(2, $grouped);

        $sellerIds = collect($grouped)->pluck('id')->toArray();
        $this->assertContains((string) $this->seller1->id, $sellerIds);
        $this->assertContains((string) $this->seller2->id, $sellerIds);

        $adamCart = collect($grouped)->firstWhere('id', (string) $this->seller1->id);
        $this->assertEquals(250000.00, $adamCart['items'][0]['price']);
        $this->assertEquals(1, $adamCart['items'][0]['quantity']);

        $sethCart = collect($grouped)->firstWhere('id', (string) $this->seller2->id);
        $this->assertEquals(25000.00, $sethCart['items'][0]['price']);
        $this->assertEquals(2, $sethCart['items'][0]['quantity']);
    }

    public function test_buyer_can_submit_offer_from_cart(): void
    {
        $cartService = app(CartService::class);
        $negotiationService = app(NegotiationService::class);

        $cartService->addToCart($this->buyer, $this->listing1, 1);
        $cart = Cart::where('buyer_id', $this->buyer->id)->where('seller_id', $this->seller1->id)->first();

        $offer = $negotiationService->createOfferFromCart($this->buyer, $this->seller1->id, [
            'delivery_method' => 'seller_delivery',
            'discount' => 10000.00,
            'terms' => 'Please include RAM installation',
            'warranty_days' => 14,
            'warranty_terms' => '14-day replacement warranty',
            'request_repair' => true,
            'repair_service_type' => 'RAM Upgrade',
            'repair_price' => 5000.00,
        ]);

        $this->assertEquals($this->buyer->id, $offer->sender_id);
        $this->assertEquals($this->seller1->id, $offer->recipient_id);
        $this->assertEquals($cart->id, $offer->cart_id);
        $this->assertEquals('seller_responsible', $offer->delivery_method);
        $this->assertEquals(10000.00, (float) $offer->discount);

        // OfferItems: listing item + repair service
        $this->assertCount(2, $offer->items);
        $this->assertTrue($offer->items->contains('description', 'RAM Upgrade'));
        $this->assertEquals(255000.00, $offer->subtotal()); // 250k item + 5k repair
        $this->assertEquals(245000.00, $offer->total()); // 255k - 10k discount
    }

    public function test_seller_can_submit_counter_offer(): void
    {
        $negotiationService = app(NegotiationService::class);

        // Buyer creates initial offer
        $offer1 = $negotiationService->createOfferFromCart($this->buyer, $this->seller1->id, [
            'delivery_method' => 'pickup',
            'discount' => 20000.00,
            'custom_items' => [
                [
                    'listing_id' => $this->listing1->id,
                    'description' => $this->listing1->title,
                    'quantity' => 1,
                    'unit_price' => 250000.00,
                ]
            ],
        ]);

        // Seller counters with lower discount (5,000 NGN instead of 20,000 NGN)
        $counterOffer = $negotiationService->submitCounterOffer($this->seller1, $offer1, [
            'discount' => 5000.00,
            'terms' => 'I can only discount ₦5,000 for this clean board.',
            'warranty_days' => 30,
            'warranty_terms' => '30-day extended testing warranty',
        ]);

        $this->assertEquals($this->seller1->id, $counterOffer->sender_id);
        $this->assertEquals($this->buyer->id, $counterOffer->recipient_id);
        $this->assertEquals($offer1->id, $counterOffer->parent_id);
        $this->assertEquals(5000.00, (float) $counterOffer->discount);
        $this->assertEquals('countered', $offer1->fresh()->status);
        $this->assertEquals('pending', $counterOffer->status);
    }

    public function test_accepting_offer_generates_invoice_with_warranties_and_clears_cart(): void
    {
        $cartService = app(CartService::class);
        $negotiationService = app(NegotiationService::class);

        $cartService->addToCart($this->buyer, $this->listing1, 1);

        $offer = $negotiationService->createOfferFromCart($this->buyer, $this->seller1->id, [
            'delivery_method' => 'pickup',
            'discount' => 5000.00,
            'terms' => 'Agreed price',
            'warranty_days' => 14,
            'warranty_terms' => '14-day warranty',
        ]);

        // Seller accepts offer
        $invoice = $negotiationService->acceptOffer($this->seller1, $offer);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('accepted', $offer->fresh()->status);
        $this->assertEquals('issued', $invoice->status);
        $this->assertEquals($this->buyer->id, $invoice->buyer_id);
        $this->assertEquals($this->seller1->id, $invoice->seller_id);
        $this->assertEquals(250000.00, (float) $invoice->subtotal);
        $this->assertEquals(5000.00, (float) $invoice->discount);
        $this->assertEquals(245000.00, (float) $invoice->total);

        // Verify invoice items
        $this->assertCount(1, $invoice->items);
        $invoiceItem = $invoice->items->first();
        $this->assertEquals(14, $invoiceItem->warranty_period_days);
        $this->assertEquals('14-day warranty', $invoiceItem->warranty_terms);

        // Buyer cart for this seller cleared
        $this->assertDatabaseMissing('carts', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller1->id,
        ]);
    }

    public function test_checkout_creates_invoice_and_clears_seller_cart(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing1, 1);

        $this->actingAs($this->buyer);

        $cart = Cart::where('buyer_id', $this->buyer->id)->where('seller_id', $this->seller1->id)->first();
        $this->assertNotNull($cart);

        // Call Livewire Checkout placeOrder
        \Livewire\Livewire::test(\App\Livewire\Marketplace\CheckoutPage::class, ['seller' => $this->seller1->id])
            ->set('deliveryMethod', 'seller_delivery')
            ->set('paymentMethod', 'escrow')
            ->call('placeOrder')
            ->assertRedirect(route('invoices'));

        // Check invoice was generated
        $this->assertDatabaseHas('invoices', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller1->id,
            'delivery_method' => 'seller_responsible',
            'payment_method' => 'platform',
            'status' => 'issued',
        ]);

        // Cart items for this seller cleared
        $this->assertDatabaseMissing('carts', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller1->id,
        ]);
    }

    public function test_make_offer_livewire_component_creates_offer_from_cart(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing1, 1);

        $this->actingAs($this->buyer);

        \Livewire\Livewire::test(\App\Livewire\Components\Offers\MakeOffer::class)
            ->dispatch('open-make-offer', ['seller_id' => $this->seller1->id, 'seller_name' => 'Adam Computers'])
            ->assertSet('isOpen', true)
            ->assertSet('proposedPrice', '250000')
            ->set('proposedPrice', '240000')
            ->set('deliveryMode', 'seller_delivery')
            ->set('warrantyDays', 14)
            ->call('submitPackageOffer')
            ->assertRedirect(route('offers'));

        $this->assertDatabaseHas('offers', [
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller1->id,
            'delivery_method' => 'seller_responsible',
            'discount' => 10000.00,
            'status' => 'pending',
        ]);
    }

    public function test_offer_view_livewire_component_counter_and_accept_lifecycle(): void
    {
        $negotiationService = app(NegotiationService::class);

        // Buyer submits offer
        $initialOffer = $negotiationService->createOfferFromCart($this->buyer, $this->seller1->id, [
            'delivery_method' => 'pickup',
            'discount' => 20000.00,
            'custom_items' => [
                [
                    'listing_id' => $this->listing1->id,
                    'description' => $this->listing1->title,
                    'quantity' => 1,
                    'unit_price' => 250000.00,
                    'warranty_days' => 14,
                ]
            ],
        ]);

        // Seller logs in and views offer, then submits counter-offer
        $this->actingAs($this->seller1);

        \Livewire\Livewire::test(\App\Livewire\Dashboard\Offers\OfferView::class, ['offer_id' => 'OFF-' . $initialOffer->id])
            ->set('counterPrice', 245000)
            ->set('counterDiscount', 5000)
            ->set('warrantyPeriod', 30)
            ->set('counterNotes', 'Best I can do is 245k with 30 days warranty')
            ->call('submitCounterOffer')
            ->assertRedirect(route('offers.view', ['offer_id' => 'OFF-' . ($initialOffer->id + 1)]));

        $counterOffer = Offer::where('parent_id', $initialOffer->id)->first();
        $this->assertNotNull($counterOffer);
        $this->assertEquals(5000.00, (float) $counterOffer->discount);

        // Buyer logs in and accepts the counter-offer
        $this->actingAs($this->buyer);

        \Livewire\Livewire::test(\App\Livewire\Dashboard\Offers\OfferView::class, ['offer_id' => 'OFF-' . $counterOffer->id])
            ->call('acceptOffer')
            ->assertRedirect(route('invoices'));

        $this->assertEquals('accepted', $counterOffer->fresh()->status);
        $this->assertDatabaseHas('invoices', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller1->id,
            'offer_id' => $counterOffer->id,
            'status' => 'issued',
        ]);
    }
}
