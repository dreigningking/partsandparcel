<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\Response;
use App\Models\User;
use App\Services\Commercial\NegotiationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommunityRequestsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $requester;
    protected User $vendor;
    protected User $outsider;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requester = User::firstOrCreate(['email' => 'requester_test@example.com'], [
            'name' => 'TechSam Requester',
            'password' => bcrypt('password123'),
        ]);

        $this->vendor = User::firstOrCreate(['email' => 'vendor_test@example.com'], [
            'name' => 'Abel Electronics Vendor',
            'password' => bcrypt('password123'),
        ]);

        $this->outsider = User::firstOrCreate(['email' => 'outsider_test@example.com'], [
            'name' => 'Random Bystander',
            'password' => bcrypt('password123'),
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'electronics-comm-test'], [
            'name' => 'Electronics',
        ]);
    }

    public function test_user_can_create_community_request(): void
    {
        $this->actingAs($this->requester);

        \Livewire\Livewire::test(\App\Livewire\Marketplace\Community\CommunityHome::class)
            ->set('formType', 'Product / Part')
            ->set('formCategory', $this->category->name)
            ->set('formTitle', 'Need HP EliteBook 840 G5 Motherboard urgently')
            ->set('formDesc', 'Tested working motherboard needed in Computer Village Ikeja.')
            ->set('formLocation', 'Ikeja, Lagos')
            ->set('formBudget', '₦80,000')
            ->call('submitRequest');

        $this->assertDatabaseHas('discussions', [
            'user_id' => $this->requester->id,
            'title' => 'Need HP EliteBook 840 G5 Motherboard urgently',
            'type' => 'item',
            'status' => 'open',
        ]);
    }

    public function test_vendor_can_submit_response_with_private_offer(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Need HP EliteBook 840 G5 Motherboard',
            'body' => 'Tested motherboard needed.',
            'status' => 'open',
        ]);

        $this->actingAs($this->vendor);

        \Livewire\Livewire::test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id])
            ->set('responseText', 'I have a clean pull board available at our shop in Computer Village.')
            ->set('isOfferActive', true)
            ->set('composerOfferPrice', '80000')
            ->set('composerOfferWarranty', '14 days')
            ->set('composerOfferDelivery', 'Buyer pickup')
            ->set('composerOfferMessage', 'Tested 100% working. Bring your laptop.')
            ->call('submitResponse');

        // Response in DB
        $this->assertDatabaseHas('responses', [
            'discussion_id' => $discussion->id,
            'user_id' => $this->vendor->id,
            'body' => 'I have a clean pull board available at our shop in Computer Village.',
        ]);

        $response = Response::where('discussion_id', $discussion->id)->where('user_id', $this->vendor->id)->first();

        // Offer in DB linked to response
        $this->assertDatabaseHas('offers', [
            'discussion_id' => $discussion->id,
            'response_id' => $response->id,
            'sender_id' => $this->vendor->id,
            'recipient_id' => $this->requester->id,
            'delivery_method' => 'buyer_responsible',
            'status' => 'pending',
        ]);

        $offer = Offer::where('response_id', $response->id)->first();
        $this->assertEquals(80000.00, (float) $offer->items->first()->unit_price);
        $this->assertEquals(14, $offer->items->first()->warranty_period_days);
    }

    public function test_private_offers_are_only_visible_to_participants(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Private Offer Visibility Test',
            'body' => 'Testing offer privacy between requester and responder.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->vendor->id,
            'body' => 'Here is my public comment response.',
            'status' => 'visible',
        ]);

        app(NegotiationService::class)->createOfferFromResponse($this->vendor, $discussion->id, $response->id, [
            'price' => 75000.00,
            'warranty_days' => 14,
            'delivery_method' => 'pickup',
            'message' => 'Confidential vendor pricing',
        ]);

        // 1. Requester sees the negotiation
        $this->actingAs($this->requester);
        $component1 = \Livewire\Livewire::test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id]);
        $responses1 = $component1->get('responses');
        $this->assertNotEmpty($responses1[0]['negotiation']);
        $this->assertEquals('₦75,000', $responses1[0]['negotiation'][0]['price']);

        // 2. Vendor who responded sees the negotiation
        $this->actingAs($this->vendor);
        $component2 = \Livewire\Livewire::test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id]);
        $responses2 = $component2->get('responses');
        $this->assertNotEmpty($responses2[0]['negotiation']);
        $this->assertEquals('₦75,000', $responses2[0]['negotiation'][0]['price']);

        // 3. Random outsider CANNOT see the negotiation!
        $this->actingAs($this->outsider);
        $component3 = \Livewire\Livewire::test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id]);
        $responses3 = $component3->get('responses');
        $this->assertEmpty($responses3[0]['negotiation']);
    }

    public function test_accepting_community_offer_generates_invoice(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->requester->id,
            'category_id' => $this->category->id,
            'type' => 'item',
            'title' => 'Invoice Generation from Offer Test',
            'body' => 'Looking for part.',
            'status' => 'open',
        ]);

        $response = Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $this->vendor->id,
            'body' => 'I have it.',
            'status' => 'visible',
        ]);

        $offer = app(NegotiationService::class)->createOfferFromResponse($this->vendor, $discussion->id, $response->id, [
            'price' => 80000.00,
            'warranty_days' => 30,
            'delivery_method' => 'seller_delivery',
            'message' => 'Will deliver to you.',
        ]);

        // Requester accepts offer
        $invoice = app(NegotiationService::class)->acceptOffer($this->requester, $offer);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('accepted', $offer->fresh()->status);
        $this->assertEquals('issued', $invoice->status);
        $this->assertEquals($this->requester->id, $invoice->buyer_id);
        $this->assertEquals($this->vendor->id, $invoice->seller_id);
        $this->assertEquals(80000.00, (float) $invoice->total);
        $this->assertEquals('seller_responsible', $invoice->delivery_method);
        $this->assertEquals(30, $invoice->items->first()->warranty_period_days);
    }
}
