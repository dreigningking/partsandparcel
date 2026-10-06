<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDiscussions;
use App\Livewire\Admin\AdminDiscussionView;
use App\Livewire\Admin\AdminListingDetails;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Location;
use App\Models\Media;
use App\Models\Moderation;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Promotion;
use App\Models\Report;
use App\Models\Response;
use App\Models\Role;
use App\Models\User;
use App\Models\ViewedEntity;
use App\Models\Watchlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDiscussionViewAndListingReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected User $reporter;
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
            'name' => 'Chidi Anozie',
            'email' => 'chidi@example.com',
        ]);

        $this->reporter = User::factory()->create([
            'name' => 'Sani Dangote',
            'email' => 'sani@example.com',
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'auto-parts'], [
            'name' => 'Auto Parts',
        ]);

        $this->brand = Brand::firstOrCreate(['slug' => 'toyota'], [
            'name' => 'Toyota',
        ]);

        $this->model = DeviceModel::firstOrCreate(['slug' => 'camry-2018'], [
            'name' => 'Camry 2018',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $this->discussion = Discussion::create([
            'user_id' => $this->member->id,
            'type' => 'item',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'title' => 'Toyota Camry 2018 Headlight Assembly Required',
            'body' => 'Need genuine OEM LED headlight assembly for passenger side.',
            'budget' => '120000',
            'status' => 'open',
            'attachments' => [
                'fulfillment' => 'Delivery',
                'urgency' => 'Urgent',
            ],
        ]);
    }

    public function test_can_navigate_to_discussion_view_from_discussions_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.discussions'));
        $response->assertStatus(200);
        $response->assertSee(route('admin.discussions.view', $this->discussion->id));
    }

    public function test_admin_discussion_view_renders_details_and_moderation_controls(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDiscussionView::class, ['discussion' => $this->discussion]);

        // Details assertions
        $component->assertSee('Toyota Camry 2018 Headlight Assembly Required')
            ->assertSee('Need genuine OEM LED headlight assembly for passenger side.')
            ->assertSee('Chidi Anozie')
            ->assertSee('Auto Parts')
            ->assertSee('Toyota')
            ->assertSee('Camry 2018')
            ->assertSee('120,000')
            ->assertSee('Delivery')
            ->assertSee('Urgent')
            ->assertSee('Approve Topic')
            ->assertSee('Reject / Flag');

        // Test approve
        $component->call('approve');
        $this->assertDatabaseHas('moderations', [
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $this->discussion->id,
            'status' => 'approved',
        ]);

        // Test toggle pin and lock
        $component->call('togglePin');
        $this->discussion->refresh();
        $this->assertTrue($this->discussion->is_pinned);

        $component->call('toggleLock');
        $this->discussion->refresh();
        $this->assertTrue($this->discussion->is_locked);

        // Test reject
        $component->set('rejectionReason', 'Duplicate request in community.')
            ->call('submitReject');
        $this->discussion->refresh();
        $this->assertEquals('closed', $this->discussion->status);
        $this->assertDatabaseHas('moderations', [
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $this->discussion->id,
            'status' => 'rejected',
        ]);
    }

    public function test_admin_discussion_view_displays_and_moderates_reports(): void
    {
        $report = $this->discussion->reports()->create([
            'user_id' => $this->reporter->id,
            'title' => 'Inappropriate Contact Details',
            'description' => 'User posted off-platform whatsapp numbers.',
            'status' => 'pending',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AdminDiscussionView::class, ['discussion' => $this->discussion]);

        $component->assertSee('Discussion Flagged: 1 Unresolved Report(s) Filed')
            ->assertSee('Inappropriate Contact Details')
            ->assertSee('User posted off-platform whatsapp numbers.')
            ->assertSee('Sani Dangote');

        // Resolve report
        $component->call('resolveDiscussionReport', $report->id, 'Reviewed and numbers removed.');
        $report->refresh();
        $this->assertEquals('resolved', $report->status);
        $this->assertEquals($this->admin->id, $report->resolved_by);
    }

    public function test_admin_discussion_view_displays_responses_and_reported_responses(): void
    {
        // Normal response
        $normalResponse = Response::create([
            'discussion_id' => $this->discussion->id,
            'user_id' => $this->member->id,
            'body' => 'Still looking for this part.',
            'status' => 'published',
        ]);

        // Reported response
        $reportedResponse = Response::create([
            'discussion_id' => $this->discussion->id,
            'user_id' => $this->reporter->id,
            'body' => 'Call me directly for illegal smuggled parts.',
            'status' => 'published',
        ]);

        $respReport = $reportedResponse->reports()->create([
            'user_id' => $this->member->id,
            'title' => 'Prohibited Goods Offer',
            'description' => 'Vendor advertising contraband goods.',
            'status' => 'pending',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AdminDiscussionView::class, ['discussion' => $this->discussion]);

        // Number of responses & reported responses alert
        $component->assertSee('2 replies')
            ->assertSee('Reported Responses: 1 Community Reply(ies) Flagged')
            ->call('setTab', 'responses')
            ->assertSee('Still looking for this part.')
            ->assertSee('Call me directly for illegal smuggled parts.')
            ->assertSee('Prohibited Goods Offer')
            ->assertSee('Vendor advertising contraband goods.');

        // Admin resolves response report
        $component->call('resolveResponseReport', $respReport->id, 'Report validated.');
        $respReport->refresh();
        $this->assertEquals('resolved', $respReport->status);

        // Admin deletes the offensive response
        $component->call('deleteResponse', $reportedResponse->id);
        $this->assertDatabaseMissing('responses', ['id' => $reportedResponse->id]);
    }

    public function test_admin_listing_details_shows_reports_and_moderation_controls(): void
    {
        $item = Item::create([
            'user_id' => $this->member->id,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'name' => 'Toyota Camry Alternator',
            'item_type' => 'part',
            'condition_status' => 'used',
        ]);

        $listing = Listing::create([
            'user_id' => $this->member->id,
            'item_id' => $item->id,
            'title' => 'Genuine Denso Alternator 12V',
            'slug' => 'genuine-denso-alternator-12v-' . uniqid(),
            'price' => 45000,
            'quantity' => 2,
            'is_published' => false,
            'is_active' => true,
        ]);

        $report = $listing->reports()->create([
            'user_id' => $this->reporter->id,
            'title' => 'Counterfeit Brand',
            'description' => 'This is a clone alternator labelled as genuine Denso.',
            'status' => 'pending',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $listing]);

        // Verifying report banner and card
        $component->assertSee('Flagged Item: 1 Unresolved Report(s) Filed')
            ->assertSee('User Reports & Flags')
            ->assertSee('Counterfeit Brand')
            ->assertSee('This is a clone alternator labelled as genuine Denso.')
            ->assertSee('Sani Dangote')
            ->assertSee('Approve Listing')
            ->assertSee('Reject / Flag');

        // Admin resolves report
        $component->call('resolveReport', $report->id, 'Inspected batch photos.');
        $report->refresh();
        $this->assertEquals('resolved', $report->status);

        // Admin approves listing
        $component->call('approve');
        $listing->refresh();
        $this->assertTrue((bool) $listing->is_published);
    }

    public function test_admin_listing_details_represents_all_thirteen_models_and_tabbed_workspace(): void
    {
        $country = \App\Models\Country::firstOrCreate(['code' => 'NG'], [
            'name' => 'Nigeria',
            'phone_code' => '234',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
        ]);

        // 1. Location
        $location = Location::create([
            'user_id' => $this->member->id,
            'country_id' => $country->id,
            'label' => 'Ikeja Central Auto Hub',
            'contact_name' => 'Emeka Okafor',
            'phone' => '+2348012345678',
            'address_line_1' => 'Plot 14 Commercial Avenue',
            'city' => 'Ikeja',
            'postal_code' => '100001',
        ]);

        // 2. Item with DeviceModel, Brand, Category, Location
        $item = Item::create([
            'user_id' => $this->member->id,
            'location_id' => $location->id,
            'model_id' => $this->model->id,
            'name' => 'High Output Alternator 150A',
            'item_type' => 'part',
            'condition_status' => 'refurbished',
            'condition_notes' => 'Bench tested at 14.4V with genuine voltage regulator.',
            'description' => 'Heavy duty alternator suitable for Camry 2018 luxury trims.',
            'year' => 2018,
        ]);

        // 3. Media
        $media = Media::create([
            'mediable_type' => 'item',
            'mediable_id' => $item->id,
            'collection' => 'default',
            'file_name' => 'alternator_front.jpg',
            'file_path' => 'listings/alternator_front.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => 102400,
        ]);

        // 4. Listing
        $listing = Listing::create([
            'user_id' => $this->member->id,
            'item_id' => $item->id,
            'title' => 'High Output Alternator 150A - Camry',
            'slug' => 'high-output-alternator-150a-camry-' . uniqid(),
            'price' => 85000,
            'quantity' => 5,
            'is_published' => false,
            'is_active' => true,
            'is_negotiable' => true,
            'allow_shipping' => true,
            'warranty_period_days' => 90,
            'warranty_terms' => 'Covers armature failure and regulator defect.',
        ]);

        // 5. Moderation
        $moderation = Moderation::create([
            'moderatable_type' => 'listing',
            'moderatable_id' => $listing->id,
            'status' => 'pending',
            'action' => 'submitted_for_review',
        ]);

        // 6. ViewedEntity
        ViewedEntity::create([
            'viewable_type' => 'listing',
            'viewable_id' => $listing->id,
            'user_id' => $this->reporter->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'device_type' => 'desktop',
        ]);

        // 7. Invoice & InvoiceItem
        $invoice = Invoice::create([
            'buyer_id' => $this->reporter->id,
            'seller_id' => $this->member->id,
            'invoice_number' => 'INV-TEST-0085',
            'subtotal' => 85000,
            'total' => 85000,
            'status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => 'listing',
            'itemable_id' => $listing->id,
            'type' => 'item',
            'description' => 'High Output Alternator 150A',
            'quantity' => 1,
            'unit_price' => 85000,
            'amount' => 85000,
        ]);

        // 8. ListingReview
        ListingReview::create([
            'listing_id' => $listing->id,
            'user_id' => $this->reporter->id,
            'rating' => 5,
            'comment' => 'Excellent alternator, charges perfectly without voltage drop.',
        ]);

        // 9. Promotion
        Promotion::create([
            'user_id' => $this->member->id,
            'listing_id' => $listing->id,
            'type' => 'clicks',
            'achieved_count' => 1250,
            'status' => 'active',
        ]);

        // 10. Report
        $report = $listing->reports()->create([
            'user_id' => $this->reporter->id,
            'title' => 'Incorrect Year Range Specified',
            'description' => 'This part fits up to 2017 models, check bracket mounts.',
            'status' => 'pending',
        ]);

        // Test component rendering with Option A tabbed workspace
        $component = Livewire::actingAs($this->admin)
            ->test(AdminListingDetails::class, ['listing' => $listing]);

        // Assert TOP KPI RIBBON contains Moderation status and inline moderation action buttons
        $component->assertSee('Current Moderation Status')
            ->assertSee('Pending Review')
            ->assertSee('Approve Listing')
            ->assertSee('Reject / Flag')
            ->assertSee('Marketplace Views')
            ->assertSee('Times Sold')
            ->assertSee('Gross Sales')
            ->assertSee('Buyer Rating')
            ->assertSee('85,000');

        // Assert TAB 1 (Overview & Specs) renders Brand, Category, DeviceModel, Item, Location
        $component->assertSee('Overview & Specs')
            ->assertSee('High Output Alternator 150A')
            ->assertSee('Toyota')
            ->assertSee('Camry 2018')
            ->assertSee('Auto Parts')
            ->assertSee('Ikeja Central Auto Hub')
            ->assertSee('Plot 14 Commercial Avenue')
            ->assertSee('Emeka Okafor')
            ->assertSee('Bench tested at 14.4V with genuine voltage regulator.')
            ->assertSee('90 Days')
            ->assertSee('Covers armature failure and regulator defect.');

        // Test TAB 2: Sales & Invoices (InvoiceItem)
        $component->call('setTab', 'sales');
        $component->assertSee('INV-TEST-0085')
            ->assertSee('Sani Dangote')
            ->assertSee('85,000.00');

        // Test TAB 3: Reviews & Ratings (ListingReview)
        $component->call('setTab', 'reviews');
        $component->assertSee('5.0')
            ->assertSee('Satisfaction Rating Breakdown')
            ->assertSee('Excellent alternator, charges perfectly without voltage drop.')
            ->assertSee('Sani Dangote');

        // Test TAB 4: Trust, Reports & Moderation (Moderation, Promotion, Report, Seller)
        $component->call('setTab', 'trust');
        $component->assertSee('Incorrect Year Range Specified')
            ->assertSee('This part fits up to 2017 models, check bracket mounts.')
            ->assertSee('Promotional Campaigns & Boosts')
            ->assertSee('1,250')
            ->assertSee('Moderation Audit Trail')
            ->assertSee('Merchant Account Profile')
            ->assertSee('Chidi Anozie');

        // Test moderation approve directly from Top KPI ribbon
        $component->call('approve');
        $listing->refresh();
        $this->assertTrue((bool) $listing->is_published);
        $this->assertDatabaseHas('moderations', [
            'moderatable_type' => Listing::class,
            'moderatable_id' => $listing->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_discussion_view_represents_all_eleven_models_and_tabbed_workspace(): void
    {
        // 1. Discussion with Brand, Category, DeviceModel
        $disc = Discussion::create([
            'user_id' => $this->member->id,
            'type' => 'item',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'title' => 'Toyota Camry 2018 Radiator Core Required',
            'body' => 'Seeking aluminum double-row replacement radiator with transmission cooler lines.',
            'budget' => '95000',
            'status' => 'open',
            'attachments' => [
                'fulfillment' => 'Delivery',
                'urgency' => 'Immediate',
            ],
        ]);

        // 2. Media: image, video, and doc
        Media::create([
            'mediable_type' => 'discussion',
            'mediable_id' => $disc->id,
            'collection' => 'default',
            'file_name' => 'radiator_fitment.jpg',
            'file_path' => 'discussions/radiator_fitment.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'size' => 102400,
        ]);

        Media::create([
            'mediable_type' => 'discussion',
            'mediable_id' => $disc->id,
            'collection' => 'default',
            'file_name' => 'leak_test.mp4',
            'file_path' => 'discussions/leak_test.mp4',
            'disk' => 'public',
            'mime_type' => 'video/mp4',
            'media_type' => 'video',
            'size' => 500000,
        ]);

        Media::create([
            'mediable_type' => 'discussion',
            'mediable_id' => $disc->id,
            'collection' => 'default',
            'file_name' => 'camry_radiator_spec.pdf',
            'file_path' => 'discussions/camry_radiator_spec.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'media_type' => 'document',
            'size' => 204800,
        ]);

        // 3. ViewedEntity (views)
        ViewedEntity::create([
            'viewable_type' => 'discussion',
            'viewable_id' => $disc->id,
            'user_id' => $this->reporter->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'device_type' => 'desktop',
        ]);

        // 4. Watchlist (watchers)
        Watchlist::create([
            'user_id' => $this->reporter->id,
            'watchable_type' => 'discussion',
            'watchable_id' => $disc->id,
        ]);

        // 5. Response & Response.Offer
        $response = Response::create([
            'discussion_id' => $disc->id,
            'user_id' => $this->reporter->id,
            'body' => 'I have an OEM Koyo aluminum radiator ready to ship immediately.',
            'status' => 'published',
        ]);

        $offer = Offer::create([
            'discussion_id' => $disc->id,
            'response_id' => $response->id,
            'sender_id' => $this->reporter->id,
            'recipient_id' => $this->member->id,
            'delivery_method' => 'seller_responsible',
            'terms' => '12 months warranty on core seals.',
            'status' => 'pending',
        ]);

        OfferItem::create([
            'offer_id' => $offer->id,
            'description' => 'Koyo Aluminum Radiator Core 2-Row',
            'quantity' => 1,
            'unit_price' => 92000,
            'amount' => 92000,
        ]);

        // 6. Moderation
        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $disc->id,
            'status' => 'pending',
            'action' => 'submitted_for_review',
        ]);

        // 7. Report
        $report = $disc->reports()->create([
            'user_id' => $this->reporter->id,
            'title' => 'Incorrect Part Number Quoted',
            'description' => 'Check whether core inlet matches transmission cooler fittings.',
            'status' => 'pending',
        ]);

        // Test component rendering
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDiscussionView::class, ['discussion' => $disc]);

        // Assert TOP KPI RIBBON contains Moderation status and inline moderation action buttons
        $component->assertSee('Current Moderation Status')
            ->assertSee('Pending Review')
            ->assertSee('Approve Topic')
            ->assertSee('Reject / Flag')
            ->assertSee('Discussion Views')
            ->assertSee('Watchers')
            ->assertSee('Responses')
            ->assertSee('Vendor Quotes')
            ->assertSee('1 with commercial offers');

        // Assert TAB 1: Overview & Specs (Discussion, Brand, Category, DeviceModel, Media)
        $component->assertSee('Overview & Specs')
            ->assertSee('Toyota Camry 2018 Radiator Core Required')
            ->assertSee('Toyota')
            ->assertSee('Camry 2018')
            ->assertSee('Auto Parts')
            ->assertSee('95,000')
            ->assertSee('Immediate')
            ->assertSee('Attached Media & Files (3)')
            ->assertSee('1 Images')
            ->assertSee('1 Videos')
            ->assertSee('1 Docs')
            ->assertSee('leak_test.mp4')
            ->assertSee('camry_radiator_spec.pdf');

        // Assert TAB 2: Responses & Commercial Proposals (Response, Response.Offer)
        $component->call('setTab', 'responses');
        $component->assertSee('I have an OEM Koyo aluminum radiator ready to ship immediately.')
            ->assertSee('Commercial Offer')
            ->assertSee('Koyo Aluminum Radiator Core 2-Row')
            ->assertSee('92,000')
            ->assertSee('seller_responsible');

        // Assert TAB 3: Trust, Reports & Moderation (Moderation, Report, User)
        $component->call('setTab', 'trust');
        $component->assertSee('Incorrect Part Number Quoted')
            ->assertSee('Moderation Audit Trail')
            ->assertSee('Request Author Profile')
            ->assertSee('Chidi Anozie');

        // Test moderation approve directly from Top KPI ribbon
        $component->call('approve');
        $disc->refresh();
        $this->assertEquals('open', $disc->status);
        $this->assertDatabaseHas('moderations', [
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $disc->id,
            'status' => 'approved',
        ]);
    }
}
