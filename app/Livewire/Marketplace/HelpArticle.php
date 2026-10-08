<?php

namespace App\Livewire\Marketplace;

use App\Models\Post;
use App\Models\ViewedEntity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HelpArticle extends Component
{
    public Post $post;
    public ?string $topic = null;
    public ?bool $wasHelpful = null;

    /** @var Collection<int, Post> */
    public Collection $relatedArticles;

    public function mount(Post $post): void
    {
        // Must be a help article or admin preview
        if (! $post->is_help) {
            abort(404);
        }

        if ($post->status !== 'published') {
            if (! Auth::check() || ! Auth::user()->isAdmin()) {
                abort(404);
            }
        }

        $this->post = $post->load(['user', 'media']);
        $this->topic = $post->help_topic;

        // Record view in ViewedEntity
        try {
            $this->post->views()->create([
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => request()->header('sec-ch-ua-mobile') === '?1' ? 'mobile' : 'desktop',
            ]);
        } catch (\Throwable $e) {
            // Silently ignore view log issues
        }

        // Fetch other articles in the same topic
        $topicQuery = $this->topic ? "%{$this->topic}%" : '%';
        $this->relatedArticles = Post::help()
            ->published()
            ->where('id', '!=', $this->post->id)
            ->where('tags', 'like', $topicQuery)
            ->latest('published_at')
            ->take(5)
            ->get();
    }

    public function rateHelpful(bool $helpful): void
    {
        $this->wasHelpful = $helpful;
    }

    public function render()
    {
        $featuredImage = $this->post->featured_image_url;
        $featuredVideo = $this->post->featured_video_url;

        return view('livewire.marketplace.help-article', [
            'featuredImage' => $featuredImage,
            'featuredVideo' => $featuredVideo,
            'allTopics' => Post::HELP_TOPICS,
        ])->title("{$this->post->title} — Help Center");
    }
}
