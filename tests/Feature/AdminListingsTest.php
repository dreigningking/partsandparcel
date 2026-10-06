<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminListingDetails;
use App\Livewire\Admin\AdminListings;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Moderation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminListingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Category $category;
    protected Brand $brand;
    protected DeviceModel $model;
    protected Item $item;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'permissions' => ['*' => true],
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $this->seller = User::factory()->create([
            'business_name' => 'Apex Spares Hub',
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'engine-parts'], [
            'name' => 'Engine Parts',
            'is_listing' => true,
        ]);

        $this->brand = Brand::firstOrCreate(['slug' => 'toyota'], [
            'name' => 'Toyota',
        ]);

        $this->model = DeviceModel::firstOrCreate(['slug' => 'corolla-2022'], [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Corolla 2022',
        ]);

        $this->item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $this->model->id,
            'name' => 'OEM Toyota Corolla Alternator',
            'item_type' => 'part',
            'condition_status' => 'used',
            'description' => 'Tested original working alternator in prime condition.',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $this->item->id,
            'price' => 150000.00,
            'quantity' => 4,
            'reserved_quantity' => 1,
            'sold_quantity' => 1,
            'warranty_period_days' => 30,
            'allow_shipping' => true,
            'is_published' => true,
            'is_active' => true,
        ]);
    }

    public function test_admin_listings_table_renders_and_filters_correctly(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminListings::class)
            ->assertSee('Listings Management')
            ->assertSee('OEM Toyota Corolla Alternator')
            ->assertSee('Toyota')
            ->assertSee('Corolla 2022')
            ->assertSee('Apex Spares Hub')
            ->assertSee('2 Available')
            ->assertSee('150,000')
            ->set('search', 'NonExistentProduct')
            ->assertDontSee('OEM Toyota Corolla Alternator')
            ->set('search', '')
            ->assertSee('OEM Toyota Corolla Alternator');
    }

    public function test_admin_can_quick_approve_and_reject_listing(): void
    {
        // 1. Mark as draft/pending
        $this->listing->update(['is_published' => false]);
        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'status' => 'pending',
            'action' => 'created',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminListings::class)
            ->call('approve', $this->listing->id);

        $this->listing->refresh();
        $this->assertTrue((bool) $this->listing->is_published);

        $moderation = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $this->listing->id)
            ->latest('id')
            ->first();
        $this->assertEquals('approved', $moderation->status);

        // 2. Reject listing
        Livewire::actingAs($this->admin)
            ->test(AdminListings::class)
            ->set('selectedListingId', $this->listing->id)
            ->set('rejectionReason', 'Violates serial number policy')
            ->call('submitReject');

        $this->listing->refresh();
        $this->assertFalse((bool) $this->listing->is_published);

        $moderation = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $this->listing->id)
            ->latest('id')
            ->first();
        $this->assertEquals('rejected', $moderation->status);
        $this->assertEquals('Violates serial number policy', $moderation->reason);
    }

    public function test_admin_listing_details_renders_and_allows_stock_adjustment(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $this->listing])
            ->assertSee('OEM Toyota Corolla Alternator')
            ->assertSee('Apex Spares Hub')
            ->assertSee('Corolla 2022')
            ->assertSee('30 Days Coverage')
            ->assertSee('Delivery & Pickup')
            ->assertSee('4 units')
            ->set('newQuantity', 10)
            ->call('saveStock')
            ->assertSee('10 units');

        $this->listing->refresh();
        $this->assertEquals(10, $this->listing->quantity);

        // Test detail page moderation approve/reject
        Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $this->listing])
            ->call('approve');

        $this->listing->refresh();
        $this->assertTrue((bool) $this->listing->is_published);

        Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $this->listing])
            ->set('rejectionReason', 'Unverifiable authenticity documentation')
            ->call('submitReject');

        $this->listing->refresh();
        $this->assertFalse((bool) $this->listing->is_published);
        $this->assertDatabaseHas('moderations', [
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'status' => 'rejected',
            'reason' => 'Unverifiable authenticity documentation',
        ]);
    }

    public function test_admin_can_toggle_publish_and_delete_listing(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $this->listing])
            ->call('togglePublished');

        $this->listing->refresh();
        $this->assertFalse((bool) $this->listing->is_published);

        Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $this->listing])
            ->call('delete')
            ->assertRedirect(route('admin.properties'));

        $this->assertDatabaseMissing('listings', ['id' => $this->listing->id]);
    }
}
