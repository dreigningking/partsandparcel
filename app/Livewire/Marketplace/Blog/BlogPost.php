<?php

namespace App\Livewire\Marketplace\Blog;

use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\ViewedEntity;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BlogPost extends Component
{
    public Post $post;
    public bool $isSubscribed = false;
    public int $likesCount = 0;
    public bool $isLiked = false;

    // Comment form fields
    public string $commentName = '';
    public string $commentEmail = '';
    public string $commentBody = '';

    public function mount(Post $post): void
    {
        // Ensure post is published
        if ($post->status !== 'published') {
            if (! Auth::check() || ! Auth::user()->isAdmin()) {
                abort(404);
            }
        }

        $this->post = $post->load(['user', 'category', 'media', 'likes']);
        $this->likesCount = $this->post->likes()->count();

        // 1. Record view in ViewedEntity via morph relation
        try {
            $this->post->views()->create([
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => request()->header('sec-ch-ua-mobile') === '?1' ? 'mobile' : 'desktop',
            ]);
        } catch (\Throwable $e) {
            // Silently ignore if logging view fails
        }

        // 2. Check if currently logged in user is subscribed (in Watchlist) and liked
        if (Auth::check()) {
            $this->isSubscribed = $this->post->isWatchedBy(Auth::user());
            $this->isLiked = $this->post->isLikedBy(Auth::user());
            $this->commentName = Auth::user()->name;
            $this->commentEmail = Auth::user()->email;
        }
    }

    public function toggleSubscription(): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $existing = $this->post->watchlists()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $this->isSubscribed = false;
            session()->flash('subscription_status', 'You have unsubscribed from notifications on this article.');
        } else {
            $this->post->watchlists()->create([
                'user_id' => $userId,
            ]);
            $this->isSubscribed = true;
            session()->flash('subscription_status', 'You are now subscribed! You will receive an instant notification whenever a new comment is approved on this article.');
        }

        return null;
    }

    public function submitComment(): void
    {
        $this->validate([
            'commentName' => 'required|string|max:100',
            'commentEmail' => 'required|email|max:150',
            'commentBody' => 'required|string|min:3|max:1500',
        ], [
            'commentName.required' => 'Please enter your name.',
            'commentEmail.required' => 'Please provide a valid email address.',
            'commentBody.required' => 'Please write your comment before submitting.',
        ]);

        PostComment::create([
            'post_id' => $this->post->id,
            'name' => $this->commentName,
            'email' => $this->commentEmail,
            'comment' => $this->commentBody,
        ]);

        // Auto-subscribe the commenting user if logged in and not yet subscribed
        if (Auth::check() && ! $this->isSubscribed) {
            $this->post->watchlists()->firstOrCreate([
                'user_id' => Auth::id(),
            ]);
            $this->isSubscribed = true;
        }

        $this->commentBody = '';

        session()->flash('comment_status', 'Thank you! Your comment has been submitted and is pending moderator approval before it appears publicly.');
    }

    public function toggleHelpful(): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $existingLike = Like::where('user_id', $userId)
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $this->post->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $this->isLiked = false;
            $this->likesCount = max(0, $this->likesCount - 1);
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_type' => Post::class,
                'likeable_id' => $this->post->id,
            ]);
            $this->isLiked = true;
            $this->likesCount++;
        }

        return null;
    }

    public function render()
    {
        $approvedComments = $this->post->approvedComments()->latest()->get();

        $relatedPosts = Post::query()
            ->where('id', '!=', $this->post->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->when($this->post->category_id, fn ($q) => $q->where('category_id', $this->post->category_id))
            ->with(['media', 'category'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $featuredImage = $this->post->featured_image_url;
        $featuredVideo = $this->post->featured_video_url;
        $viewsCount = $this->post->views()->count();
        $watchersCount = $this->post->watchlists()->count();

        return view('livewire.marketplace.blog.blog-post', [
            'approvedComments' => $approvedComments,
            'relatedPosts' => $relatedPosts,
            'featuredImage' => $featuredImage,
            'featuredVideo' => $featuredVideo,
            'viewsCount' => $viewsCount,
            'watchersCount' => $watchersCount,
        ]);
    }
}
