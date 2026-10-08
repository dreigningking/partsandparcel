<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Requests\MyRequests;
use App\Livewire\Dashboard\Requests\MyRequestView;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Jobs\NotifyDiscussionEditedJob;
use App\Models\Response;
use App\Models\User;
use App\Models\Watchlist;
use App\Notifications\DiscussionEditedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
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

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->vendor->id,
            'body' => 'I have the original OEM radiator in stock.',
        ]);

        $offer = Offer::create([
            'sender_id' => $this->vendor->id,
            'recipient_id' => $this->user->id,
            'discussion_id' => $discussion->id,
            'response_id' => $response->id,
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
            ->assertSee('Offer Attached')
            ->assertSee('View Offer')
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

    public function test_user_can_edit_request_and_queue_debounce_notification(): void
    {
        Queue::fake();

        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Mercedes C200 Side Mirror Glass',
            'body' => 'Driver side heated glass needed.',
            'budget' => '₦40,000',
            'status' => 'open',
        ]);

        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => $discussion->id])
            ->assertSet('isOwner', true)
            ->call('openEditModal')
            ->assertSet('showEditModal', true)
            ->set('editTitle', 'Mercedes C200 Side Mirror Glass (Heated OEM)')
            ->set('editBody', 'Driver side heated glass needed with auto-dimming.')
            ->set('editBudget', '₦45,000')
            ->call('saveRequest')
            ->assertSet('showEditModal', false)
            ->assertSee('Mercedes C200 Side Mirror Glass (Heated OEM)')
            ->assertSee('Driver side heated glass needed with auto-dimming.');

        $this->assertDatabaseHas('discussions', [
            'id' => $discussion->id,
            'title' => 'Mercedes C200 Side Mirror Glass (Heated OEM)',
            'budget' => '₦45,000',
        ]);

        Queue::assertPushed(NotifyDiscussionEditedJob::class);
    }

    public function test_only_last_5_responses_are_displayed_and_replies_identified(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Toyota Rav4 Alternator',
            'body' => '2016 Rav4 2.5L Alternator needed.',
            'status' => 'open',
        ]);

        // Create 7 responses
        $responses = [];
        for ($i = 1; $i <= 7; $i++) {
            $responses[] = Response::create([
                'discussion_id' => $discussion->id,
                'user_id' => $this->vendor->id,
                'body' => "Response inquiry number {$i}",
            ]);
        }

        // Attach an offer to response #7
        Offer::create([
            'sender_id' => $this->vendor->id,
            'recipient_id' => $this->user->id,
            'discussion_id' => $discussion->id,
            'response_id' => $responses[6]->id,
            'delivery_method' => 'seller_responsible',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => $discussion->id])
            ->assertCount('responses', 5)
            ->assertSee('Response inquiry number 7')
            ->assertSee('Offer Attached')
            ->assertSee('View Offer')
            ->assertDontSee('Response inquiry number 1')
            ->assertDontSee('Response inquiry number 2')
            ->call('openQuickViewOffer', $responses[6]->id)
            ->assertDispatched('open-quick-view-offer', response_id: $responses[6]->id);
    }

    public function test_demo_fallback_for_non_existent_request_id(): void
    {
        Livewire::actingAs($this->user)
            ->test(MyRequestView::class, ['id' => 999999])
            ->assertSee('Looking for HP EliteBook 840 G5 Motherboard')
            ->assertSee('Abel Electronics')
            ->assertSee('Seth Tech Hub');
    }

    public function test_notify_discussion_edited_job_sends_notification_to_responders_and_watchers(): void
    {
        Notification::fake();

        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Toyota Camry ECU',
            'body' => 'ECU unit needed.',
            'status' => 'open',
        ]);

        $watcher = User::factory()->create(['email' => 'watcher@example.com']);
        Watchlist::create([
            'user_id' => $watcher->id,
            'watchable_type' => Discussion::class,
            'watchable_id' => $discussion->id,
        ]);

        $responder = User::factory()->create(['email' => 'responder@example.com']);
        Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $responder->id,
            'body' => 'I have this part available.',
        ]);

        $timestamp = now()->timestamp;
        Cache::put("discussion_edit_timestamp_{$discussion->id}", $timestamp, 300);

        $job = new NotifyDiscussionEditedJob($discussion->id, $timestamp);
        $job->handle();

        // Notification should be sent to watcher and responder, but NOT the discussion owner ($this->user)
        Notification::assertSentTo(
            [$watcher, $responder],
            DiscussionEditedNotification::class
        );
        Notification::assertNotSentTo(
            [$this->user],
            DiscussionEditedNotification::class
        );
    }

    public function test_notify_discussion_edited_job_debounces_when_newer_edit_timestamp_exists(): void
    {
        Notification::fake();

        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Toyota Camry ECU',
            'body' => 'ECU unit needed.',
            'status' => 'open',
        ]);

        $responder = User::factory()->create(['email' => 'responder2@example.com']);
        Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $responder->id,
            'body' => 'I have this part available.',
        ]);

        $firstTimestamp = 1000;
        $newerTimestamp = 2000;

        // Cache contains the newer timestamp from a subsequent edit
        Cache::put("discussion_edit_timestamp_{$discussion->id}", $newerTimestamp, 300);

        // Run the older job with the older timestamp
        $job = new NotifyDiscussionEditedJob($discussion->id, $firstTimestamp);
        $job->handle();

        // No notification should have been sent by the older job because it was debounced
        Notification::assertNothingSent();
    }
}
