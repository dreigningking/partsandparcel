<?php

namespace Tests\Feature;

use App\Livewire\Components\Offers\MakeOffer;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature Test for Livewire 3 Component: MakeOffer.
 *
 * Demonstrates Livewire Component Testing:
 * 1. Testing initial component state without a browser.
 * 2. Simulating event listeners (dispatching events to component).
 * 3. Simulating user form inputs (set, call).
 * 4. Testing business logic actions and redirects.
 */
class LivewireOfferComponentTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::firstOrCreate(['email' => 'livewire_buyer@example.com'], [
            'name' => 'Livewire Buyer',
            'password' => bcrypt('password123'),
        ]);

        $this->seller = User::firstOrCreate(['email' => 'livewire_seller@example.com'], [
            'name' => 'Livewire Seller',
            'business_name' => 'Super Parts Tech',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::firstOrCreate(['slug' => 'test-livewire-cat'], ['name' => 'Test Hardware']);
        $model = DeviceModel::firstOrCreate(['slug' => 'thinkpad-t480-livewire'], [
            'category_id' => $category->id,
            'name' => 'ThinkPad T480',
        ]);
        $item = Item::firstOrCreate(['serial_number' => 'SN-LW-16GB-RAM'], [
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::firstOrCreate(['slug' => 'original-16gb-ram-livewire'], [
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'description' => 'Original 16GB DDR4 RAM Module',
            'price' => 50000.00,
            'currency' => 'NGN',
            'quantity' => 2,
            'status' => 'active',
            'is_negotiable' => true,
            'is_warranty_negotiable' => true,
            'allow_shipping' => true,
        ]);
        $this->listing->update([
            'is_negotiable' => true,
            'is_warranty_negotiable' => true,
            'allow_shipping' => true,
        ]);
    }

    /**
     * Test 1: Component initializes in a closed drawer state.
     */
    public function test_make_offer_component_initializes_closed(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->assertSet('isOpen', false)
            ->assertSet('deliveryMode', 'pickup')
            ->assertSet('warrantyDays', 14);
    }

    /**
     * Test 2: Dispatching 'open-make-offer' event opens the drawer and loads listing.
     */
    public function test_open_event_populates_listing_details_and_opens_drawer(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->assertSet('isOpen', true)
            ->assertSet('sellerId', (string) $this->seller->id)
            ->assertSet('sellerName', 'Super Parts Tech')
            ->assertSet('proposedPrice', '50000.00')
            ->assertSee('Original 16GB DDR4 RAM Module');
    }

    /**
     * Test 3: Changing warranty days updates component state and terms text.
     */
    public function test_setting_warranty_days_updates_terms(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->call('setWarrantyDays', 30)
            ->assertSet('warrantyDays', 30)
            ->assertSet('warrantyTerms', '30-day inspection and replacement warranty');
    }

    /**
     * Test 4: Submitting an offer creates the offer and redirects to offers dashboard.
     */
    public function test_submitting_offer_persists_negotiation_and_redirects(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->set('proposedPrice', '42000')
            ->set('offerNote', 'Can you do 42,000 for quick pickup today?')
            ->call('submitPackageOffer')
            ->assertSessionHas('message')
            ->assertRedirect(route('offers'));

        // Verify database persistence using sender_id and recipient_id
        $this->assertDatabaseHas('offers', [
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test 5: Non-negotiable listing locks proposed price and sets discount to 0 on submit.
     */
    public function test_non_negotiable_listing_locks_price_and_discount(): void
    {
        $this->listing->update(['is_negotiable' => false]);

        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->assertSet('isNegotiable', false)
            ->assertSee('Fixed Price')
            ->set('proposedPrice', '30000') // attempt to discount
            ->call('submitPackageOffer')
            ->assertRedirect(route('offers'));

        $this->assertDatabaseHas('offers', [
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller->id,
            'discount' => 0,
        ]);
    }

    /**
     * Test 6: Disallowed shipping forces pickup mode.
     */
    public function test_disallowed_shipping_forces_pickup_mode(): void
    {
        $this->listing->update(['allow_shipping' => false]);

        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->assertSet('allowShipping', false)
            ->assertSet('deliveryMode', 'pickup')
            ->assertSee('Local Pickup Only')
            ->set('deliveryMode', 'seller_delivery')
            ->call('submitPackageOffer');

        $this->assertDatabaseHas('offers', [
            'sender_id' => $this->buyer->id,
            'recipient_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
        ]);
    }

    /**
     * Test 7: Fixed warranty terms prevent changing warranty days.
     */
    public function test_fixed_warranty_prevents_warranty_changes(): void
    {
        $this->listing->update([
            'is_warranty_negotiable' => false,
            'warranty_period_days' => 10,
            'warranty_terms' => '10 days seller testing warranty',
        ]);

        Livewire::actingAs($this->buyer)
            ->test(MakeOffer::class)
            ->dispatch('open-make-offer', ['listing_id' => $this->listing->id])
            ->assertSet('isWarrantyNegotiable', false)
            ->assertSet('warrantyDays', 10)
            ->call('setWarrantyDays', 30) // should be ignored
            ->assertSet('warrantyDays', 10)
            ->assertSee('Warranty terms are fixed by seller');
    }
}
