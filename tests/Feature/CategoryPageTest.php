<?php

namespace Tests\Feature;

use App\Livewire\Marketplace\Listings\Category;
use App\Livewire\Marketplace\Listings\CategoryDevices;
use App\Livewire\Marketplace\Listings\CategoryParts;
use App\Livewire\Marketplace\Listings\CategoryRequests;
use App\Livewire\Marketplace\Listings\CategoryScraps;
use App\Models\Brand;
use App\Models\Category as CategoryModel;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Location $location;
    protected CategoryModel $category;
    protected Brand $brand;
    protected DeviceModel $deviceModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->location = Location::create([
            'user_id' => $this->user->id,
            'label' => 'Main Yard',
            'contact_name' => 'John Doe',
            'address_line_1' => '10 Computer Village',
            'city' => 'Ikeja',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'is_default' => true,
        ]);

        $this->category = CategoryModel::create(['name' => 'Laptops', 'slug' => 'laptops']);
        $this->brand = Brand::create(['name' => 'HP', 'slug' => 'hp']);
        $this->deviceModel = DeviceModel::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'EliteBook 840 G5',
            'slug' => 'hp-elitebook-840-g5',
        ]);

        // Whole item & listing
        $wholeItem = Item::create([
            'user_id' => $this->user->id,
            'model_id' => $this->deviceModel->id,
            'item_type' => 'whole',
            'name' => 'HP EliteBook 840 G5 Laptop',
            'condition_status' => 'used_clean',
            'status' => 'available',
        ]);
        Listing::create([
            'user_id' => $this->user->id,
            'item_id' => $wholeItem->id,
            'location_id' => $this->location->id,
            'price' => 280000,
            'quantity' => 2,
            'status' => 'active',
        ]);

        // Part item & listing
        $partItem = Item::create([
            'user_id' => $this->user->id,
            'model_id' => $this->deviceModel->id,
            'item_type' => 'part',
            'name' => 'HP EliteBook Battery',
            'condition_status' => 'tested_working',
            'status' => 'available',
        ]);
        Listing::create([
            'user_id' => $this->user->id,
            'item_id' => $partItem->id,
            'location_id' => $this->location->id,
            'price' => 25000,
            'quantity' => 5,
            'status' => 'active',
        ]);

        // Scrap item & listing
        $scrapItem = Item::create([
            'user_id' => $this->user->id,
            'model_id' => $this->deviceModel->id,
            'item_type' => 'scrap',
            'name' => 'HP EliteBook Screen Fault (Salvage)',
            'condition_status' => 'scrap',
            'status' => 'available',
        ]);
        Listing::create([
            'user_id' => $this->user->id,
            'item_id' => $scrapItem->id,
            'location_id' => $this->location->id,
            'price' => 90000,
            'quantity' => 1,
            'status' => 'active',
        ]);

        // Community Discussion
        Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->deviceModel->id,
            'location_id' => $this->location->id,
            'title' => 'Need HP EliteBook Motherboard',
            'body' => 'Searching for tested motherboard in Ikeja',
            'budget' => '60,000',
            'type' => 'item',
            'status' => 'open',
        ]);
    }

    public function test_category_page_renders_successfully_with_default_devices_tab(): void
    {
        $response = $this->get(route('category'));
        $response->assertStatus(200);
        $response->assertSee('Complete Devices');
        $response->assertSee('HP EliteBook 840 G5 Laptop');
    }

    public function test_category_page_switches_tabs_to_parts_scraps_and_requests(): void
    {
        // Parts tab
        $response = $this->get(route('category', ['tab' => 'parts']));
        $response->assertStatus(200);
        $response->assertSee('HP EliteBook Battery');

        // Scraps tab (both scraps and scrap alias)
        $responseScraps = $this->get(route('category', ['tab' => 'scraps']));
        $responseScraps->assertStatus(200);
        $responseScraps->assertSee('HP EliteBook Screen Fault');

        $responseScrapAlias = $this->get(route('category', ['tab' => 'scrap']));
        $responseScrapAlias->assertStatus(200);
        $responseScrapAlias->assertSee('HP EliteBook Screen Fault');

        // Requests tab (both requests and community alias)
        $responseRequests = $this->get(route('category', ['tab' => 'requests']));
        $responseRequests->assertStatus(200);
        $responseRequests->assertSee('Need HP EliteBook Motherboard');

        $responseCommunityAlias = $this->get(route('category', ['tab' => 'community']));
        $responseCommunityAlias->assertStatus(200);
        $responseCommunityAlias->assertSee('Need HP EliteBook Motherboard');
    }

    public function test_devices_subpage_loads_listings_where_item_type_is_whole(): void
    {
        $component = Livewire::test(CategoryDevices::class);
        $listings = $component->viewData('listings');

        $this->assertNotEmpty($listings);
        foreach ($listings as $listing) {
            $this->assertEquals('whole', $listing->assetable->item_type);
        }
    }

    public function test_parts_subpage_loads_listings_where_item_type_is_part(): void
    {
        $component = Livewire::test(CategoryParts::class);
        $listings = $component->viewData('listings');

        $this->assertNotEmpty($listings);
        foreach ($listings as $listing) {
            $this->assertContains($listing->assetable->item_type, ['part', 'parts']);
        }
    }

    public function test_scraps_subpage_loads_listings_where_item_type_is_scrap(): void
    {
        $component = Livewire::test(CategoryScraps::class);
        $listings = $component->viewData('listings');

        $this->assertNotEmpty($listings);
        foreach ($listings as $listing) {
            $this->assertEquals('scrap', $listing->assetable->item_type);
        }
    }

    public function test_requests_subpage_loads_discussions_matching_category(): void
    {
        $component = Livewire::test(CategoryRequests::class, ['cat' => $this->category->slug]);
        $discussions = $component->viewData('discussions');

        $this->assertNotEmpty($discussions);
        foreach ($discussions as $discussion) {
            $this->assertEquals($this->category->id, $discussion->category_id);
        }
    }

    public function test_tailored_filters_operate_accurately(): void
    {
        // 1. Devices filter by condition and price
        Livewire::test(CategoryDevices::class)
            ->set('selectedConditions', ['used'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() >= 1;
            })
            ->set('minPrice', 400000)
            ->assertViewHas('listings', function ($listings) {
                return $listings->isEmpty();
            });

        // 2. Parts filter by component type and condition (new, used, refurbished)
        Livewire::test(CategoryParts::class)
            ->set('selectedComponentTypes', ['Battery'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() >= 1;
            })
            ->set('selectedConditions', ['refurbished'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() >= 1;
            });

        // 3. Scraps filter by damage fault
        Livewire::test(CategoryScraps::class)
            ->set('selectedFaults', ['Screen'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() >= 1;
            });

        // 4. Requests filter by type (device, service, advice)
        Livewire::test(CategoryRequests::class)
            ->set('selectedType', 'device')
            ->assertViewHas('discussions', function ($discussions) {
                return $discussions->count() >= 1;
            })
            ->set('selectedType', 'advice')
            ->assertViewHas('discussions', function ($discussions) {
                return $discussions->isEmpty();
            });
    }
}
