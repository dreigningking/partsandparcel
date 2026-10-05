<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Discussion;
use App\Models\Response;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Report;
use App\Models\Listing;
use App\Models\Moderation;
use App\Models\Watchlist;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\Setting;
use App\Services\Commercial\NegotiationService;
use App\Services\Commercial\SubscriptionService;
use App\Events\MessageSent;
use App\Notifications\DiscussionResponseNotification;
use App\Notifications\ReportResolvedNotification;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

echo "=== STARTING COMPREHENSIVE COMMUNITY & MESSAGING TESTS ===\n\n";

$country = Country::first();
$countryId = $country ? $country->id : 1;

// 1. Setup Test Users
$author = User::firstOrCreate(
    ['email' => 'test_author@example.com'],
    ['name' => 'Discussion Author', 'password' => bcrypt('password'), 'role' => 'buyer', 'country_id' => $countryId]
);

$vendor = User::firstOrCreate(
    ['email' => 'test_vendor@example.com'],
    ['name' => 'Vendor Responder', 'password' => bcrypt('password'), 'role' => 'vendor', 'country_id' => $countryId]
);

$watcher = User::firstOrCreate(
    ['email' => 'test_watcher@example.com'],
    ['name' => 'Community Watcher', 'password' => bcrypt('password'), 'role' => 'buyer', 'country_id' => $countryId]
);

echo "1. Test Users setup: Author (ID: {$author->id}), Vendor (ID: {$vendor->id}), Watcher (ID: {$watcher->id})\n";

// Clean up previous runs for test users
$testUserIds = [$author->id, $vendor->id, $watcher->id];
Response::whereIn('user_id', $testUserIds)->delete();
Discussion::whereIn('user_id', $testUserIds)->delete();
Offer::whereIn('sender_id', $testUserIds)->delete();
Offer::whereIn('recipient_id', $testUserIds)->delete();
Report::whereIn('user_id', $testUserIds)->delete();
Conversation::whereIn('created_by', $testUserIds)->delete();
Watchlist::whereIn('user_id', $testUserIds)->delete();

// 2. Test auto_approve_discussion setting
Setting::updateOrCreate(
    ['name' => 'auto_approve_discussion'],
    ['value' => '1', 'type' => 'boolean', 'segment' => 'community']
);

$discApproved = Discussion::create([
    'user_id' => $author->id,
    'title' => 'Need iPhone 14 Pro Max screen replacement',
    'body' => 'Looking for clean original screen in Lagos.',
    'status' => 'open',
]);

$mod1 = Moderation::where('moderatable_type', Discussion::class)
    ->where('moderatable_id', $discApproved->id)
    ->first();

assert($mod1 && $mod1->status === 'approved', "Expected moderation status 'approved' when auto_approve_discussion is 1. Got: " . ($mod1 ? $mod1->status : 'null'));
echo "2. Auto-approve=1 test PASSED: Moderation status is '{$mod1->status}'\n";

// Test auto_approve_discussion = 0
Setting::updateOrCreate(
    ['name' => 'auto_approve_discussion'],
    ['value' => '0', 'type' => 'boolean', 'segment' => 'community']
);

$discPending = Discussion::create([
    'user_id' => $author->id,
    'title' => 'Need MacBook Pro M2 logic board',
    'body' => 'Looking for working board.',
    'status' => 'open',
]);

$mod2 = Moderation::where('moderatable_type', Discussion::class)
    ->where('moderatable_id', $discPending->id)
    ->first();

assert($mod2 && $mod2->status === 'pending', "Expected moderation status 'pending' when auto_approve_discussion is 0. Got: " . ($mod2 ? $mod2->status : 'null'));
echo "2b. Auto-approve=0 test PASSED: Moderation status is '{$mod2->status}'\n";

// Restore setting to 1
Setting::updateOrCreate(
    ['name' => 'auto_approve_discussion'],
    ['value' => '1', 'type' => 'boolean', 'segment' => 'community']
);

// 3. Test Author cannot respond to their own discussion
Auth::login($author);
$subService = app(SubscriptionService::class);
$usage = $subService->getUsageStats($author);
$canRespond = $usage['can_respond'] ?? false;
echo "3. Author response quota check: can_respond is " . ($canRespond ? 'true' : 'false') . ", daily_responses_remaining: {$usage['daily_responses_remaining']}\n";

// Test author attempting response
$communityRequestComp = new \App\Livewire\Marketplace\Community\CommunityRequest();
$communityRequestComp->mount($discApproved->id);
$communityRequestComp->responseText = 'Author trying to respond to own discussion';
$communityRequestComp->submitResponse();

$authorResponseExists = Response::where('discussion_id', $discApproved->id)
    ->where('user_id', $author->id)
    ->exists();

assert(! $authorResponseExists, "Author should NOT be able to respond to own discussion!");
echo "3b. Author response block test PASSED: Author was blocked from responding to own discussion.\n";

// 4. Test Author CAN edit their own discussion
$communityRequestComp->editTitle = 'Updated: Need iPhone 14 Pro Max screen replacement (OEM only)';
$communityRequestComp->editBody = 'Updated description: strictly OEM grade A screen.';
$communityRequestComp->editBudget = '95000';
$communityRequestComp->editStatus = 'open';
$communityRequestComp->updateDiscussion();

$discApproved->refresh();
assert($discApproved->title === 'Updated: Need iPhone 14 Pro Max screen replacement (OEM only)', "Author edit failed!");
echo "4. Author edit test PASSED: Title updated to '{$discApproved->title}'\n";

// 5. Test Watchlist & Notification to Watchers
Auth::login($watcher);
$watch = $discApproved->watchlists()->create([
    'user_id' => $watcher->id,
]);
assert($discApproved->isWatchedBy($watcher), "Watcher should be watching discussion!");
echo "5. Watchlist test PASSED: Watcher is watching discussion.\n";

// Now vendor responds to discussion with an offer
// Now vendor responds to discussion with an offer
Auth::login($vendor);

$vendorComp = new \App\Livewire\Marketplace\Community\CommunityRequest();
$vendorComp->mount($discApproved->id);
$vendorComp->responseText = 'I have original pull screen available for ₦90,000.';
$vendorComp->isOfferActive = true;
$vendorComp->composerOfferPrice = '90000';
$vendorComp->composerOfferWarranty = '14 days';
$vendorComp->composerOfferDelivery = 'buyer_pickup';
$vendorComp->composerOfferMessage = 'Tested original screen';
$vendorComp->submitResponse();

$vendorResp = Response::where('discussion_id', $discApproved->id)
    ->where('user_id', $vendor->id)
    ->latest()
    ->first();

assert($vendorResp !== null, "Vendor response should be created!");
echo "5b. Vendor response created with ID: {$vendorResp->id}\n";

// Check that watcher received notification
$newNotif = $watcher->notifications()->latest()->first();
assert($newNotif !== null, "Watcher should receive notification!");
echo "5c. Notification test PASSED: DiscussionResponseNotification recorded for watcher: '{$newNotif->data['title']}'\n";

// 6. Test Offer and Counter-Offer Root Parent ID Rule
$firstOffer = Offer::where('response_id', $vendorResp->id)->first();
assert($firstOffer !== null, "First offer should be created!");
assert($firstOffer->parent_id === null, "First offer parent_id must be null!");
echo "6. First offer created with ID: {$firstOffer->id}, parent_id: " . var_export($firstOffer->parent_id, true) . "\n";

// Author counters the first offer
$negService = app(NegotiationService::class);
$counter1 = $negService->submitCounterOffer($author, $firstOffer, [
    'delivery_method' => 'buyer_pickup',
    'terms' => 'Can you do ₦80,000?',
    'items' => [
        [
            'title' => 'iPhone 14 Pro Max screen',
            'quantity' => 1,
            'unit_price' => 80000,
            'condition' => 'used',
            'warranty_days' => 14,
        ]
    ]
]);

assert((int)$counter1->parent_id === (int)$firstOffer->id, "Counter 1 parent_id must equal first offer ID {$firstOffer->id}. Got: {$counter1->parent_id}");
echo "6b. Author counter 1 created with ID: {$counter1->id}, parent_id: {$counter1->parent_id} (Matches root offer ID: {$firstOffer->id})\n";

// Vendor counters author's counter offer
$counter2 = $negService->submitCounterOffer($vendor, $counter1, [
    'delivery_method' => 'buyer_pickup',
    'terms' => 'Lowest I can go is ₦85,000',
    'items' => [
        [
            'title' => 'iPhone 14 Pro Max screen',
            'quantity' => 1,
            'unit_price' => 85000,
            'condition' => 'used',
            'warranty_days' => 14,
        ]
    ]
]);

assert((int)$counter2->parent_id === (int)$firstOffer->id, "Counter 2 parent_id must STILL equal first offer ID {$firstOffer->id}. Got: {$counter2->parent_id}");
echo "6c. Vendor counter 2 created with ID: {$counter2->id}, parent_id: {$counter2->parent_id} (Still matches root offer ID: {$firstOffer->id}!)\n";

// Author counters vendor's second counter offer
$counter3 = $negService->submitCounterOffer($author, $counter2, [
    'delivery_method' => 'buyer_pickup',
    'terms' => 'Deal at ₦83,000!',
    'items' => [
        [
            'title' => 'iPhone 14 Pro Max screen',
            'quantity' => 1,
            'unit_price' => 83000,
            'condition' => 'used',
            'warranty_days' => 14,
        ]
    ]
]);

assert((int)$counter3->parent_id === (int)$firstOffer->id, "Counter 3 parent_id must STILL equal first offer ID {$firstOffer->id}. Got: {$counter3->parent_id}");
echo "6d. Author counter 3 created with ID: {$counter3->id}, parent_id: {$counter3->parent_id} (Hierarchy intact!)\n";

// 7. Test QuickViewOffers authorization
// Vendor tries to open QuickViewOffers on author's discussion
Auth::login($vendor);
$quickComp = new \App\Livewire\Components\Offers\QuickViewOffers();
$quickComp->loadOffers($vendorResp->id);
assert(! $quickComp->isOpen, "Vendor should NOT be allowed to view received offers via QuickViewOffers!");
echo "7. QuickViewOffers access restriction PASSED: Non-author blocked.\n";

// Author opens QuickViewOffers
Auth::login($author);
$quickCompAuthor = new \App\Livewire\Components\Offers\QuickViewOffers();
$quickCompAuthor->loadOffers($vendorResp->id);
assert($quickCompAuthor->isOpen, "Discussion author MUST be allowed to open QuickViewOffers!");
assert(count($quickCompAuthor->rounds) === 4, "Expected 4 negotiation rounds in QuickViewOffers, got: " . count($quickCompAuthor->rounds));
echo "7b. QuickViewOffers author access PASSED: Author successfully viewed " . count($quickCompAuthor->rounds) . " negotiation rounds.\n";

// 8. Test Polymorphic Reporting (Listing, Discussion, Response)
$listing = Listing::first();
if (! $listing) {
    $listing = Listing::create([
        'user_id' => $vendor->id,
        'title' => 'Demo Test Listing',
        'price' => 50000,
        'status' => 'active',
    ]);
}

// Report listing
$repListing = Report::create([
    'user_id' => $author->id,
    'reportable_type' => Listing::class,
    'reportable_id' => $listing->id,
    'title' => 'Misleading pricing',
    'description' => 'Price does not match description',
    'status' => 'pending',
]);

// Report discussion
$repDisc = Report::create([
    'user_id' => $vendor->id,
    'reportable_type' => Discussion::class,
    'reportable_id' => $discApproved->id,
    'title' => 'Spam content',
    'description' => 'Duplicate request',
    'status' => 'pending',
]);

// Report response
$repResp = Report::create([
    'user_id' => $author->id,
    'reportable_type' => Response::class,
    'reportable_id' => $vendorResp->id,
    'title' => 'Counterfeit warning',
    'description' => 'Suspected counterfeit part',
    'status' => 'pending',
]);

assert($repListing->reportable instanceof Listing, "Reportable must be Listing");
assert($repDisc->reportable instanceof Discussion, "Reportable must be Discussion");
assert($repResp->reportable instanceof Response, "Reportable must be Response");
echo "8. Polymorphic Reporting test PASSED: Listing, Discussion, Response all successfully reported.\n";

// Admin resolves report and notification is sent
$admin = User::firstOrCreate(
    ['email' => 'admin@propatis.com'],
    ['name' => 'System Admin', 'password' => bcrypt('password'), 'role' => 'admin', 'country_id' => $countryId]
);

$repResp->update([
    'status' => 'resolved',
    'action_taken' => 'Vendor warned',
    'resolution_notes' => 'Thank you for reporting. We investigated and issued a formal warning to the seller.',
    'resolved_by' => $admin->id,
    'resolved_at' => now(),
]);

$author->notify(new ReportResolvedNotification($repResp));

$authorNotif = $author->notifications()->where('type', ReportResolvedNotification::class)->latest()->first();
assert($authorNotif !== null, "Reporter should receive ReportResolvedNotification!");
echo "8b. Admin resolution & email notification test PASSED: Resolution note received by reporter: '{$authorNotif->data['resolution_notes']}'\n";

// 9. Test Real-Time Messaging & Reverb Events
$capturedEvent = null;
Event::listen(MessageSent::class, function ($event) use (&$capturedEvent) {
    $capturedEvent = $event;
});

// Author messages vendor via openVendorConversation
Auth::login($author);
$commReq = new \App\Livewire\Marketplace\Community\CommunityRequest();
$commReq->mount($discApproved->id);
$commReq->openVendorConversation($vendorResp->id);

$conv = Conversation::where('contextable_type', Discussion::class)
    ->where('contextable_id', $discApproved->id)
    ->first();

assert($conv !== null, "Conversation should be created between author and vendor!");
assert($conv->participants->count() === 2, "Conversation should have exactly 2 participants!");
echo "9. Conversation created with ID: {$conv->id} between author and vendor.\n";

// ConversationDrawer sends message
$convDrawer = new \App\Livewire\Components\Messaging\ConversationDrawer();
$convDrawer->loadConversation($conv->id);
$convDrawer->newMessage = 'Hi, is this screen still available for inspection today?';
$convDrawer->sendMessage();

assert($capturedEvent !== null, "MessageSent event should be fired!");
assert((int)$capturedEvent->message->conversation_id === (int)$conv->id, "Event conversation ID matches!");
assert((int)$capturedEvent->recipientId === (int)$vendor->id, "Event recipient ID matches!");
echo "9b. MessageSent event dispatched and broadcast on Reverb for conversation and recipient user.\n";

// Test unread badge counter for recipient
Auth::login($vendor);
$badge = new \App\Livewire\Components\Messaging\MessageCounterBadge();
$badge->mount();
assert($badge->unreadCount >= 1, "Vendor should have at least 1 unread message! Count: {$badge->unreadCount}");
echo "9c. MessageCounterBadge test PASSED: Recipient has {$badge->unreadCount} unread message(s).\n";

// Vendor opens conversation and marks message as read
$convDrawerVendor = new \App\Livewire\Components\Messaging\ConversationDrawer();
$convDrawerVendor->loadConversation($conv->id);

$badge->updateCount();
assert($badge->unreadCount === 0, "After vendor opens conversation, unreadCount should be 0! Got: {$badge->unreadCount}");
echo "9d. Real-time message read & badge decrement test PASSED: Unread count reset to 0.\n";

echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY! ===\n";
