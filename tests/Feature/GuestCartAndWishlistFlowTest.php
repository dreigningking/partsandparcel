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
}
