<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\AdminCountries;
use App\Livewire\Dashboard\Inventory\ListingView;
use App\Livewire\Marketplace\Listings\ListingDetails;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCountriesPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Country $country;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'permissions' => ['*' => true],
        ]);

        $this->country = Country::create([
            'name' => 'Nigeria',
            'code' => 'NG',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'views' => 0.0050,
            'clicks' => 20.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'country_id' => $this->country->id,
        ]);

        $this->seller = User::factory()->create([
            'country_id' => $this->country->id,
            'business_name' => 'Seller Garage',
        ]);

        $category = Category::firstOrCreate(['slug' => 'phones'], ['name' => 'Phones', 'is_listing' => true]);
        $model = DeviceModel::firstOrCreate(['slug' => 'iphone-14'], ['name' => 'iPhone 14', 'category_id' => $category->id]);
        $item = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'iPhone 14 Screen',
            'item_type' => 'part',
            'condition_status' => 'new',
            'model_id' => $model->id,
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'slug' => 'iphone-14-screen-' . uniqid(),
            'price' => 50000,
            'quantity' => 5,
            'is_published' => true,
            'is_active' => true,
        ]);
    }

    public function test_admin_countries_table_renders_cost_per_view_and_click(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminCountries::class)
            ->assertSee('Countries & Regions')
            ->assertSee('Cost / View')
            ->assertSee('Cost / Click')
            ->assertSee('0.0050')
            ->assertSee('20.00');
    }

    public function test_admin_can_update_country_views_and_clicks(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminCountries::class)
            ->call('openEdit', $this->country->id)
            ->assertSet('views', '0.0050')
            ->assertSet('clicks', '20.00')
            ->set('views', '0.0085')
            ->set('clicks', '35.50')
            ->call('saveCountry', app(\App\Services\Location\GeographyService::class));

        $this->country->refresh();
        $this->assertEquals('0.0085', (string) $this->country->views);
        $this->assertEquals('35.50', (string) $this->country->clicks);
    }

    public function test_seller_listing_view_uses_registered_country_promotion_prices(): void
    {
        Livewire::actingAs($this->seller)
            ->test(ListingView::class, ['listing' => $this->listing])
            ->call('setActiveTab', 'promotions')
            ->assertSee('Promotional Clicks (PPC)')
            ->assertSee('₦20.00 / click')
            ->assertSee('Promotional Impressions (PPI)')
            ->assertSee('₦0.0050 / view');
    }

    public function test_marketplace_listing_details_shows_owner_promotion_banner_with_country_rates(): void
    {
        // When logged in as the owner
        Livewire::actingAs($this->seller)
            ->test(ListingDetails::class, ['listing' => $this->listing])
            ->assertSee('Owner Advertising & Promotion Rates')
            ->assertSee('₦20.00 / click')
            ->assertSee('₦0.0050 / view')
            ->assertSee('Nigeria');

        // When logged in as a different user, owner banner should not show
        $otherUser = User::factory()->create();
        Livewire::actingAs($otherUser)
            ->test(ListingDetails::class, ['listing' => $this->listing])
            ->assertDontSee('Owner Advertising & Promotion Rates');
    }

    public function test_seller_can_initiate_promotion_and_target_count_is_stored(): void
    {
        Livewire::actingAs($this->seller)
            ->test(ListingView::class, ['listing' => $this->listing])
            ->call('setActiveTab', 'promotions')
            ->call('setPromoType', 'clicks')
            ->set('promoQuantity', 25)
            ->call('processPromotionPayment');

        $promotion = \App\Models\Promotion::where('listing_id', $this->listing->id)->latest()->first();
        $this->assertNotNull($promotion);
        $this->assertEquals('clicks', $promotion->type);
        $this->assertEquals(25, $promotion->target_count);
        $this->assertEquals(0, $promotion->achieved_count);

        $payment = \App\Models\Payment::where('paymentable_id', $promotion->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(500.00, (float) $payment->amount); // 25 * 20.00 = 500.00
        $this->assertEquals(25, $payment->metadata['target_count']);
    }

    public function test_seller_can_initiate_views_promotion_with_target_count(): void
    {
        Livewire::actingAs($this->seller)
            ->test(ListingView::class, ['listing' => $this->listing])
            ->call('setActiveTab', 'promotions')
            ->call('setPromoType', 'views')
            ->set('promoQuantity', 10000)
            ->call('processPromotionPayment');

        $promotion = \App\Models\Promotion::where('listing_id', $this->listing->id)->where('type', 'views')->latest()->first();
        $this->assertNotNull($promotion);
        $this->assertEquals('views', $promotion->type);
        $this->assertEquals(10000, $promotion->target_count);
        $this->assertEquals(0, $promotion->achieved_count);

        $payment = \App\Models\Payment::where('paymentable_id', $promotion->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(50.00, (float) $payment->amount); // 10000 * 0.0050 = 50.00
        $this->assertEquals(10000, $payment->metadata['target_count']);
    }

    public function test_payment_callback_activates_promotion_and_preserves_target_count(): void
    {
        $promotion = \App\Models\Promotion::create([
            'user_id' => $this->seller->id,
            'listing_id' => $this->listing->id,
            'type' => 'clicks',
            'target_count' => 50,
            'achieved_count' => 0,
            'status' => 'pending',
        ]);

        $payment = \App\Models\Payment::create([
            'user_id' => $this->seller->id,
            'paymentable_id' => $promotion->id,
            'paymentable_type' => \App\Models\Promotion::class,
            'reference' => 'PROM-TEST12345',
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 1000.00,
            'currency' => 'NGN',
            'metadata' => [
                'payment_type' => 'promotion',
                'promotion_id' => $promotion->id,
                'listing_id' => $this->listing->id,
                'type' => 'clicks',
                'quantity' => 50,
                'target_count' => 50,
            ],
        ]);

        $response = $this->actingAs($this->seller)->get(route('payment.callback', [
            'reference' => 'PROM-TEST12345',
            'provider' => 'paystack',
            'mock_success' => 1,
        ]));

        $response->assertRedirect(route('mylisting.view', $this->listing->id));

        $promotion->refresh();
        $this->assertEquals('active', $promotion->status);
        $this->assertEquals(50, $promotion->target_count);
    }
}
