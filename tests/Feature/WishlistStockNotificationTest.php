<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Wishlists as WishlistsComponent;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\ListingRestockedNotification;
use App\Notifications\ListingSoldOutNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class WishlistStockNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected DeviceModel $deviceModel;
    protected Item $item;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'email' => 'buyer@example.com',
            'name' => 'Buyer Jane',
        ]);

        $this->seller = User::factory()->create([
            'email' => 'seller@example.com',
            'name' => 'Seller John',
            'business_name' => 'Pro Repairs',
        ]);

        $category = Category::firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);
        $this->deviceModel = DeviceModel::firstOrCreate(['slug' => 'macbook-pro-14'], [
            'category_id' => $category->id,
            'name' => 'MacBook Pro 14',
        ]);

        $this->item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'name' => 'Retina Display Panel',
            'item_type' => 'part',
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $this->item->id,
            'price' => 120000.00,
            'quantity' => 5,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
            'is_published' => true,
            'is_active' => true,
        ]);
    }

    public function test_wishlisted_user_receives_notification_when_item_sells_out(): void
    {
        Notification::fake();

        // Buyer adds listing to wishlist
        Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        // Seller changes stock quantity to 0 (sold out)
        $this->listing->quantity = 0;
        $this->listing->save();

        Notification::assertSentTo(
            $this->buyer,
            ListingSoldOutNotification::class,
            function (ListingSoldOutNotification $notification) {
                return $notification->listing->id === $this->listing->id
                    && $notification->reason === 'sold_out';
            }
        );

        // Seller should not receive notification for their own listing
        Notification::assertNotSentTo($this->seller, ListingSoldOutNotification::class);
    }

    public function test_wishlisted_user_receives_notification_when_item_is_restocked(): void
    {
        Notification::fake();

        // Listing starts sold out
        $this->listing->update(['quantity' => 0]);

        // Buyer adds sold-out listing to wishlist
        Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        // Seller restocks with 5 units
        $this->listing->quantity = 5;
        $this->listing->save();

        Notification::assertSentTo(
            $this->buyer,
            ListingRestockedNotification::class,
            function (ListingRestockedNotification $notification) {
                return $notification->listing->id === $this->listing->id
                    && $notification->availableStock === 5;
            }
        );
    }

    public function test_sold_out_notification_saves_to_database_correctly(): void
    {
        Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        // Sell out
        $this->listing->quantity = 0;
        $this->listing->save();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->buyer->id,
            'notifiable_type' => $this->buyer->getMorphClass(),
            'type' => ListingSoldOutNotification::class,
        ]);

        $dbNotification = $this->buyer->notifications()->first();
        $this->assertNotNull($dbNotification);
        $this->assertEquals('wishlist', $dbNotification->data['category']);
        $this->assertEquals('listing_sold_out', $dbNotification->data['type']);
        $this->assertEquals($this->listing->id, $dbNotification->data['listing_id']);
    }

    public function test_restocked_notification_saves_to_database_correctly(): void
    {
        $this->listing->update(['quantity' => 0]);

        Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        // Restock
        $this->listing->quantity = 3;
        $this->listing->save();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->buyer->id,
            'notifiable_type' => $this->buyer->getMorphClass(),
            'type' => ListingRestockedNotification::class,
        ]);

        $dbNotification = $this->buyer->notifications()->first();
        $this->assertNotNull($dbNotification);
        $this->assertEquals('wishlist', $dbNotification->data['category']);
        $this->assertEquals('listing_restocked', $dbNotification->data['type']);
        $this->assertEquals(3, $dbNotification->data['available_stock']);
    }

    public function test_wishlist_page_renders_dynamically_with_stock_badges(): void
    {
        $this->actingAs($this->buyer);

        Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        Livewire::test(WishlistsComponent::class)
            ->assertSee('Retina Display Panel')
            ->assertSee('IN STOCK (5)')
            ->assertSee('Buy Now →');

        // Now sell out the listing
        $this->listing->update(['quantity' => 0]);

        Livewire::test(WishlistsComponent::class)
            ->assertSee('SOLD OUT')
            ->assertSee('Restock Alert Active');
    }

    public function test_cannot_move_or_buy_sold_out_wishlist_item(): void
    {
        $this->actingAs($this->buyer);

        $this->listing->update(['quantity' => 0]);

        $wishlist = Wishlist::create([
            'user_id' => $this->buyer->id,
            'listing_id' => $this->listing->id,
        ]);

        // Trying to move sold out item to cart should flash error and NOT delete from wishlist
        Livewire::test(WishlistsComponent::class)
            ->call('moveToCart', $wishlist->id)
            ->assertSee('This item is currently sold out');

        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlist->id,
        ]);

        // Trying to buyNow should also fail gracefully
        Livewire::test(WishlistsComponent::class)
            ->call('buyNow', $wishlist->id)
            ->assertSee('This item is currently sold out');
    }

    public function test_wishlist_filtering_and_search(): void
    {
        $this->actingAs($this->buyer);

        $item2 = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->deviceModel->id,
            'name' => 'Dead Logic Board',
            'item_type' => 'scrap',
            'condition_status' => 'parts_only',
        ]);

        $listing2 = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item2->id,
            'price' => 15000.00,
            'quantity' => 0, // sold out
        ]);

        Wishlist::create(['user_id' => $this->buyer->id, 'listing_id' => $this->listing->id]);
        Wishlist::create(['user_id' => $this->buyer->id, 'listing_id' => $listing2->id]);

        // Test stock filter: in_stock
        Livewire::test(WishlistsComponent::class)
            ->set('stockFilter', 'in_stock')
            ->assertSee('Retina Display Panel')
            ->assertDontSee('Dead Logic Board');

        // Test stock filter: sold_out
        Livewire::test(WishlistsComponent::class)
            ->set('stockFilter', 'sold_out')
            ->assertSee('Dead Logic Board')
            ->assertDontSee('Retina Display Panel');

        // Test search
        Livewire::test(WishlistsComponent::class)
            ->set('search', 'Retina')
            ->assertSee('Retina Display Panel')
            ->assertDontSee('Dead Logic Board');

        // Test clear sold out
        Livewire::test(WishlistsComponent::class)
            ->call('clearSoldOut');

        $this->assertDatabaseMissing('wishlists', [
            'listing_id' => $listing2->id,
        ]);
        $this->assertDatabaseHas('wishlists', [
            'listing_id' => $this->listing->id,
        ]);
    }
}
