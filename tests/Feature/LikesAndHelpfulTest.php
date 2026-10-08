<?php

namespace Tests\Feature;

use App\Livewire\Marketplace\Blog\BlogPost;
use App\Livewire\Marketplace\Community\CommunityRequest;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Like;
use App\Models\Post;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LikesAndHelpfulTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $author;
    protected Category $category;
    protected Discussion $discussion;
    protected Response $response;
    protected Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Helpful Tester',
            'email' => 'helpful_tester@example.com',
        ]);

        $this->author = User::factory()->create([
            'name' => 'Content Author',
            'email' => 'content_author@example.com',
        ]);

        $this->category = Category::create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops-and-computers',
        ]);

        $this->discussion = Discussion::create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Need HP EliteBook Motherboard',
            'body' => 'Looking for clean replacement motherboard.',
            'type' => 'item',
            'status' => 'open',
        ]);

        $this->response = Response::create([
            'discussion_id' => $this->discussion->id,
            'user_id' => $this->author->id,
            'body' => 'I have 2 units in Computer Village ready for testing.',
            'status' => 'active',
        ]);

        $this->post = Post::create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Complete Laptop Troubleshooting Guide',
            'slug' => 'complete-laptop-troubleshooting-guide',
            'excerpt' => 'Practical diagnostic steps for technician yards.',
            'content' => '<p>Detailed motherboard testing steps and techniques.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function test_guest_cannot_see_helpful_button_on_community_request(): void
    {
        Livewire::test(CommunityRequest::class, ['id' => $this->discussion->id])
            ->assertDontSeeHtml('wire:click="toggleHelpful(' . $this->response->id . ')"');
    }

    public function test_authenticated_user_sees_helpful_button_and_can_like_community_response(): void
    {
        $this->actingAs($this->user);

        Livewire::test(CommunityRequest::class, ['id' => $this->discussion->id])
            ->assertSeeHtml('wire:click="toggleHelpful(' . $this->response->id . ')"')
            ->assertSee('Helpful')
            ->call('toggleHelpful', $this->response->id);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->response->id,
            'likeable_type' => Response::class,
        ]);

        $this->assertEquals(1, $this->response->likes()->count());
        $this->assertTrue($this->response->isLikedBy($this->user));
    }

    public function test_authenticated_user_can_unlike_community_response(): void
    {
        $this->actingAs($this->user);

        // Seed existing like
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $this->response->id,
            'likeable_type' => Response::class,
        ]);

        $component = Livewire::test(CommunityRequest::class, ['id' => $this->discussion->id]);
        
        // Assert initial state is liked
        $responses = $component->get('responses');
        $this->assertEquals(1, $responses[0]['likes_count']);
        $this->assertTrue($responses[0]['is_liked']);

        // Toggle to unlike
        $component->call('toggleHelpful', $this->response->id);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->response->id,
            'likeable_type' => Response::class,
        ]);

        $responsesAfter = $component->get('responses');
        $this->assertEquals(0, $responsesAfter[0]['likes_count']);
        $this->assertFalse($responsesAfter[0]['is_liked']);
    }

    public function test_guest_cannot_see_helpful_button_on_blog_post(): void
    {
        Livewire::test(BlogPost::class, ['post' => $this->post])
            ->assertDontSeeHtml('wire:click="toggleHelpful"');
    }

    public function test_authenticated_user_sees_helpful_button_and_can_toggle_blog_post_like(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(BlogPost::class, ['post' => $this->post])
            ->assertSeeHtml('wire:click="toggleHelpful"')
            ->assertSee('Helpful')
            ->assertSet('isLiked', false)
            ->assertSet('likesCount', 0)
            ->call('toggleHelpful');

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->post->id,
            'likeable_type' => Post::class,
        ]);

        $component->assertSet('isLiked', true)
            ->assertSet('likesCount', 1);

        // Click again to unlike
        $component->call('toggleHelpful');

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->post->id,
            'likeable_type' => Post::class,
        ]);

        $component->assertSet('isLiked', false)
            ->assertSet('likesCount', 0);
    }
}
