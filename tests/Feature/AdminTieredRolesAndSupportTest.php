<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTieredRolesAndSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_has_full_access_including_system_settings(): void
    {
        $superAdminRole = Role::where('slug', 'super_admin')->firstOrFail();
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'name' => 'Chief Executive',
        ]);

        // Navigation check
        $navResponse = $this->actingAs($superAdmin)->get('/admin');
        $navResponse->assertStatus(200);
        $navResponse->assertSee('SYSTEM');
        $navResponse->assertSee('Support Desk');

        // Route check: settings
        $settingsResponse = $this->actingAs($superAdmin)->get('/admin/settings/general');
        $settingsResponse->assertStatus(200);

        // Route check: support desk
        $supportResponse = $this->actingAs($superAdmin)->get('/admin/support');
        $supportResponse->assertStatus(200);
    }

    public function test_general_manager_can_access_payouts_and_support_but_not_settings(): void
    {
        $gmRole = Role::where('slug', 'general_manager')->firstOrFail();
        $gm = User::factory()->create([
            'role_id' => $gmRole->id,
            'name' => 'Operations Director',
        ]);

        // Navigation: SYSTEM must NOT be visible
        $navResponse = $this->actingAs($gm)->get('/admin');
        $navResponse->assertStatus(200);
        $navResponse->assertDontSee('SYSTEM');
        $navResponse->assertSee('FINANCE &amp; REVENUE', false);
        $navResponse->assertSee('Support Desk');

        // Can access payouts
        $payoutsResponse = $this->actingAs($gm)->get('/admin/payouts');
        $payoutsResponse->assertStatus(200);

        // Can access support
        $supportResponse = $this->actingAs($gm)->get('/admin/support');
        $supportResponse->assertStatus(200);

        // CANNOT access settings -> 403 Forbidden
        $settingsResponse = $this->actingAs($gm)->get('/admin/settings/general');
        $settingsResponse->assertStatus(403);
    }

    public function test_customer_support_can_access_support_desk_but_not_payouts_or_settings(): void
    {
        $supportRole = Role::where('slug', 'customer_support')->firstOrFail();
        $supportAgent = User::factory()->create([
            'role_id' => $supportRole->id,
            'name' => 'Amina Agent',
        ]);

        // Navigation check
        $navResponse = $this->actingAs($supportAgent)->get('/admin');
        $navResponse->assertStatus(200);
        $navResponse->assertDontSee('SYSTEM');
        $navResponse->assertDontSee('FINANCE &amp; REVENUE', false);
        $navResponse->assertSee('Support Desk');

        // Can access support desk
        $supportResponse = $this->actingAs($supportAgent)->get('/admin/support');
        $supportResponse->assertStatus(200);

        // Cannot access payouts
        $payoutsResponse = $this->actingAs($supportAgent)->get('/admin/payouts');
        $payoutsResponse->assertStatus(403);

        // Cannot access settings
        $settingsResponse = $this->actingAs($supportAgent)->get('/admin/settings/general');
        $settingsResponse->assertStatus(403);
    }

    public function test_content_specialist_cannot_access_support_desk(): void
    {
        $contentRole = Role::where('slug', 'content_specialist')->firstOrFail();
        $contentAgent = User::factory()->create([
            'role_id' => $contentRole->id,
            'name' => 'Blog Writer',
        ]);

        // Navigation check
        $navResponse = $this->actingAs($contentAgent)->get('/admin');
        $navResponse->assertStatus(200);
        $navResponse->assertSee('CONTENT &amp; BLOG', false);
        $navResponse->assertDontSee('Support Desk');
        $navResponse->assertDontSee('SYSTEM');

        // Can access blog
        $blogResponse = $this->actingAs($contentAgent)->get('/admin/blog');
        $blogResponse->assertStatus(200);

        // Cannot access support desk
        $supportResponse = $this->actingAs($contentAgent)->get('/admin/support');
        $supportResponse->assertStatus(403);
    }

    public function test_user_registration_automatically_creates_support_conversation(): void
    {
        // Create support user first
        $supportUser = User::getSupportUser();

        // Register a regular user (role_id is null)
        $newUser = User::factory()->create([
            'name' => 'Emeka Buyer',
            'email' => 'emeka@example.com',
            'role_id' => null,
        ]);

        // Check conversation was created
        $conversation = Conversation::whereIn('contextable_type', [User::class, 'user'])
            ->where('contextable_id', $newUser->id)
            ->first();

        $this->assertNotNull($conversation, 'Support conversation was not created for the new user.');

        // Check welcome message exists
        $welcomeMessage = ConversationMessage::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($welcomeMessage);
        $this->assertStringContainsString('Welcome to Parts & Parcel, Emeka Buyer!', $welcomeMessage->body);
        $this->assertEquals($supportUser->id, $welcomeMessage->sender_id);

        // The user can view the message in /messages
        $messagesResponse = $this->actingAs($newUser)->get('/messages');
        $messagesResponse->assertStatus(200);
        $messagesResponse->assertSee('Customer Support');
        $messagesResponse->assertSee('Welcome to Parts & Parcel, Emeka Buyer!');
    }

    public function test_user_can_reply_and_support_agent_sees_it_in_support_desk(): void
    {
        $supportRole = Role::where('slug', 'customer_support')->firstOrFail();
        $supportAgent = User::factory()->create([
            'role_id' => $supportRole->id,
            'name' => 'Support Supervisor',
        ]);

        $customer = User::factory()->create([
            'name' => 'Fatima Merchant',
            'email' => 'fatima@example.com',
            'role_id' => null,
        ]);

        $conversation = Conversation::whereIn('contextable_type', [User::class, 'user'])
            ->where('contextable_id', $customer->id)
            ->firstOrFail();

        // Customer replies
        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'body' => 'Hello, I need help tracking invoice #PP-9901.',
            'read_at' => null,
        ]);

        // Support agent checks support desk
        $deskResponse = $this->actingAs($supportAgent)->get('/admin/support?c=' . $conversation->id);
        $deskResponse->assertStatus(200);
        $deskResponse->assertSee('Fatima Merchant');
        $deskResponse->assertSee('Hello, I need help tracking invoice #PP-9901.');
    }
}
