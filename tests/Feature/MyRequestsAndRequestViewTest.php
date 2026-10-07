<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Requests\MyRequests;
use App\Livewire\Dashboard\Requests\MyRequestView;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyRequestsAndRequestViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $vendor;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Chidi Requester',
            'email' => 'chidi@example.com',
            'is_verified' => true,
        ]);

        $this->vendor = User::factory()->create([
            'name' => 'AutoSpares Hub',
            'business_name' => 'AutoSpares Hub Nig Ltd',
            'email' => 'autospares@example.com',
            'is_verified' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Engine & Drivetrain',
            'slug' => 'engine-drivetrain',
        ]);
    }

    public function test_user_can_view_my_requests_page(): void
    {
        $this->actingAs($this->user)
            ->get('/myrequests')
            ->assertStatus(200)
            ->assertSee('My Community Requests')
            ->assertSee('Post New Request');
    }

    public function test_my_requests_lists_discussions_dynamically_with_modern_appearance(): void
    {
        $discussion1 = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Need Toyota Camry 2.4L Engine block',
            'body' => 'Need a tested Tokunbo engine block in Lagos with warranty.',
            'budget' => '₦450,000',
            'status' => 'open',
            'attachments' => [
                'location' => 'Ladipo, Lagos',
                'budget' => '₦450,000',
            ],
        ]);

        $discussion2 = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'service',
            'title' => 'ECU Remapping and Diagnostics needed',
            'body' => 'Require an on-site technician for Mercedes W204 diagnostic.',
            'budget' => '₦35,000',
            'status' => 'resolved',
            'attachments' => [
                'location' => 'Ikeja, Lagos',
                'budget' => '₦35,000',
            ],
        ]);

        // Create an offer on discussion 1
        $offer = Offer::create([
            'sender_id' => $this->vendor->id,
            'recipient_id' => $this->user->id,
            'discussion_id' => $discussion1->id,
            'delivery_method' => 'seller_responsible',
            'status' => 'pending',
        ]);

        OfferItem::create([
            'offer_id' => $offer->id,
            'description' => 'Toyota Camry 2.4L Tested Tokunbo Engine',
            'type' => 'item',
            'quantity' => 1,
            'unit_price' => 440000,
            'warranty_period_days' => 30,
        ]);

        // Test Livewire component
        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->assertSet('activeTab', 'open')
            ->assertSee('Need Toyota Camry 2.4L Engine block')
            ->assertSee('Part / Product')
            ->assertSee('Engine & Drivetrain')
            ->assertSee('OPEN FOR OFFERS')
            ->assertSee('1 Private Offer Received')
            ->assertSee('From ₦440,000')
            ->assertSee('#REQ-' . $discussion1->id)
            ->call('setTab', 'fulfilled')
            ->assertSet('activeTab', 'fulfilled')
            ->assertSee('ECU Remapping and Diagnostics needed')
            ->assertSee('Service / Repair')
            ->assertSee('FULFILLED')
            ->assertDontSee('Need Toyota Camry 2.4L Engine block');
    }

    public function test_my_requests_search_and_filters(): void
    {
        Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Looking for Lexus RX350 Alternator',
            'body' => 'Original OEM 130A alternator.',
            'budget' => '₦95,000',
            'status' => 'open',
        ]);

        Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'BMW 328i Radiator Hose',
            'body' => 'Upper coolant hose.',
            'budget' => '₦25,000',
            'status' => 'open',
        ]);

        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->set('search', 'Lexus')
            ->assertSee('Looking for Lexus RX350 Alternator')
            ->assertDontSee('BMW 328i Radiator Hose')
            ->set('search', '')
            ->assertSee('BMW 328i Radiator Hose');
    }

    public function test_user_can_execute_lifecycle_actions_from_my_requests(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Toyota Corolla Front Bumper',
            'body' => 'Need 2015 Corolla bumper in Lagos.',
            'status' => 'open',
        ]);

        // Mark as fulfilled
        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->call('markFulfilled', $discussion->id)
            ->assertSee('marked as fulfilled');

        $this->assertEquals('resolved', $discussion->fresh()->status);

        // Reopen
        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->call('reopenRequest', $discussion->id)
            ->assertSee('reopened for proposals');

        $this->assertEquals('open', $discussion->fresh()->status);

        // Close
        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->call('closeRequest', $discussion->id)
            ->assertSee('has been closed');

        $this->assertEquals('closed', $discussion->fresh()->status);

        // Delete
        Livewire::actingAs($this->user)
            ->test(MyRequests::class)
            ->call('deleteRequest', $discussion->id)
            ->assertSee('has been deleted');

        $this->assertDatabaseMissing('discussions', ['id' => $discussion->id]);
    }

    public function test_user_can_view_my_request_view_page(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Honda Accord Power Steering Pump',
            'body' => 'Need 2008-2012 Honda Accord V6 power steering pump.',
            'budget' => '₦65,000',
            'status' => 'open',
            'attachments' => [
                'location' => 'Garki, Abuja',
                'budget' => '₦65,000',
            ],
        ]);

        $this->actingAs($this->user)
            ->get('/myrequests/' . $discussion->id)
            ->assertStatus(200)
            ->assertSee('Honda Accord Power Steering Pump')
            ->assertSee('OPEN FOR PROPOSALS')
            ->assertSee('Request Details &amp; Specifications', false)
            ->assertSee('Parts &amp; Parcel Escrow', false);
    }

    public function test_user_can_accept_vendor_offer_from_my_request_view(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Nissan Pathfinder Radiator Assembly',
            'body' => 'Looking for clean radiator.',
            'budget' => '₦75,000',
            'status' => 'open',
        ]);

        $offer = Offer::create([
            'sender_id' => $this->vendor->id,
            'recipient_id' => $this->user->id,
            'discussion_id' => $discussion->id,
            'delivery_method' => 'seller_responsible',
            'status' => 'pending',
        ]);

        OfferItem::create([
            'offer_id' => $offer->id,
            'description' => 'Original OEM Nissan Radiator',
            'type' => 'item',
            'quantity' => 1,
            'unit_price' => 70000,
            'warranty_period_days' => 14,
        ]);

        // Accept offer
        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => $discussion->id])
            ->assertSee('Original OEM Nissan Radiator')
            ->assertSee('₦70,000.00')
            ->call('acceptOffer', $offer->id);

        $this->assertEquals('accepted', $offer->fresh()->status);
        $this->assertDatabaseHas('invoices', [
            'buyer_id' => $this->user->id,
            'seller_id' => $this->vendor->id,
            'total' => 70000,
            'status' => 'issued',
        ]);
    }

    public function test_user_can_post_comment_reply_from_my_request_view(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Mercedes C200 Side Mirror Glass',
            'body' => 'Driver side heated glass needed.',
            'status' => 'open',
        ]);

        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => $discussion->id])
            ->set('replyText', 'Does this include the blind spot indicator triangle?')
            ->call('postReply')
            ->assertSee('Does this include the blind spot indicator triangle?');

        $this->assertDatabaseHas('responses', [
            'discussion_id' => $discussion->id,
            'user_id' => $this->user->id,
            'body' => 'Does this include the blind spot indicator triangle?',
        ]);
    }

    public function test_demo_fallback_for_non_existent_request_id(): void
    {
        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => 999999])
            ->assertSee('Looking for HP EliteBook 840 G5 Motherboard')
            ->assertSee('Abel Electronics')
            ->assertSee('Seth Tech Hub');
    }
}
