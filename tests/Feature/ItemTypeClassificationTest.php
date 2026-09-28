<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ItemTypeClassificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Location $location;
    protected DeviceModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->location = Location::create([
            'user_id' => $this->user->id,
            'label' => 'Main Workshop',
            'contact_name' => 'Test User',
            'address_line_1' => '123 Test St',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'NG',
            'is_default' => true,
        ]);

        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);
        $this->model = DeviceModel::create([
            'category_id' => $category->id,
            'name' => 'EliteBook 840 G5',
            'slug' => 'elitebook-840-g5',
        ]);
    }

    public function test_can_create_whole_device_asset_for_adam()
    {
        $this->actingAs($this->user);

        Livewire::test('dashboard.inventory.item-create')
            ->set('item_type', 'whole')
            ->set('model_id', $this->model->id)
            ->set('name', 'Adam HP EliteBook 840 G5')
            ->set('condition_status', 'used')
            ->set('location_id', $this->location->id)
            ->set('price', 250000)
            ->set('quantity', 2)
            ->call('submitListing');

        $this->assertDatabaseHas('items', [
            'user_id' => $this->user->id,
            'name' => 'Adam HP EliteBook 840 G5',
            'item_type' => 'whole',
            'parent_id' => null,
        ]);

        $item = Item::where('name', 'Adam HP EliteBook 840 G5')->first();
        $this->assertDatabaseHas('listings', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'price' => 250000,
            'quantity' => 2,
        ]);
    }

    public function test_can_create_standalone_spare_part_for_seth()
    {
        $this->actingAs($this->user);

        Livewire::test('dashboard.inventory.item-create')
            ->set('item_type', 'part')
            ->set('model_id', $this->model->id)
            ->set('name', 'Seth 14-inch LCD Screen')
            ->set('condition_status', 'new')
            ->set('location_id', $this->location->id)
            ->set('price', 45000)
            ->set('quantity', 5)
            ->call('submitListing');

        $this->assertDatabaseHas('items', [
            'user_id' => $this->user->id,
            'name' => 'Seth 14-inch LCD Screen',
            'item_type' => 'part',
            'parent_id' => null,
        ]);

        $item = Item::where('name', 'Seth 14-inch LCD Screen')->first();
        $this->assertDatabaseHas('listings', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'price' => 45000,
            'quantity' => 5,
        ]);
    }

    public function test_can_create_scrap_unit_with_disassembled_components_for_abel()
    {
        $this->actingAs($this->user);

        Livewire::test('dashboard.inventory.item-create')
            ->set('item_type', 'scrap')
            ->set('model_id', $this->model->id)
            ->set('name', 'Abel Salvage Laptop Unit')
            ->set('location_id', $this->location->id)
            ->set('disassemble_mode', true)
            ->set('harvested_components', [
                [
                    'name' => 'Harvested Motherboard',
                    'condition_status' => 'TESTED WORKING',
                    'notes' => 'Boots clean',
                    'price' => 35000,
                    'list_for_sale' => true,
                ],
                [
                    'name' => 'Unidentified Internal Cable',
                    'condition_status' => 'UNTESTED',
                    'notes' => 'Untested ribbon',
                    'price' => 5000,
                    'list_for_sale' => true,
                ],
            ])
            ->set('price', 80000)
            ->call('submitListing');

        $parentItem = Item::where('name', 'Abel Salvage Laptop Unit')->first();
        $this->assertNotNull($parentItem);
        $this->assertEquals('scrap', $parentItem->item_type);
        $this->assertEquals('faulty', $parentItem->condition_status);

        $this->assertDatabaseHas('items', [
            'parent_id' => $parentItem->id,
            'name' => 'Harvested Motherboard',
            'item_type' => 'part',
        ]);

        $this->assertDatabaseHas('items', [
            'parent_id' => $parentItem->id,
            'name' => 'Unidentified Internal Cable',
            'item_type' => 'part',
        ]);

        // 3 listings created (1 whole scrap unit + 2 component listings)
        $this->assertEquals(3, Listing::where('user_id', $this->user->id)->count());
    }

    public function test_non_technician_abel_can_list_whole_scrap_unit_as_is()
    {
        $this->actingAs($this->user);

        Livewire::test('dashboard.inventory.item-create')
            ->set('item_type', 'scrap')
            ->set('model_id', $this->model->id)
            ->set('name', 'Non Tech Washing Machine Scrap')
            ->set('location_id', $this->location->id)
            ->set('as_is_scrap', true)
            ->set('price', 50000)
            ->call('submitListing');

        $parentItem = Item::where('name', 'Non Tech Washing Machine Scrap')->first();
        $this->assertNotNull($parentItem);
        $this->assertEquals('scrap', $parentItem->item_type);
        $this->assertEquals('faulty', $parentItem->condition_status);
        $this->assertEquals(0, $parentItem->children()->count());

        $this->assertDatabaseHas('listings', [
            'user_id' => $this->user->id,
            'item_id' => $parentItem->id,
            'price' => 50000,
        ]);
    }

    public function test_item_create_media_upload_attaches_media_to_item_and_listing()
    {
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->image('laptop.jpg', 1000, 1000);
        $video = UploadedFile::fake()->create('demo.mp4', 1024, 'video/mp4');

        Livewire::test('dashboard.inventory.item-create')
            ->set('item_type', 'whole')
            ->set('model_id', $this->model->id)
            ->set('name', 'Media Tested HP Laptop')
            ->set('condition_status', 'new')
            ->set('location_id', $this->location->id)
            ->set('photos', [$image, $video])
            ->set('price', 300000)
            ->call('submitListing');

        $item = Item::where('name', 'Media Tested HP Laptop')->first();
        $this->assertNotNull($item);
        $this->assertEquals(2, $item->media()->count());

        $listing = Listing::where('item_id', $item->id)->first();
        $this->assertNotNull($listing);
        $this->assertEquals(2, $listing->media()->count());
    }
}
