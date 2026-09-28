<?php

namespace Tests\Feature;

use App\Livewire\Marketplace\Listings\ListingDetails;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListingDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Category $category;
    protected Brand $brand;
    protected DeviceModel $deviceModel;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'name' => 'Abel Tech',
            'business_name' => 'Abel Tech Parts',
            'is_verified' => true,
        ]);

        $this->buyer = User::factory()->create([
            'name' => 'Buyer John',
        ]);

        $this->category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name' => 'Dell',
            'slug' => 'dell',
            'is_active' => true,
        ]);

        $this->deviceModel = DeviceModel::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Latitude 5420',
            'slug' => 'latitude-5420',
        ]);

        $this->location = Location::create([
            'user_id' => $this->seller->id,
            'label' => 'Main Workshop',
            'address_line_1' => 'Computer Village',
            'city' => 'Ikeja',
            'state' => 'Lagos',
            'country' => 'NG',
            'is_default' => true,
        ]);
    }

    public function test_listing_details_renders_for_complete_device(): void
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'whole',
            'name' => 'Dell Latitude 5420 Laptop Complete',
            'condition_status' => 'used',
            'condition_notes' => 'Minor scratches, tested working perfectly',
            'description' => 'Fast 11th Gen Core i5 laptop with 8GB RAM',
            'status' => 'available',
        ]);

        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 250000,
            'quantity' => 2,
            'status' => 'active',
            'warranty_period_days' => 14,
            'warranty_terms' => '14 days testing warranty',
        ]);

        $response = $this->get(route('listing-details', $listing));
        $response->assertStatus(200);
        $response->assertSee('Dell Latitude 5420 Laptop Complete');
        $response->assertSee('COMPLETE DEVICE');
        $response->assertSee('250,000');
        $response->assertSee('Abel Tech Parts');
        $response->assertSee('14 Days Warranty');
        $response->assertDontSee('Component Status'); // Scrap only tab
    }

    public function test_listing_details_renders_scrap_unit_with_component_matrix(): void
    {
        $scrapItem = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'scrap',
            'name' => 'Dell Latitude 5420 Salvage Unit',
            'condition_status' => 'faulty',
            'condition_notes' => 'Cracked LCD screen, motherboard powers on',
            'description' => 'Great for technicians harvesting internal components',
            'status' => 'available',
        ]);

        // Harvested child components
        Item::create([
            'user_id' => $this->seller->id,
            'parent_id' => $scrapItem->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'part',
            'name' => 'Dell 5420 Motherboard',
            'condition_status' => 'Testing working',
            'condition_notes' => 'Booted to BIOS fine',
            'status' => 'available',
        ]);

        Item::create([
            'user_id' => $this->seller->id,
            'parent_id' => $scrapItem->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'part',
            'name' => 'Original Battery 65Wh',
            'condition_status' => 'Testing working',
            'condition_notes' => 'Healthy charge',
            'status' => 'available',
        ]);

        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $scrapItem->id,
            'location_id' => $this->location->id,
            'price' => 120000,
            'quantity' => 1,
            'status' => 'active',
        ]);

        $response = $this->get(route('listing-details', $listing));
        $response->assertStatus(200);
        $response->assertSee('SCRAP / SALVAGE');
        $response->assertSee('Dell 5420 Motherboard');
        $response->assertSee('Original Battery 65Wh');
        $response->assertSee('Offer on Specific Component');
    }

    public function test_sold_count_calculation_and_reviews_integration(): void
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'part',
            'name' => 'Dell Latitude 5420 Battery',
            'condition_status' => 'new',
            'status' => 'available',
        ]);

        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 30000,
            'quantity' => 10,
            'status' => 'active',
        ]);

        // Paid cart & invoice to simulate 3 sold items
        $cart = Cart::create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => 'completed',
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'listing_id' => $listing->id,
            'quantity' => 3,
            'unit_price' => 30000,
        ]);

        Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'cart_id' => $cart->id,
            'subtotal' => 90000,
            'total' => 90000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Add a customer review
        ListingReview::create([
            'listing_id' => $listing->id,
            'user_id' => $this->buyer->id,
            'rating' => 5,
            'comment' => 'Battery holds charge very well. Tested for 6 hours.',
        ]);

        $component = Livewire::test(ListingDetails::class, ['listing' => $listing]);
        $component->assertSee('3 sold');
        $component->assertSee('Battery holds charge very well');
        $component->assertSee('5.0');
    }

    public function test_negotiable_badge_visibility(): void
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'whole',
            'name' => 'Dell Laptop Negotiable Test',
            'condition_status' => 'working',
            'status' => 'available',
        ]);

        $listingNegotiable = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'is_negotiable' => true,
        ]);

        $response = $this->get(route('listing-details', $listingNegotiable));
        $response->assertSee('Negotiable');

        $listingFixed = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'is_negotiable' => false,
        ]);

        $responseFixed = $this->get(route('listing-details', $listingFixed));
        $responseFixed->assertDontSee('◉ Negotiable');
    }

    public function test_fulfillment_shipping_vs_local_pickup_display(): void
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'whole',
            'name' => 'Dell Laptop Shipping Test',
            'condition_status' => 'working',
            'status' => 'available',
        ]);

        $listingWithShipping = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'allow_shipping' => true,
        ]);

        $responseWithShipping = $this->get(route('listing-details', $listingWithShipping));
        $responseWithShipping->assertSee('Seller delivery / Escrow courier');
        $responseWithShipping->assertDontSee('Local pickup only. Seller delivery / shipping is not offered');

        $listingPickupOnly = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'allow_shipping' => false,
        ]);

        $responsePickupOnly = $this->get(route('listing-details', $listingPickupOnly));
        $responsePickupOnly->assertDontSee('Seller delivery / Escrow courier');
        $responsePickupOnly->assertSee('Local pickup only. Seller delivery / shipping is not offered');
    }

    public function test_make_offer_button_hidden_when_all_negotiations_disabled(): void
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'item_type' => 'whole',
            'name' => 'Dell Laptop Offer Hidden Test',
            'condition_status' => 'working',
            'status' => 'available',
        ]);

        // All false
        $listingLocked = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'is_negotiable' => false,
            'is_warranty_negotiable' => false,
            'allow_shipping' => false,
        ]);

        Livewire::test(ListingDetails::class, ['listing' => $listingLocked])
            ->assertDontSeeHtml('open-make-offer');

        // At least one true (e.g. is_negotiable = true)
        $listingNegotiable = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'location_id' => $this->location->id,
            'price' => 200000,
            'quantity' => 1,
            'status' => 'active',
            'is_negotiable' => true,
            'is_warranty_negotiable' => false,
            'allow_shipping' => false,
        ]);

        Livewire::test(ListingDetails::class, ['listing' => $listingNegotiable])
            ->assertSeeHtml('open-make-offer');
    }
}
