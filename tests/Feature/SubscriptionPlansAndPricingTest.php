<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use App\Models\User;
use App\Services\Location\LocationService;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SubscriptionPlansSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionPlansAndPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountriesSeeder::class,
            RolesAndPermissionsSeeder::class,
            SubscriptionPlansSeeder::class,
        ]);
    }

    public function test_pricing_page_is_publicly_accessible_to_guests(): void
    {
        $response = $this->get(route('pricing'));
        $response->assertStatus(200);
        $response->assertSee('Scale Your Parts & Device Business');
        $response->assertSee('Starter Free');
        $response->assertSee('Pro Technician & Vendor');
        $response->assertSee('Enterprise Salvage & Dealer');
    }

    public function test_subscription_plans_page_is_accessible_to_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription-plans'));
        $response->assertStatus(200);
        $response->assertSee('Marketplace Subscriptions');
        $response->assertSee('Starter Free');
    }

    public function test_plans_are_ordered_dynamically_by_sort_order(): void
    {
        // Add a new plan with sort_order 0 (should appear first)
        $vipPlan = SubscriptionPlan::create([
            'name' => 'VIP Founding Vendor',
            'slug' => 'vip-founding-vendor',
            'request_limit' => 100,
            'response_limit' => 100,
            'listing_limit' => 1000,
            'escrow_percentage' => 3.00,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $nigeria = Country::where('code', 'NG')->first();
        SubscriptionPlanPrice::create([
            'subscription_plan_id' => $vipPlan->id,
            'country_id' => $nigeria->id,
            'price_monthly' => 10000.00,
            'price_annual' => 100000.00,
        ]);

        $orderedPlans = SubscriptionPlan::where('is_active', true)->ordered()->get();
        $this->assertEquals('VIP Founding Vendor', $orderedPlans->first()->name);

        $response = $this->get(route('pricing'));
        $response->assertStatus(200);
        $response->assertSee('VIP Founding Vendor');
    }

    public function test_guest_uses_pricing_of_session_country_and_user_uses_user_country(): void
    {
        $usCountry = Country::firstOrCreate(['code' => 'US'], [
            'name' => 'United States',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'phone_code' => '+1',
            'timezone' => 'America/New_York',
            'is_active' => true,
        ]);

        $proPlan = SubscriptionPlan::where('slug', 'pro-technician-vendor')->firstOrFail();

        SubscriptionPlanPrice::create([
            'subscription_plan_id' => $proPlan->id,
            'country_id' => $usCountry->id,
            'price_monthly' => 15.00,
            'price_annual' => 150.00,
        ]);

        // Guest with US session location
        $this->withSession([
            'current_location' => [
                'country_id' => $usCountry->id,
                'country_code' => 'US',
                'currency' => 'USD',
                'currency_symbol' => '$',
            ],
        ]);

        $response = $this->get(route('pricing'));
        $response->assertStatus(200);
        $response->assertSee('$ 15.00');

        // Logged-in Nigerian user
        $ngCountry = Country::where('code', 'NG')->firstOrFail();
        $nigerianUser = User::factory()->create([
            'country_id' => $ngCountry->id,
        ]);

        $responseAuth = $this->actingAs($nigerianUser)->get(route('subscription-plans'));
        $responseAuth->assertStatus(200);
        $responseAuth->assertSee('2,000.00');
    }

    public function test_format_money_handles_null_and_custom_currencies_gracefully(): void
    {
        $service = app(LocationService::class);

        // When null is passed, must not throw TypeError
        $formattedNull = $service->formatMoney(null);
        $this->assertStringContainsString('0.00', $formattedNull);

        // With USD
        $formattedUsd = $service->formatMoney(45.50, 'USD');
        $this->assertEquals('$ 45.50', $formattedUsd);

        // With NGN
        $formattedNgn = $service->formatMoney(5000, 'NGN');
        $this->assertEquals('₦ 5,000.00', $formattedNgn);
    }

    public function test_admin_can_manage_subscription_plans_with_sort_order(): void
    {
        $role = \App\Models\Role::where('slug', 'super_admin')->first();
        $admin = User::factory()->create(['role_id' => $role?->id]);

        $nigeria = Country::where('code', 'NG')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Settings\AdminSubscriptionPlans::class)
            ->assertSee('Subscription Plans')
            ->assertSee('Starter Free')
            ->call('openCreate')
            ->set('name', 'Custom Wholesale Tier')
            ->set('slug', 'custom-wholesale-tier')
            ->set('sort_order', 5)
            ->set('request_limit', 50)
            ->set('response_limit', 50)
            ->set('listing_limit', 200)
            ->set('escrow_percentage', '4.50')
            ->set('priceRows', [
                [
                    'country_id' => $nigeria->id,
                    'price_monthly' => '15000.00',
                    'price_annual' => '150000.00',
                    'is_active' => true,
                ],
            ])
            ->call('savePlan')
            ->assertHasNoErrors();

        $created = SubscriptionPlan::where('slug', 'custom-wholesale-tier')->first();
        $this->assertNotNull($created);
        $this->assertEquals(5, $created->sort_order);

        // Edit sort order
        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Settings\AdminSubscriptionPlans::class)
            ->call('openEdit', $created->id)
            ->assertSet('sort_order', 5)
            ->set('sort_order', 10)
            ->call('savePlan')
            ->assertHasNoErrors();

        $created->refresh();
        $this->assertEquals(10, $created->sort_order);
    }
}

