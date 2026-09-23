<?php

namespace Tests\Feature;

use App\Jobs\AbandonedCartJob;
use App\Jobs\SubscriptionAutoRenewJob;
use App\Jobs\SubscriptionExpiredJob;
use App\Jobs\SubscriptionExpiringJob;
use App\Livewire\Dashboard\Notifications;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\DeviceToken;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Response;
use App\Models\Revenue;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\AbandonedCartNotification;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use App\Services\Commercial\SubscriptionService;
use App\Services\Notification\FcmService;
use Database\Seeders\CategoriesAndBrandsSeeder;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SubscriptionPlansSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionAndNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountriesSeeder::class,
            RolesAndPermissionsSeeder::class,
            CategoriesAndBrandsSeeder::class,
            SubscriptionPlansSeeder::class,
        ]);

        $category = Category::firstOrCreate(['slug' => 'test-laptops'], ['name' => 'Laptops']);
        $deviceModel = DeviceModel::firstOrCreate(['slug' => 'hp-840-g5'], [
            'category_id' => $category->id,
            'name' => 'HP EliteBook 840 G5',
        ]);
        $owner = User::factory()->create();
        $this->item = Item::create([
            'user_id' => $owner->id,
            'model_id' => $deviceModel->id,
            'serial_number' => 'SN-SUB-TEST-001',
            'condition_status' => 'working',
        ]);
    }

    public function test_starter_free_user_quota_is_calculated_dynamically_on_the_fly(): void
    {
        $user = User::factory()->create();
        $subscriptionService = app(SubscriptionService::class);

        // 1. Check initial stats on the fly
        $initialStats = $subscriptionService->getUsageStats($user);
        $this->assertEquals('Starter Free', $initialStats['plan_name']);
        $this->assertEquals(1, $initialStats['daily_response_limit']);
        $this->assertEquals(0, $initialStats['daily_responses_used']);
        $this->assertEquals(1, $initialStats['daily_responses_remaining']);
        $this->assertTrue($initialStats['can_respond']);
        $this->assertEquals(10, $initialStats['listing_limit']);
        $this->assertTrue($initialStats['can_create_listing']);

        // 2. Create discussion and 1 response today
        $discussion = Discussion::create([
            'user_id' => $user->id,
            'title' => 'Need HP Motherboard',
            'body' => 'HP EliteBook motherboard needed urgently.',
            'status' => 'open',
        ]);

        Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'body' => 'I have this board available.',
            'status' => 'visible',
            'created_at' => now(),
        ]);

        // 3. Stats immediately reflect today's usage without any saved counters
        $usedStats = $subscriptionService->getUsageStats($user);
        $this->assertEquals(1, $usedStats['daily_responses_used']);
        $this->assertEquals(0, $usedStats['daily_responses_remaining']);
        $this->assertFalse($usedStats['can_respond']);
        $this->assertFalse($subscriptionService->canSubmitResponse($user));

        // 4. If a response was made yesterday, today's quota remains unaffected
        $otherUser = User::factory()->create();
        $pastResponse = new Response([
            'discussion_id' => $discussion->id,
            'user_id' => $otherUser->id,
            'body' => 'Yesterday response',
            'status' => 'visible',
        ]);
        $pastResponse->timestamps = false;
        $pastResponse->created_at = now()->subDay();
        $pastResponse->save();

        $otherStats = $subscriptionService->getUsageStats($otherUser);
        $this->assertEquals(0, $otherStats['daily_responses_used']);
        $this->assertTrue($otherStats['can_respond']);
    }

    public function test_active_listings_limit_is_calculated_dynamically_on_the_fly(): void
    {
        $user = User::factory()->create();
        $subscriptionService = app(SubscriptionService::class);

        // Create 10 active listings for starter user
        for ($i = 0; $i < 10; $i++) {
            Listing::create([
                'user_id' => $user->id,
                'assetable_type' => Item::class,
                'assetable_id' => $this->item->id,
                'price' => 5000 + ($i * 100),
                'quantity' => 1,
                'status' => 'active',
            ]);
        }

        $stats = $subscriptionService->getUsageStats($user);
        $this->assertEquals(10, $stats['listings_used']);
        $this->assertEquals(0, $stats['listings_remaining']);
        $this->assertFalse($stats['can_create_listing']);
        $this->assertFalse($subscriptionService->canCreateListing($user));

        // Deactivate one listing
        Listing::where('user_id', $user->id)->first()->update(['status' => 'sold']);

        $updatedStats = $subscriptionService->getUsageStats($user);
        $this->assertEquals(9, $updatedStats['listings_used']);
        $this->assertEquals(1, $updatedStats['listings_remaining']);
        $this->assertTrue($updatedStats['can_create_listing']);
    }

    public function test_community_request_livewire_blocks_response_when_quota_exhausted(): void
    {
        $requester = User::factory()->create();
        $seller = User::factory()->create();

        $discussion = Discussion::create([
            'user_id' => $requester->id,
            'title' => 'Need Dell Battery',
            'body' => 'Latitude 7490 battery needed',
            'status' => 'open',
        ]);

        // Consume seller's 1 free response for today
        Response::create([
            'discussion_id' => $discussion->id,
            'user_id' => $seller->id,
            'body' => 'Already replied once today',
            'status' => 'visible',
            'created_at' => now(),
        ]);

        // Attempt 2nd response via Livewire component
        $this->actingAs($seller);
        Livewire::test(\App\Livewire\Marketplace\Community\CommunityRequest::class, ['id' => $discussion->id])
            ->set('responseText', 'Trying to reply again today')
            ->call('submitResponse')
            ->assertSee('You have reached your daily response quota');

        // Verify no second response was created in database
        $this->assertEquals(1, Response::where('user_id', $seller->id)->count());
    }

    public function test_subscription_checkout_and_activation(): void
    {
        $user = User::factory()->create();
        $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->firstOrFail();
        $subscriptionService = app(SubscriptionService::class);

        // 1. Initialize checkout
        $checkout = $subscriptionService->initializeSubscriptionCheckout($user, $proPlan->id, 'paystack');
        $this->assertEquals('success', $checkout['status']);
        $this->assertFalse($checkout['is_free']);
        $this->assertNotNull($checkout['reference']);

        $payment = $checkout['payment'];
        $this->assertEquals('pending', $payment->status);

        // 2. Activate subscription upon payment confirmation
        $subscription = $subscriptionService->activateSubscription($payment);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals($proPlan->id, $subscription->subscription_plan_id);
        $this->assertTrue($subscription->ends_at->isFuture());

        // Platform revenue recorded
        $revenue = Revenue::where('payment_id', $payment->id)->first();
        $this->assertNotNull($revenue);
        $this->assertEquals('subscription', $revenue->type);

        // 3. User usage dynamically scales to Pro tier (20 responses/day, 100 listings, disassembly unlocked)
        $proStats = $subscriptionService->getUsageStats($user->fresh());
        $this->assertEquals('Pro Technician & Vendor', $proStats['plan_name']);
        $this->assertEquals(20, $proStats['daily_response_limit']);
        $this->assertEquals(100, $proStats['listing_limit']);
        $this->assertTrue($proStats['has_disassembly_tool']);
        $this->assertTrue($proStats['is_paid']);
    }

    public function test_subscription_expiring_job_notifies_users_within_3_days(): void
    {
        $user = User::factory()->create();
        $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->firstOrFail();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $proPlan->id,
            'status' => 'active',
            'starts_at' => now()->subDays(27),
            'ends_at' => now()->addDays(2),
            'response_limit' => 20,
        ]);

        $this->assertEquals(0, $user->notifications()->count());

        (new SubscriptionExpiringJob)->handle();

        $this->assertEquals(1, $user->notifications()->count());
        $notification = $user->notifications()->first();
        $this->assertEquals(SubscriptionExpiringNotification::class, $notification->type);
        $this->assertStringContainsString('will expire in 2 days', $notification->data['message']);
    }

    public function test_subscription_expired_job_transitions_lapsed_subscriptions(): void
    {
        $user = User::factory()->create();
        $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->firstOrFail();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $proPlan->id,
            'status' => 'active',
            'starts_at' => now()->subMonth()->subDay(),
            'ends_at' => now()->subDay(),
            'response_limit' => 20,
        ]);

        (new SubscriptionExpiredJob)->handle(app(SubscriptionService::class));

        $this->assertEquals('expired', $subscription->fresh()->status);
        $this->assertEquals(1, $user->notifications()->count());
        $notification = $user->notifications()->first();
        $this->assertEquals(SubscriptionExpiredNotification::class, $notification->type);

        // Account downgraded to Starter Free
        $usage = app(SubscriptionService::class)->getUsageStats($user->fresh());
        $this->assertEquals('Starter Free', $usage['plan_name']);
        $this->assertEquals(1, $usage['daily_response_limit']);
    }

    public function test_subscription_auto_renew_job_extends_subscription(): void
    {
        $user = User::factory()->create();
        $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->firstOrFail();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $proPlan->id,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addHours(2),
            'response_limit' => 20,
        ]);

        (new SubscriptionAutoRenewJob)->handle();

        $refreshedSub = $subscription->fresh();
        $this->assertEquals('active', $refreshedSub->status);
        $this->assertTrue($refreshedSub->ends_at->isAfter(now()->addDays(20)));

        $payment = Payment::where('subscription_id', $subscription->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('successful', $payment->status);
    }

    public function test_abandoned_cart_job_dispatches_notification(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $listing = Listing::create([
            'user_id' => $seller->id,
            'assetable_type' => Item::class,
            'assetable_id' => $this->item->id,
            'price' => 12000,
            'quantity' => 2,
            'status' => 'active',
        ]);

        $cart = Cart::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'listing_id' => $listing->id,
            'quantity' => 1,
            'unit_price' => 12000,
        ]);

        // Manually set past updated_at without auto-timestamp override
        $cart->timestamps = false;
        $cart->updated_at = now()->subHours(30);
        $cart->save();

        (new AbandonedCartJob)->handle();

        $this->assertEquals(1, $buyer->notifications()->count());
        $notif = $buyer->notifications()->first();
        $this->assertEquals(AbandonedCartNotification::class, $notif->type);
        $this->assertStringContainsString('Cart is Waiting', $notif->data['title']);
    }

    public function test_fcm_service_and_device_tokens(): void
    {
        $user = User::factory()->create();
        $fcmService = app(FcmService::class);

        // Register device token
        $deviceToken = $fcmService->registerToken($user, 'sample-fcm-token-12345', 'android', 'Samsung Galaxy');
        $this->assertInstanceOf(DeviceToken::class, $deviceToken);
        $this->assertEquals('sample-fcm-token-12345', $deviceToken->token);
        $this->assertEquals(1, $user->deviceTokens()->count());

        // Send to user executes gracefully without throwing
        $res = $fcmService->sendToUser($user, 'Test Alert', 'This is a test notification');
        $this->assertTrue($res['success']);
    }

    public function test_notifications_livewire_component_actions(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::create(['buyer_id' => $user->id, 'seller_id' => $user->id]);

        // Send dummy database notifications
        $user->notify(new \App\Notifications\AbandonedCartNotification($cart));

        $component = Livewire::test(Notifications::class)
            ->assertSee('Your Cart is Waiting')
            ->assertSee('All Alerts (1)');

        $notif = $user->notifications()->first();
        $this->assertNull($notif->read_at);

        // Mark as read
        $component->call('markAsRead', $notif->id);
        $this->assertNotNull($notif->fresh()->read_at);

        // Mark all as read
        $component->call('markAllAsRead');
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_broadcasting_configuration_loads_pusher_and_reverb(): void
    {
        $pusherConfig = config('broadcasting.connections.pusher');
        $this->assertNotNull($pusherConfig);
        $this->assertEquals('pusher', $pusherConfig['driver']);

        $reverbConfig = config('broadcasting.connections.reverb');
        $this->assertNotNull($reverbConfig);
        $this->assertEquals('reverb', $reverbConfig['driver']);
    }
}
