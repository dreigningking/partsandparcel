<?php

namespace Tests\Feature;

use App\Livewire\Components\Promotions\BlogPagePromotion;
use App\Livewire\Components\Promotions\HomePagePromotion;
use App\Livewire\Marketplace\Listings\ListingDetails;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Moderation;
use App\Models\Promotion;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Models\ViewedEntity;
use App\Services\Promotion\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeaturedPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;
    protected State $lagosState;
    protected State $abujaState;
    protected Category $category;
    protected DeviceModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'permissions' => ['*' => true],
        ]);

        $this->country = Country::create([
            'name' => 'Nigeria',
            'code' => 'NG',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->lagosState = State::create([
            'country_id' => $this->country->id,
            'name' => 'Lagos',
            'code' => 'LA',
            'is_active' => true,
        ]);

        $this->abujaState = State::create([
            'country_id' => $this->country->id,
            'name' => 'Abuja',
            'code' => 'ABJ',
            'is_active' => true,
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'phones'], ['name' => 'Phones', 'is_listing' => true]);
        $this->model = DeviceModel::firstOrCreate(['slug' => 'iphone-14'], ['name' => 'iPhone 14', 'category_id' => $this->category->id]);

        \App\Models\Setting::setValue('auto_approve_listings', true);
    }

    protected function createAvailableListing(User $seller, ?State $state = null, string $title = 'Test Listing', int $qty = 5): Listing
    {
        $loc = null;
        if ($state) {
            $loc = Location::create([
                'user_id' => $seller->id,
                'label' => 'Warehouse',
                'address_line_1' => '123 Market Road',
                'country_id' => $this->country->id,
                'state_id' => $state->id,
                'city' => $state->name . ' City',
                'is_default' => true,
            ]);
        }

        $item = Item::create([
            'user_id' => $seller->id,
            'location_id' => $loc?->id,
            'model_id' => $this->model->id,
            'name' => $title,
            'item_type' => 'whole',
            'condition_status' => 'used',
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'item_id' => $item->id,
            'slug' => str()->slug($title) . '-' . uniqid(),
            'price' => 150000,
            'quantity' => $qty,
            'is_published' => true,
            'is_active' => true,
        ]);

        Moderation::where('moderatable_id', $listing->id)
            ->whereIn('moderatable_type', [$listing->getMorphClass(), Listing::class])
            ->update(['status' => 'approved']);

        return $listing;
    }

    public function test_featured_listings_prioritizes_state_over_country_over_popular_listings(): void
    {
        $sellerLagos = User::factory()->create(['country_id' => $this->country->id]);
        $sellerAbuja = User::factory()->create(['country_id' => $this->country->id]);
        $sellerGeneral = User::factory()->create(['country_id' => $this->country->id]);

        $listingLagos = $this->createAvailableListing($sellerLagos, $this->lagosState, 'Lagos iPhone');
        $listingAbuja = $this->createAvailableListing($sellerAbuja, $this->abujaState, 'Abuja iPhone');
        $listingPopular = $this->createAvailableListing($sellerGeneral, null, 'Popular iPhone');

        // Promotion in Lagos
        Promotion::create([
            'user_id' => $sellerLagos->id,
            'listing_id' => $listingLagos->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 10,
            'status' => 'active',
        ]);

        // Promotion in Abuja (Same country, different state)
        Promotion::create([
            'user_id' => $sellerAbuja->id,
            'listing_id' => $listingAbuja->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 10,
            'status' => 'active',
        ]);

        // Log views for Popular iPhone in ViewedEntity
        for ($i = 0; $i < 5; $i++) {
            ViewedEntity::create([
                'ip_address' => "10.0.0.{$i}",
                'user_agent' => 'TestBrowser',
                'device_type' => 'desktop',
                'viewable_id' => $listingPopular->id,
                'viewable_type' => $listingPopular->getMorphClass(),
            ]);
        }

        // Set session location to Lagos
        session()->put('current_location', [
            'country_id' => $this->country->id,
            'country_name' => 'Nigeria',
            'state' => 'Lagos',
        ]);

        $service = app(PromotionService::class);
        $results = $service->getFeaturedListings(limit: 3);

        $this->assertCount(3, $results);
        // Lagos state promotion is 1st
        $this->assertEquals($listingLagos->id, $results[0]->id);
        // Abuja country promotion is 2nd (backfill from country)
        $this->assertEquals($listingAbuja->id, $results[1]->id);
        // Popular listing is 3rd (backfill from ViewedEntity)
        $this->assertEquals($listingPopular->id, $results[2]->id);
    }

    public function test_promotions_are_ranked_by_lowest_achievement_percentage_first(): void
    {
        $seller = User::factory()->create(['country_id' => $this->country->id]);

        $listingHighAchieved = $this->createAvailableListing($seller, $this->lagosState, 'High Achieved Listing');
        $listingLowAchieved = $this->createAvailableListing($seller, $this->lagosState, 'Low Achieved Listing');

        // Promo A: 80% achieved (target 100, achieved 80)
        Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $listingHighAchieved->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 80,
            'status' => 'active',
        ]);

        // Promo B: 10% achieved (target 100, achieved 10) -> lowest achievement!
        Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $listingLowAchieved->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 10,
            'status' => 'active',
        ]);

        session()->put('current_location', [
            'country_id' => $this->country->id,
            'state' => 'Lagos',
        ]);

        $service = app(PromotionService::class);
        $results = $service->getFeaturedListings(limit: 2);

        $this->assertCount(2, $results);
        $this->assertEquals($listingLowAchieved->id, $results[0]->id);
        $this->assertEquals($listingHighAchieved->id, $results[1]->id);
    }

    public function test_unavailable_listings_are_excluded(): void
    {
        $seller = User::factory()->create(['country_id' => $this->country->id]);

        // Listing with stock 0
        $outOfStock = $this->createAvailableListing($seller, $this->lagosState, 'Out of Stock', 0);
        Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $outOfStock->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 0,
            'status' => 'active',
        ]);

        // Inactive listing
        $inactive = $this->createAvailableListing($seller, $this->lagosState, 'Inactive', 5);
        $inactive->update(['is_active' => false]);
        Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $inactive->id,
            'type' => 'views',
            'target_count' => 100,
            'achieved_count' => 0,
            'status' => 'active',
        ]);

        session()->put('current_location', [
            'country_id' => $this->country->id,
            'state' => 'Lagos',
        ]);

        $service = app(PromotionService::class);
        $results = $service->getFeaturedListings(limit: 2);

        $this->assertFalse($results->contains('id', $outOfStock->id));
        $this->assertFalse($results->contains('id', $inactive->id));
    }

    public function test_home_page_promotion_increments_views_promotions_deduplicated(): void
    {
        $seller = User::factory()->create(['country_id' => $this->country->id]);
        $listing = $this->createAvailableListing($seller, $this->lagosState, 'Promo View Listing');

        $promo = Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $listing->id,
            'type' => 'views',
            'target_count' => 5,
            'achieved_count' => 0,
            'status' => 'active',
        ]);

        session()->put('current_location', [
            'country_id' => $this->country->id,
            'state' => 'Lagos',
        ]);

        // 1st load of HomePagePromotion
        Livewire::test(HomePagePromotion::class)
            ->call('loadFeaturedListings')
            ->assertSet('isLoaded', true);

        $promo->refresh();
        $this->assertEquals(1, $promo->achieved_count);
        $this->assertDatabaseHas('viewed_entities', [
            'viewable_id' => $listing->id,
            'viewable_type' => $listing->getMorphClass(),
        ]);

        // 2nd load with same session/IP should NOT increment
        Livewire::test(HomePagePromotion::class)
            ->call('loadFeaturedListings');

        $promo->refresh();
        $this->assertEquals(1, $promo->achieved_count);
    }

    public function test_listing_details_increments_promoted_listing_and_deduplicates(): void
    {
        $seller = User::factory()->create(['country_id' => $this->country->id]);
        $listing = $this->createAvailableListing($seller, $this->lagosState, 'Click Promo Listing');

        $promo = Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $listing->id,
            'type' => 'clicks',
            'target_count' => 2,
            'achieved_count' => 0,
            'status' => 'active',
        ]);

        // 1st visit
        Livewire::test(ListingDetails::class, ['listing' => $listing]);

        $promo->refresh();
        $this->assertEquals(1, $promo->achieved_count);
        $this->assertEquals('active', $promo->status);

        // 2nd visit from same visitor should NOT increment
        Livewire::test(ListingDetails::class, ['listing' => $listing]);

        $promo->refresh();
        $this->assertEquals(1, $promo->achieved_count);

        // Different user visit increments and completes target
        $otherUser = User::factory()->create(['country_id' => $this->country->id]);
        Livewire::actingAs($otherUser)
            ->test(ListingDetails::class, ['listing' => $listing]);

        $promo->refresh();
        $this->assertEquals(2, $promo->achieved_count);
        $this->assertEquals('completed', $promo->status);
    }

    public function test_blog_page_promotion_loads_single_featured_listing(): void
    {
        $seller = User::factory()->create(['country_id' => $this->country->id]);
        $listing = $this->createAvailableListing($seller, $this->lagosState, 'Blog Promo Listing');

        Promotion::create([
            'user_id' => $seller->id,
            'listing_id' => $listing->id,
            'type' => 'views',
            'target_count' => 10,
            'achieved_count' => 0,
            'status' => 'active',
        ]);

        session()->put('current_location', [
            'country_id' => $this->country->id,
            'state' => 'Lagos',
        ]);

        Livewire::test(BlogPagePromotion::class)
            ->call('loadFeaturedListing')
            ->assertSet('isLoaded', true)
            ->assertSee('Blog Promo Listing');
    }
}
