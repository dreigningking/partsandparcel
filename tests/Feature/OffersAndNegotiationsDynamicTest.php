<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Role;
use App\Models\User;
use App\Services\Commercial\NegotiationService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OffersAndNegotiationsDynamicTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected User $thirdParty;
    protected Item $item;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->buyer = User::factory()->create([
            'name' => 'Chidi Buyer',
            'email' => 'chidi@example.com',
            'role_id' => null,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Bolaji Seller',
            'business_name' => 'Bolaji Auto Spares',
            'email' => 'bolaji@example.com',
            'role_id' => null,
        ]);

        $this->thirdParty = User::factory()->create([
            'name' => 'Tunde Other',
            'email' => 'tunde@example.com',
            'role_id' => null,
        ]);

        $category = Category::firstOrCreate(['slug' => 'alternators-test'], [
            'name' => 'Alternators Test',
        ]);

        $deviceModel = \App\Models\DeviceModel::firstOrCreate(['slug' => 'toyota-camry-2018-model'], [
            'category_id' => $category->id,
            'name' => 'Toyota Camry 2018 Alternator OEM',
        ]);

        $this->item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $deviceModel->id,
            'name' => 'Toyota Camry 2018 Alternator OEM',
            'description' => 'Tested original working alternator.',
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $this->item->id,
            'category_id' => $category->id,
            'price' => 120000,
            'quantity' => 2,
            'status' => 'approved',
            'is_active' => true,
            'is_published' => true,
        ]);
    }

    public function test_offers_list_top_filter_contains_only_all_sent_received(): void
    {
        $this->actingAs($this->buyer);

        $response = $this->get(route('offers'));
        $response->assertStatus(200);

        // Top filter elements
        $response->assertSee('wire:click="setDirection(\'all\')"', false);
        $response->assertSee('wire:click="setDirection(\'sent\')"', false);
        $response->assertSee('wire:click="setDirection(\'received\')"', false);

        // Should NOT have accepted or declined as top direction tabs
        $response->assertDontSee('wire:click="setDirection(\'accepted\')"', false);
        $response->assertDontSee('wire:click="setDirection(\'declined\')"', false);

        // Secondary status filter elements are present
        $response->assertSee('wire:click="setStatus(\'accepted\')"', false);
        $response->assertSee('wire:click="setStatus(\'pending\')"', false);
        $response->assertSee('wire:click="setStatus(\'countered\')"', false);
        $response->assertSee('wire:click="setStatus(\'declined\')"', false);
    }

    public function test_offers_list_filters_by_direction_and_status_dynamically(): void
    {
        $negotiationService = app(NegotiationService::class);

        // 1. Offer sent by Buyer to Seller (pending)
        $offerSentPending = $negotiationService->createOfferFromListing($this->buyer, $this->listing, [
            'price' => 110000,
            'discount' => 10000,
            'terms' => 'Can you do 110k?',
        ]);

        // 2. Offer sent by ThirdParty to Buyer (received by Buyer, pending)
        $offerReceivedPending = Offer::create([
            'sender_id' => $this->thirdParty->id,
            'recipient_id' => $this->buyer->id,
            'status' => 'pending',
            'discount' => 0,
            'terms' => 'Offer to buy your old unit',
        ]);
        OfferItem::create([
            'offer_id' => $offerReceivedPending->id,
            'description' => 'Scrap Core Unit',
            'unit_price' => 15000,
            'quantity' => 1,
            'type' => 'item',
        ]);

        // 3. Offer sent by Buyer that was accepted
        $offerAccepted = Offer::create([
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller->id,
            'status' => 'accepted',
            'discount' => 5000,
            'terms' => 'Accepted deal for brake pads',
        ]);
        OfferItem::create([
            'offer_id' => $offerAccepted->id,
            'description' => 'Ceramic Brake Pads',
            'unit_price' => 35000,
            'quantity' => 1,
            'type' => 'item',
        ]);

        $this->actingAs($this->buyer);

        // Test Livewire component state
        Livewire::test(\App\Livewire\Dashboard\Offers\OffersList::class)
            // Initial 'all' direction
            ->assertSet('activeTab', 'all')
            ->assertSee('Toyota Camry 2018 Alternator OEM')
            ->assertSee('Scrap Core Unit')
            ->assertSee('Ceramic Brake Pads')
            // Switch to 'sent'
            ->call('setDirection', 'sent')
            ->assertSet('activeTab', 'sent')
            ->assertSee('Toyota Camry 2018 Alternator OEM')
            ->assertSee('Ceramic Brake Pads')
            ->assertDontSee('Scrap Core Unit')
            // Switch to 'received'
            ->call('setDirection', 'received')
            ->assertSet('activeTab', 'received')
            ->assertSee('Scrap Core Unit')
            ->assertDontSee('Toyota Camry 2018 Alternator OEM')
            // Switch to accepted status filter under 'all'
            ->call('setDirection', 'all')
            ->call('setStatus', 'accepted')
            ->assertSee('Ceramic Brake Pads')
            ->assertDontSee('Scrap Core Unit')
            ->assertDontSee('Toyota Camry 2018 Alternator OEM');
    }

    public function test_user_can_view_full_negotiation_timeline_and_decline(): void
    {
        $negotiationService = app(NegotiationService::class);

        // Round 1: Buyer offers 100k
        $round1 = $negotiationService->createOfferFromListing($this->buyer, $this->listing, [
            'price' => 100000,
            'terms' => 'Offering 100,000 NGN',
        ]);

        // Round 2: Seller counters at 115k
        $round2 = $negotiationService->submitCounterOffer($this->seller, $round1, [
            'price' => 115000,
            'terms' => 'Cannot go lower than 115,000 NGN',
        ]);

        // Buyer logs in and views Round 2
        $this->actingAs($this->buyer);

        $test = Livewire::test(\App\Livewire\Dashboard\Offers\OfferView::class, ['offer_id' => 'OFF-' . $round2->id]);
        $test->assertStatus(200);

        // Should see both rounds in timeline
        $rounds = $test->viewData('rounds');
        $this->assertCount(2, $rounds);
        $this->assertEquals(1, $rounds[0]['round_number']);
        $this->assertEquals(2, $rounds[1]['round_number']);
        $this->assertTrue($rounds[1]['is_current']);

        // Buyer declines the counter offer
        $test->call('declineOffer');
        $this->assertEquals('declined', $round2->fresh()->status);
    }

    public function test_offer_negotiations_page_lists_sessions_with_dynamic_data(): void
    {
        $negotiationService = app(NegotiationService::class);

        // Create a 2-round negotiation
        $round1 = $negotiationService->createOfferFromListing($this->buyer, $this->listing, [
            'price' => 105000,
            'terms' => 'Negotiation initial proposal',
        ]);

        $round2 = $negotiationService->submitCounterOffer($this->seller, $round1, [
            'price' => 112000,
            'terms' => 'Counter proposal from seller',
        ]);

        $this->actingAs($this->buyer);

        $response = $this->get(route('negotiations'));
        $response->assertStatus(200);
        $response->assertSee('Active Deal Negotiations');
        $response->assertSee('Toyota Camry 2018 Alternator OEM');
        $response->assertSee('2 Rounds');
        $response->assertSee('YOUR TURN TO RESPOND');
    }
}
