<?php

namespace Tests\Feature;

use App\Livewire\Components\Messaging\ConversationDrawer;
use App\Livewire\Components\Messaging\MessageDrawer;
use App\Livewire\Components\Offers\CounterOfferDrawer;
use App\Livewire\Components\Offers\ListingOfferDrawer;
use App\Livewire\Components\Offers\QuickViewOffers;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InstantDrawersTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Listing $listing;
    protected Conversation $conversation;
    protected Offer $offer;
    protected Response $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Drawer Buyer',
            'email' => 'drawer_buyer@example.com',
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Drawer Seller',
            'business_name' => 'Drawer Parts Hub',
            'email' => 'drawer_seller@example.com',
        ]);

        $category = Category::firstOrCreate(['slug' => 'test-hardware'], ['name' => 'Hardware']);
        $model = DeviceModel::firstOrCreate(['slug' => 'thinkpad-x1'], [
            'category_id' => $category->id,
            'name' => 'ThinkPad X1',
        ]);
        $item = Item::firstOrCreate(['serial_number' => 'SN-DRAWER-TEST'], [
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'slug' => 'thinkpad-x1-motherboard',
            'description' => 'Tested ThinkPad Motherboard',
            'price' => 75000.00,
            'currency' => 'NGN',
            'quantity' => 2,
            'status' => 'active',
            'is_negotiable' => true,
            'is_warranty_negotiable' => true,
            'allow_shipping' => true,
        ]);

        $this->conversation = Conversation::create([
            'created_by' => $this->buyer->id,
            'title' => 'Inquiry on ThinkPad Motherboard',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->buyer->id,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->seller->id,
        ]);

        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Hello seller, is this board available?',
        ]);

        $discussion = Discussion::create([
            'user_id' => $this->buyer->id,
            'title' => 'Need ThinkPad Board',
            'body' => 'Looking for ThinkPad motherboard',
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        $this->response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->seller->id,
            'content' => 'I have 2 units available for pickup',
        ]);

        $this->offer = Offer::create([
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller->id,
            'discussion_id' => $discussion->id,
            'response_id' => $this->response->id,
            'status' => 'pending',
            'delivery_method' => 'buyer_responsible',
        ]);

        OfferItem::create([
            'offer_id' => $this->offer->id,
            'listing_id' => $this->listing->id,
            'description' => 'ThinkPad X1 Motherboard',
            'original_price' => 75000.00,
            'proposed_price' => 70000.00,
            'quantity' => 1,
            'warranty_days' => 14,
            'status' => 'pending',
        ]);
    }

    public function test_message_drawer_opens_and_renders_conversations_with_alpine_shell(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(MessageDrawer::class)
            ->assertSet('isOpen', false)
            ->dispatch('open-message-drawer')
            ->assertSet('isOpen', true)
            ->assertSee('Drawer Parts Hub')
            ->assertSee('Hello seller, is this board available?')
            ->call('closeDrawer')
            ->assertSet('isOpen', false);
    }

    public function test_conversation_drawer_loads_and_closes_properly(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(ConversationDrawer::class)
            ->assertSet('isOpen', false)
            ->dispatch('open-conversation', id: $this->conversation->id)
            ->assertSet('isOpen', true)
            ->assertSet('recipientName', 'Drawer Parts Hub')
            ->assertSee('Hello seller, is this board available?')
            ->call('closeDrawer')
            ->assertSet('isOpen', false);
    }

    public function test_listing_offer_drawer_opens_via_events_and_closes(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(ListingOfferDrawer::class)
            ->assertSet('isOpen', false)
            ->dispatch('open-listing-offer', listing_id: $this->listing->id, seller_id: $this->seller->id)
            ->assertSet('isOpen', true)
            ->assertSet('listingId', $this->listing->id)
            ->assertSet('sellerId', $this->seller->id)
            ->assertSet('askingPrice', 75000.00)
            ->call('closeDrawer')
            ->assertSet('isOpen', false)
            ->assertSet('listingId', null);
    }

    public function test_counter_offer_drawer_opens_and_closes(): void
    {
        Livewire::actingAs($this->seller)
            ->test(CounterOfferDrawer::class)
            ->assertSet('isOpen', false)
            ->dispatch('open-counter-offer', offerId: $this->offer->id)
            ->assertSet('isOpen', true)
            ->assertSet('offerId', $this->offer->id)
            ->call('closeDrawer')
            ->assertSet('isOpen', false);
    }

    public function test_quick_view_offers_opens_and_closes(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(QuickViewOffers::class)
            ->assertSet('isOpen', false)
            ->dispatch('open-quick-view-offer', response_id: $this->response->id)
            ->assertSet('isOpen', true)
            ->call('closeDrawer')
            ->assertSet('isOpen', false);
    }
}
