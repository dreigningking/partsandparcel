<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use App\Services\Location\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LocationAndLocalizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure countries exist
        Country::updateOrCreate(['code' => 'NG'], [
            'name' => 'Nigeria',
            'phone_code' => '+234',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'timezone' => 'Africa/Lagos',
            'is_active' => true,
        ]);

        Country::updateOrCreate(['code' => 'US'], [
            'name' => 'United States',
            'phone_code' => '+1',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'timezone' => 'America/New_York',
            'is_active' => true,
        ]);
    }

    public function test_guest_visitor_resolves_location_and_caches_in_redis(): void
    {
        $service = app(LocationService::class);
        $request = Request::create('/', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $location = $service->resolveForRequest($request);

        $this->assertEquals('NG', $location['country_code']);
        $this->assertEquals('NGN', $location['currency']);
        $this->assertEquals('₦', $location['currency_symbol']);

        // Verify it was cached
        $cached = Cache::get('visitor_location:127.0.0.1');
        $this->assertNotNull($cached);
        $this->assertEquals('NG', $cached['country_code']);
    }

    public function test_logged_in_user_resolves_location_from_profile(): void
    {
        $user = User::factory()->create([
            'country_code' => 'US',
            'currency' => 'USD',
        ]);

        $service = app(LocationService::class);
        $request = Request::create('/', 'GET');
        $request->setUserResolver(fn () => $user);

        $location = $service->resolveForRequest($request);

        $this->assertEquals('US', $location['country_code']);
        $this->assertEquals('USD', $location['currency']);
        $this->assertEquals('$', $location['currency_symbol']);
        $this->assertEquals('user_profile', $location['source']);
    }

    public function test_middleware_handles_manual_country_switch(): void
    {
        $response = $this->get('/?switch_country=US');

        $response->assertStatus(200);
        $this->assertEquals('US', session('current_location.country_code'));
        $this->assertEquals('USD', session('current_location.currency'));
        $this->assertEquals('$', session('current_location.currency_symbol'));
    }

    public function test_money_formatting(): void
    {
        $service = app(LocationService::class);

        $this->assertEquals('₦ 150,000.00', $service->formatMoney(150000, 'NGN'));
        $this->assertEquals('$ 250.50', $service->formatMoney(250.50, 'USD'));
    }

    public function test_listings_are_scoped_by_current_country(): void
    {
        // Merchant in Nigeria
        $ngMerchant = User::factory()->create(['country_code' => 'NG']);
        $ngListing = Listing::create([
            'user_id' => $ngMerchant->id,
            'quantity' => 2,
            'price' => 50000,
            'status' => 'active',
        ]);

        // Merchant in US
        $usMerchant = User::factory()->create(['country_code' => 'US']);
        $usListing = Listing::create([
            'user_id' => $usMerchant->id,
            'quantity' => 1,
            'price' => 120,
            'status' => 'active',
        ]);

        // When browsing from Nigeria
        $ngResults = Listing::inCurrentCountry('NG')->get();
        $this->assertTrue($ngResults->contains($ngListing));
        $this->assertFalse($ngResults->contains($usListing));

        // When browsing from US
        $usResults = Listing::inCurrentCountry('US')->get();
        $this->assertTrue($usResults->contains($usListing));
        $this->assertFalse($usResults->contains($ngListing));
    }
}
