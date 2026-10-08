<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Responses\MyResponses;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Offer;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyResponsesTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Category $categoryAuto;
    protected Category $categoryLaptop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Emeka Buyer',
            'email' => 'emeka@example.com',
            'is_verified' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'AutoSpares Pro',
            'business_name' => 'AutoSpares Pro Ltd',
            'email' => 'autopro@example.com',
            'is_verified' => true,
        ]);

        $this->categoryAuto = Category::create([
            'name' => 'Engines & Parts',
            'slug' => 'engines-and-parts',
        ]);

        $this->categoryLaptop = Category::create([
            'name' => 'Laptop Motherboards',
            'slug' => 'laptop-motherboards',
        ]);
    }

    public function test_guest_is_redirected_from_my_responses(): void
    {
        $this->get(route('myresponses'))
            ->assertRedirect(route('login'));
    }

    public function test_seller_can_view_my_responses_page(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need 2AZ-FE Engine for Camry 2008',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Looking for clean tokunbo 2AZ-FE engine in Lagos with warranty.',
            'budget' => '₦450,000',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'I have 2 units available in Ladipo warehouse, tested compression 180 PSI with 14 days warranty.',
            'status' => 'visible',
        ]);

        $this->actingAs($this->seller)
            ->get(route('myresponses'))
            ->assertStatus(200)
            ->assertSee('My Community Hub Responses')
            ->assertSee('Need 2AZ-FE Engine for Camry 2008')
            ->assertSee('I have 2 units available in Ladipo warehouse, tested compression 180 PSI with 14 days warranty.');
    }

    public function test_response_shows_truncated_request_and_untruncated_seller_response(): void
    {
        $longRequestBody = str_repeat('Detailed request description for auto engine replacement. ', 15); // > 160 chars
        $fullSellerResponse = 'Very specific seller offer: complete tokunbo unit with intake manifold, starter motor, alternator, untouched wiring harness, and 30-day testing guarantee with full return policy if compression fails.';

        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'High Spec Tokunbo Engine Needed',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => $longRequestBody,
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => $fullSellerResponse,
            'status' => 'visible',
        ]);

        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->assertSee($fullSellerResponse)
            ->assertSee('...'); // Request preview is truncated with ellipses
    }

    public function test_response_shows_offers_count_and_view_offers_button(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need HP EliteBook Motherboard',
            'type' => 'item',
            'category_id' => $this->categoryLaptop->id,
            'body' => 'Core i5 8th gen motherboard needed urgently.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'Original pulled motherboard available.',
            'status' => 'visible',
        ]);

        $offer = Offer::create([
            'discussion_id' => $discussion->id,
            'response_id' => $response->id,
            'sender_id' => $this->seller->id,
            'recipient_id' => $this->buyer->id,
            'status' => 'pending',
            'terms' => '14 days warranty included.',
        ]);

        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->assertSee('1 Offer')
            ->assertSee('View Offers')
            ->assertSee(route('offers.view', ['offer_id' => 'OFF-' . $offer->id]))
            ->assertSee(route('community.request', ['id' => $discussion->id]));
    }

    public function test_seller_can_search_by_request_or_response(): void
    {
        $d1 = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Toyota Corolla Transmission',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Need automatic transmission box.',
            'status' => 'open',
        ]);

        $r1 = Response::create([
            'discussion_id' => $d1->id,
            'user_id' => $this->seller->id,
            'body' => 'Available in our Ikeja store.',
            'status' => 'visible',
        ]);

        $d2 = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Dell XPS Screen Assembly',
            'type' => 'item',
            'category_id' => $this->categoryLaptop->id,
            'body' => '4K touch display wanted.',
            'status' => 'open',
        ]);

        $r2 = Response::create([
            'discussion_id' => $d2->id,
            'user_id' => $this->seller->id,
            'body' => 'We can supply this OLED panel.',
            'status' => 'visible',
        ]);

        // Search matching request title
        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->set('search', 'Corolla')
            ->assertSee('Toyota Corolla Transmission')
            ->assertDontSee('Dell XPS Screen Assembly');

        // Search matching seller response body
        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->set('search', 'OLED panel')
            ->assertSee('Dell XPS Screen Assembly')
            ->assertDontSee('Toyota Corolla Transmission');
    }

    public function test_seller_can_filter_by_type_category_and_statuses(): void
    {
        $dItem = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need Brake Pads',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Ceramic pads needed.',
            'status' => 'open',
        ]);

        $rItem = Response::create([
            'discussion_id' => $dItem->id,
            'user_id' => $this->seller->id,
            'body' => 'Original Brembo pads in stock.',
            'status' => 'visible',
        ]);

        $dService = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'ECU Remapping Service Needed',
            'type' => 'service',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Looking for tuner in Lagos.',
            'status' => 'resolved',
        ]);

        $rService = Response::create([
            'discussion_id' => $dService->id,
            'user_id' => $this->seller->id,
            'body' => 'We offer stage 1 remap.',
            'status' => 'visible',
        ]);

        // Filter by type: service
        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->set('typeFilter', 'service')
            ->assertSee('ECU Remapping Service Needed')
            ->assertDontSee('Need Brake Pads');

        // Filter by requestStatus: open
        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->set('requestStatusFilter', 'open')
            ->assertSee('Need Brake Pads')
            ->assertDontSee('ECU Remapping Service Needed');

        // Filter by category
        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->set('categoryFilter', (string) $this->categoryLaptop->id)
            ->assertDontSee('Need Brake Pads')
            ->assertDontSee('ECU Remapping Service Needed');
    }

    public function test_old_myresponse_view_route_redirects_to_myresponses(): void
    {
        $this->actingAs($this->seller)
            ->get('/myresponses/RESP-1092')
            ->assertRedirect(route('myresponses'));
    }

    public function test_seller_can_edit_response_without_offer_and_open_modal(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need HP Pavilion Charger',
            'type' => 'item',
            'category_id' => $this->categoryLaptop->id,
            'body' => 'Need blue pin 65W charger in Ikeja.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'Initial comment: we might have it.',
            'status' => 'visible',
        ]);

        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->assertSee('Edit Response')
            ->call('openEditModal', $response->id)
            ->assertSet('showEditModal', true)
            ->assertSet('responseText', 'Initial comment: we might have it.')
            ->set('responseText', 'Updated comment: Original 65W charger in stock, bench tested.')
            ->call('saveEditedResponse')
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('responses', [
            'id' => $response->id,
            'body' => 'Updated comment: Original 65W charger in stock, bench tested.',
        ]);
    }

    public function test_seller_can_attach_offer_when_editing_response(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need Camry Alternator',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Original alternator needed.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'Tokunbo alternator available.',
            'status' => 'visible',
        ]);

        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->call('openEditModal', $response->id)
            ->set('isOfferActive', true)
            ->set('composerItemDescription', 'Tested Denso Alternator 120A')
            ->set('composerItemPrice', '35000')
            ->set('composerItemWarranty', 30)
            ->set('composerOfferDelivery', 'seller_delivery')
            ->call('saveEditedResponse');

        $this->assertDatabaseHas('offers', [
            'response_id' => $response->id,
            'sender_id' => $this->seller->id,
            'recipient_id' => $this->buyer->id,
            'status' => 'pending',
        ]);
    }

    public function test_response_with_existing_offer_or_closed_discussion_cannot_be_edited(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Closed Request',
            'type' => 'item',
            'category_id' => $this->categoryAuto->id,
            'body' => 'Completed discussion.',
            'status' => 'closed',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'Response on closed request.',
            'status' => 'visible',
        ]);

        Livewire::actingAs($this->seller)
            ->test(MyResponses::class)
            ->assertDontSee('Edit Response')
            ->call('openEditModal', $response->id)
            ->assertSet('showEditModal', false);
    }

    public function test_community_request_page_blocks_duplicate_response_and_shows_sidebar(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need ThinkPad Battery',
            'type' => 'item',
            'category_id' => $this->categoryLaptop->id,
            'body' => 'Internal 3-cell battery needed.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'body' => 'Original battery with 4 hours backup available.',
            'status' => 'visible',
        ]);

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id])
            ->assertSee('Response Already Submitted')
            ->assertSee('Your Submitted Response')
            ->assertSee('Original battery with 4 hours backup available.')
            ->call('openUserResponseModal')
            ->assertSet('showUserResponseModal', true)
            ->assertSee('Your Message');
    }
}
