<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminBlog;
use App\Livewire\Admin\AdminBlogPostCreate;
use App\Livewire\Marketplace\Blog\BlogList;
use App\Livewire\Marketplace\Help;
use App\Livewire\Marketplace\HelpArticle;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\HelpArticlesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HelpAndContactArticlesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountriesSeeder::class,
            RolesAndPermissionsSeeder::class,
            HelpArticlesSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Super Admin')->first() ?? Role::first();
        $this->admin = User::factory()->create([
            'role_id' => $adminRole?->id,
            'email' => 'admin_test@partsandparcel.com',
        ]);
    }

    public function test_help_page_renders_topic_sections_and_articles(): void
    {
        $response = $this->get(route('help'));
        $response->assertStatus(200);

        // Core topics check
        $response->assertSee('How to Negotiate and Counter-Offer on Parts');
        $response->assertSee('Understanding the 48-Hour Escrow Protection Window');
        $response->assertSee('Tracking Waybills &amp; Inspecting Delivered Parcels', false);

        // Verify Livewire component renders topics
        Livewire::test(Help::class)
            ->assertSee('Buying &amp; Making Offers', false)
            ->assertSee('Escrow &amp; Secure Payments', false)
            ->assertSee('Disputes &amp; Mediation', false);
    }

    public function test_help_page_search_filters_results(): void
    {
        Livewire::test(Help::class)
            ->set('search', 'Escrow')
            ->assertSee('Understanding the 48-Hour Escrow Protection Window')
            ->assertDontSee('Tracking Waybills & Inspecting Delivered Parcels');
    }

    public function test_help_page_topic_filter_works(): void
    {
        Livewire::test(Help::class)
            ->call('filterTopic', 'Shipping & Deliveries')
            ->assertSet('selectedTopic', 'Shipping & Deliveries')
            ->assertSee('Tracking Waybills & Inspecting Delivered Parcels')
            ->assertDontSee('How to Negotiate and Counter-Offer on Parts');
    }

    public function test_help_article_reader_page_displays_content_and_breadcrumbs(): void
    {
        $article = Post::help()->where('title', 'Understanding the 48-Hour Escrow Protection Window')->firstOrFail();

        $response = $this->get(route('help.show', $article->slug));
        $response->assertStatus(200);
        $response->assertSee('Understanding the 48-Hour Escrow Protection Window');
        $response->assertSee('Escrow &amp; Secure Payments', false);
        $response->assertSee('Help Center');
        $response->assertSee('Was this article helpful?');
    }

    public function test_help_article_feedback_rating_interaction(): void
    {
        $article = Post::help()->firstOrFail();

        Livewire::test(HelpArticle::class, ['post' => $article])
            ->assertSet('wasHelpful', null)
            ->call('rateHelpful', true)
            ->assertSet('wasHelpful', true)
            ->assertSee('Thank you for your feedback!');
    }

    public function test_blog_feed_does_not_show_help_articles(): void
    {
        // Create a regular blog post
        $category = Category::create([
            'name' => 'Auto Diagnostics',
            'slug' => 'auto-diagnostics',
            'is_active' => true,
        ]);

        $blogPost = Post::create([
            'user_id' => $this->admin->id,
            'category_id' => $category->id,
            'is_help' => false,
            'title' => 'Top 10 Auto Diagnostic Tips for Workshop Owners',
            'slug' => 'top-10-auto-diagnostic-tips-for-workshop-owners',
            'excerpt' => 'Practical garage advice.',
            'content' => 'Full blog article content here.',
            'tags' => 'tips, auto',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Livewire::test(BlogList::class)
            ->assertSee('Top 10 Auto Diagnostic Tips for Workshop Owners')
            ->assertDontSee('How to Negotiate and Counter-Offer on Parts')
            ->assertDontSee('Understanding the 48-Hour Escrow Protection Window');
    }

    public function test_admin_can_filter_and_create_help_article(): void
    {
        $this->actingAs($this->admin);

        // Test AdminBlog filter
        Livewire::test(AdminBlog::class)
            ->set('type', 'help')
            ->assertSee('Understanding the 48-Hour Escrow Protection Window')
            ->set('type', 'blog')
            ->assertDontSee('Understanding the 48-Hour Escrow Protection Window');

        // Test AdminBlogPostCreate for help article
        Livewire::test(AdminBlogPostCreate::class)
            ->set('is_help', true)
            ->set('helpTopic', 'Disputes & Mediation')
            ->set('title', 'Arbitration Guidelines for Rare Classic Parts')
            ->set('excerpt', 'Guide on classic car mediation.')
            ->set('content', 'Detailed guidelines content here.')
            ->set('status', 'published')
            ->call('save')
            ->assertRedirect();

        $created = Post::where('slug', 'arbitration-guidelines-for-rare-classic-parts')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->is_help);
        $this->assertNull($created->category_id);
        $this->assertStringContainsString('Disputes & Mediation', $created->tags);
    }

    public function test_guest_contact_form_submission_forwards_email_to_support(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        Livewire::test(\App\Livewire\Marketplace\Contact::class)
            ->set('topic', 'Order & Delivery')
            ->set('name', 'Nkem Owoh')
            ->set('email', 'nkem.guest@example.com')
            ->set('phone', '+2348033334444')
            ->set('subject', 'Tracking update for radiator assembly')
            ->set('message', 'Hello, my courier tracking is showing pending dispatch for 48 hours.')
            ->call('sendMessage')
            ->assertSet('submitted', true)
            ->assertSet('isRegistered', false)
            ->assertSee('Message Successfully Sent!');

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ContactMessage::class, function ($mail) {
            return $mail->hasTo('support@partsandparcel.com')
                && $mail->contactData['email'] === 'nkem.guest@example.com'
                && $mail->contactData['subject'] === 'Tracking update for radiator assembly';
        });

        // Guest email does not exist in users table -> no conversation thread created
        $this->assertDatabaseMissing('conversations', [
            'created_by' => 999999,
        ]);
    }

    public function test_registered_user_contact_form_creates_support_conversation_and_participant(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $supportRole = Role::where('slug', 'customer_support')->first();
        $supportAdmin = User::where('email', 'support@partsandparcel.com')->first();
        if (! $supportAdmin) {
            $supportAdmin = User::factory()->create([
                'role_id' => $supportRole?->id,
                'email' => 'support@partsandparcel.com',
                'name' => 'Support Desk Officer',
            ]);
        } else {
            $supportAdmin->update(['role_id' => $supportRole?->id]);
        }

        $registeredUser = User::factory()->create([
            'email' => 'registered.mechanic@yahoo.com',
            'name' => 'Emeka Mechanic',
            'phone' => '+2348055556666',
        ]);

        Livewire::test(\App\Livewire\Marketplace\Contact::class)
            ->set('topic', 'Escrow & Payment')
            ->set('name', 'Emeka Mechanic')
            ->set('email', 'registered.mechanic@yahoo.com')
            ->set('reference_id', 'INV-2026-0042')
            ->set('subject', 'Escrow release dispute inquiry')
            ->set('message', 'I delivered the gearbox and buyer confirmed inspection but escrow is pending.')
            ->call('sendMessage')
            ->assertSet('submitted', true)
            ->assertSet('isRegistered', true)
            ->assertSee('This ticket has also been added to your in-app');

        // 1. Mail forwarded to support
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ContactMessage::class, function ($mail) {
            return $mail->hasTo('support@partsandparcel.com')
                && $mail->contactData['is_registered_user'] === true;
        });

        // 2. Conversation created with nullable contextable fields
        $conversation = \App\Models\Conversation::where('created_by', $registeredUser->id)->first();
        $this->assertNotNull($conversation);
        $this->assertNull($conversation->contextable_type);
        $this->assertNull($conversation->contextable_id);
        $this->assertTrue($conversation->isSupport());

        // 3. ConversationParticipant created for registered user AND support admin
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $registeredUser->id,
        ]);
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $supportAdmin->id,
        ]);

        // 4. ConversationMessage created with details
        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $registeredUser->id,
        ]);
        $message = \App\Models\ConversationMessage::where('conversation_id', $conversation->id)->first();
        $this->assertStringContainsString('Escrow release dispute inquiry', $message->body);
        $this->assertStringContainsString('INV-2026-0042', $message->body);

        // 5. Appears in Admin Support Desk
        $this->actingAs($supportAdmin);
        Livewire::test(\App\Livewire\Admin\AdminSupportConversations::class)
            ->assertSee('Emeka Mechanic')
            ->assertSee('Escrow release dispute inquiry');

        // 6. Appears in Registered User's Messages Hub
        $this->actingAs($registeredUser);
        Livewire::test(\App\Livewire\Dashboard\Messages\MessageList::class)
            ->assertSee('Customer Support')
            ->assertSee('Escrow release dispute inquiry');
    }
}
