<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDiscussions;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Moderation;
use App\Models\Offer;
use App\Models\Response;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDiscussionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Category $category;
    protected Brand $brand;
    protected DeviceModel $model;
    protected Discussion $discussion;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'permissions' => ['*' => true],
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Moderator',
            'role_id' => $adminRole->id,
        ]);

        $this->member = User::factory()->create([
            'name' => 'Emeka Okafor',
            'email' => 'emeka@example.com',
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'laptops-accessories'], [
            'name' => 'Laptops & Computers',
        ]);

        $this->brand = Brand::firstOrCreate(['slug' => 'hp-inc'], [
            'name' => 'HP',
        ]);

        $this->model = DeviceModel::firstOrCreate(['slug' => 'elitebook-840-g5'], [
            'name' => 'EliteBook 840 G5',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $this->discussion = Discussion::create([
            'user_id' => $this->member->id,
            'type' => 'item',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'title' => 'Need HP EliteBook Motherboard in Ikeja',
            'body' => 'Looking for clean tested HP EliteBook 840 G5 motherboard without GPU fault.',
            'budget' => '85000',
            'status' => 'open',
            'attachments' => [
                'fulfillment' => 'Pickup Only',
                'urgency' => 'Immediate (1-2 days)',
                'views' => 45,
            ],
        ]);
    }

    public function test_admin_discussions_renders_table_and_filters(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDiscussions::class)
            ->assertSee('Discussions & Requests Moderation')
            ->assertSee('Need HP EliteBook Motherboard in Ikeja')
            ->assertSee('Emeka Okafor')
            ->assertSee('EliteBook 840 G5')
            ->assertSee('85,000')
            ->assertSee('Pickup Only')
            ->set('search', 'NonExistentThreadKeyword')
            ->assertDontSee('Need HP EliteBook Motherboard in Ikeja')
            ->set('search', '')
            ->assertSee('Need HP EliteBook Motherboard in Ikeja');
    }

    public function test_admin_can_open_modal_and_switch_tabs(): void
    {
        // Add a reply and an offer
        Response::create([
            'user_id' => $this->admin->id,
            'discussion_id' => $this->discussion->id,
            'body' => 'I have this motherboard available at Computer Village.',
        ]);

        Offer::create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->member->id,
            'discussion_id' => $this->discussion->id,
            'terms' => 'Fully tested with 30-day warranty.',
            'status' => 'pending',
            'delivery_method' => 'buyer_responsible',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDiscussions::class)
            ->call('showDiscussion', $this->discussion->id, 'details')
            ->assertSee('Topic Content & Specifications')
            ->assertSee('Looking for clean tested HP EliteBook')
            ->call('setModalTab', 'replies')
            ->assertSee('I have this motherboard available at Computer Village.')
            ->call('setModalTab', 'offers')
            ->assertSee('Fully tested with 30-day warranty.')
            ->call('closeDiscussion')
            ->assertSet('selectedDiscussionId', null);
    }

    public function test_admin_can_approve_discussion(): void
    {
        // Set pending moderation
        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $this->discussion->id,
            'status' => 'pending',
            'action' => 'created',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDiscussions::class)
            ->call('approve', $this->discussion->id);

        $moderation = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $this->discussion->id)
            ->latest('id')
            ->first();

        $this->assertEquals('approved', $moderation->status);
    }

    public function test_admin_can_reject_discussion_with_reason(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDiscussions::class)
            ->call('openRejectModal', $this->discussion->id)
            ->assertSet('showRejectModal', true)
            ->set('rejectionReason', 'Violates marketplace community terms regarding off-platform payment.')
            ->call('submitReject')
            ->assertSet('showRejectModal', false);

        $moderation = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $this->discussion->id)
            ->latest('id')
            ->first();

        $this->assertEquals('rejected', $moderation->status);
        $this->assertEquals('Violates marketplace community terms regarding off-platform payment.', $moderation->reason);

        $this->discussion->refresh();
        $this->assertEquals('closed', $this->discussion->status);
    }

    public function test_admin_can_toggle_pin_lock_change_status_and_delete(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDiscussions::class);

        // Toggle Pin
        $component->call('togglePin', $this->discussion->id);
        $this->discussion->refresh();
        $this->assertTrue($this->discussion->is_pinned);

        // Toggle Lock
        $component->call('toggleLock', $this->discussion->id);
        $this->discussion->refresh();
        $this->assertTrue($this->discussion->is_locked);

        // Change Lifecycle status
        $component->call('changeStatus', $this->discussion->id, 'fulfilled');
        $this->discussion->refresh();
        $this->assertEquals('fulfilled', $this->discussion->status);

        // Delete discussion
        $component->call('deleteDiscussion', $this->discussion->id);
        $this->assertDatabaseMissing('discussions', ['id' => $this->discussion->id]);
    }
}
