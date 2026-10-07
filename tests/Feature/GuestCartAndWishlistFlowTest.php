<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Wishlists as WishlistsComponent;
use App\Livewire\Marketplace\CartPage;
use App\Livewire\Marketplace\CheckoutPage;
use App\Livewire\Marketplace\Listings\ListingDetails;
use App\Models\Cart;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\Commercial\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class GuestCartAndWishlistFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $seller1;
    protected User $seller2;
    protected Listing $listing1;
    protected Listing $listing2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'buyer_test@example.com',
            'name' => 'Buyer Test',
        ]);

        $this->seller1 = User::factory()->create([
            'email' => 'seller_one@example.com',
            'business_name' => 'Tech Hub One',
            'name' => 'Seller One',
        ]);

        $this->seller2 = User::factory()->create([
            'email' => 'seller_two@example.com',
            'business_name' => 'Gadget Store Two',
            'name' => 'Seller Two',
        ]);

        $category = Category::firstOrCreate(['slug' => 'test-category'], ['name' => 'Test Category']);
        $model = DeviceModel::firstOrCreate(['slug' => 'test-model'], [
            'category_id' => $category->id,
            'name' => 'Test Model',
        ]);

        $item1 = Item::firstOrCreate(['serial_number' => 'SN-GUEST-1'], [
            'name' => 'Listing One Test',
            'user_id' => $this->seller1->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $item2 = Item::firstOrCreate(['serial_number' => 'SN-GUEST-2'], [
            'name' => 'Listing Two Test',
            'user_id' => $this->seller2->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $this->listing1 = Listing::firstOrCreate(['slug' => 'listing-one-test'], [
            'user_id' => $this->seller1->id,
            'item_id' => $item1->id,
            'title' => 'Listing One Test',
            'price' => 150000.00,
            'quantity' => 1,
            'condition' => 'used',
            'status' => 'active',
        ]);

        $this->listing2 = Listing::firstOrCreate(['slug' => 'listing-two-test'], [
            'user_id' => $this->seller2->id,
            'item_id' => $item2->id,
            'title' => 'Listing Two Test',
            'price' => 75000.00,
            'quantity' => 1,
            'condition' => 'used',
            'status' => 'active',
        ]);
    }

    public function test_carts_table_does_not_have_expires_at_column(): void
    {
        $this->assertFalse(Schema::hasColumn('carts', 'expires_at'));
    }

    public function test_guest_can_add_item_to_cart_from_listing_details(): void
    {
        Livewire::test(ListingDetails::class, ['listing' => $this->listing1])
            ->call('addToCart')
            ->assertDispatched('cart-updated')
            ->assertSee('Item added to your cart!');

        $cartService = app(CartService::class);
        $this->assertEquals(1, $cartService->getCartCount(null));

        $grouped = $cartService->getGroupedCarts(null);
        $this->assertCount(1, $grouped);
        $this->assertEquals((string) $this->seller1->id, $grouped[0]['id']);
        $this->assertEquals('Listing One Test', $grouped[0]['items'][0]['title']);
    }

    public function test_guest_items_are_separated_by_seller_id(): void
    {
        $cartService = app(CartService::class);

        // Guest adds from two different sellers
        $cartService->addToCart(null, $this->listing1, 1);
        $cartService->addToCart(null, $this->listing2, 2);

        $grouped = $cartService->getGroupedCarts(null);
        $this->assertCount(2, $grouped);

        $sellerIds = collect($grouped)->pluck('id')->toArray();
        $this->assertContains((string) $this->seller1->id, $sellerIds);
        $this->assertContains((string) $this->seller2->id, $sellerIds);
    }

    public function test_guest_cannot_checkout_and_is_redirected_to_login(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart(null, $this->listing1, 1);

        // Clicking proceedToCheckout on CartPage redirects guest
        Livewire::test(CartPage::class)
            ->call('proceedToCheckout', $this->seller1->id)
            ->assertRedirect(route('login'));

        // Navigating directly to checkout also redirects guest
        $response = $this->get(route('checkout', ['seller' => $this->seller1->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cart_merges_to_db_upon_login(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart(null, $this->listing1, 2);

        $this->assertDatabaseMissing('carts', [
            'buyer_id' => $this->user->id,
        ]);

        // When user logs in and loads cart/grouped carts
        $grouped = $cartService->getGroupedCarts($this->user);

        $this->assertDatabaseHas('carts', [
            'buyer_id' => $this->user->id,
            'seller_id' => $this->seller1->id,
        ]);

        $this->assertCount(1, $grouped);
        $this->assertEquals(2, $grouped[0]['items'][0]['quantity']);
    }

    public function test_only_logged_in_user_can_save_to_wishlist(): void
    {
        // 1. Guest attempt
        Livewire::test(ListingDetails::class, ['listing' => $this->listing1])
            ->call('toggleWishlist')
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('wishlists', [
            'listing_id' => $this->listing1->id,
        ]);

        // 2. Authenticated user attempt
        $this->actingAs($this->user);

        Livewire::test(ListingDetails::class, ['listing' => $this->listing1])
            ->call('toggleWishlist')
            ->assertSee('Item saved to your wishlist!');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing1->id,
        ]);
    }

    public function test_wishlist_dashboard_actions(): void
    {
        $this->actingAs($this->user);

        $wishlistItem = Wishlist::create([
            'user_id' => $this->user->id,
            'listing_id' => $this->listing1->id,
        ]);

        // Move to cart action
        Livewire::test(WishlistsComponent::class)
            ->call('moveToCart', $wishlistItem->id)
            ->assertSee('Item moved to your cart!');

        $this->assertDatabaseMissing('wishlists', [
            'id' => $wishlistItem->id,
        ]);

        $this->assertDatabaseHas('carts', [
            'buyer_id' => $this->user->id,
            'seller_id' => $this->seller1->id,
        ]);
    }

    public function test_checkout_escrow_vs_direct_seller(): void
    {
        $this->actingAs($this->user);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->user, $this->listing1, 1);

        // 1. Direct Seller Payment Checkout
        Livewire::test(CheckoutPage::class, ['seller' => $this->seller1->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'direct_seller')
            ->call('placeOrder')
            ->assertRedirect(route('invoices'));

        $this->assertDatabaseHas('invoices', [
            'buyer_id' => $this->user->id,
            'seller_id' => $this->seller1->id,
            'payment_method' => 'direct',
            'delivery_method' => 'buyer_responsible',
        ]);
    }

    public function test_cart_counter_reacts_to_cart_updated_in_both_desktop_and_mobile_variants(): void
    {
        // Initial state with empty cart
        $desktopCounter = Livewire::test(\App\Livewire\Components\Header\CartCounter::class, ['variant' => 'desktop'])
            ->assertSet('cartCount', 0)
            ->assertDontSee('bg-pp-600 text-white text-[10px]');

        $mobileCounter = Livewire::test(\App\Livewire\Components\Header\CartCounter::class, ['variant' => 'mobile'])
            ->assertSet('cartCount', 0)
            ->assertSee('Cart');

        // Add item to cart
        app(CartService::class)->addToCart(null, $this->listing1, 2);

        // Dispatch cart-updated event
        $desktopCounter->dispatch('cart-updated')
            ->assertSet('cartCount', 2)
            ->assertSee('2');

        $mobileCounter->dispatch('cart-updated')
            ->assertSet('cartCount', 2)
            ->assertSee('2');
    }

    public function test_adding_item_to_cart_from_listing_details_dispatches_event_and_updates_mobile_cart_counter(): void
    {
        Livewire::test(ListingDetails::class, ['listing' => $this->listing1])
            ->call('addToCart')
            ->assertDispatched('cart-updated');

        Livewire::test(\App\Livewire\Components\Header\CartCounter::class, ['variant' => 'mobile'])
            ->assertSet('cartCount', 1)
            ->assertSee('1');
    }

    public function test_listing_availability_is_determined_by_published_active_approved_moderation_and_positive_quantity(): void
    {
        $listing = $this->listing1;
        $listing->update([
            'is_published' => true,
            'is_active' => true,
            'quantity' => 5,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
        ]);

        // 1. Pending moderation: not available
        \App\Models\Moderation::updateOrCreate(
            ['moderatable_type' => $listing->getMorphClass(), 'moderatable_id' => $listing->id],
            ['status' => 'pending', 'action' => 'created']
        );
        $listing = $listing->fresh();
        $this->assertFalse($listing->isAvailable());

        // 2. Approved moderation: available
        \App\Models\Moderation::where('moderatable_type', $listing->getMorphClass())
            ->where('moderatable_id', $listing->id)
            ->update(['status' => 'approved']);
        $listing = $listing->fresh();
        $this->assertTrue($listing->isAvailable());

        // 3. Sold out: not available
        $listing->update(['sold_quantity' => 5]);
        $this->assertFalse($listing->isAvailable());

        // 4. Inactive: not available
        $listing->update(['sold_quantity' => 0, 'is_active' => false]);
        $this->assertFalse($listing->isAvailable());

        // 5. Unpublished: not available
        $listing->update(['is_active' => true, 'is_published' => false]);
        $this->assertFalse($listing->isAvailable());
    }

    public function test_scrap_listing_with_children_renders_listing_details_without_unknown_status_column_error(): void
    {
        $scrapItem = Item::create([
            'user_id' => $this->seller1->id,
            'model_id' => $this->listing1->item->model_id,
            'item_type' => 'scrap',
            'name' => 'Dell Salvage Unit',
            'condition_status' => 'faulty',
        ]);

        $child1 = Item::create([
            'user_id' => $this->seller1->id,
            'parent_id' => $scrapItem->id,
            'model_id' => $scrapItem->model_id,
            'item_type' => 'part',
            'name' => 'Motherboard Part',
            'condition_status' => 'working',
        ]);

        $scrapListing = Listing::create([
            'user_id' => $this->seller1->id,
            'item_id' => $scrapItem->id,
            'price' => 75000,
            'quantity' => 1,
            'is_published' => true,
            'is_active' => true,
        ]);

        \App\Models\Moderation::updateOrCreate(
            ['moderatable_type' => $scrapListing->getMorphClass(), 'moderatable_id' => $scrapListing->id],
            ['status' => 'approved', 'action' => 'created']
        );

        $test = Livewire::test(ListingDetails::class, ['listing' => $scrapListing]);
        $test->assertStatus(200);
        $test->assertSee('Dell Salvage Unit');
        $test->assertSee('Motherboard Part');
        $test->assertSee('Offer on Specific Component');
    }
}
