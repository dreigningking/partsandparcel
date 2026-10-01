<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;

class AdminBlogPostShow extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function delete(): mixed
    {
        $this->post->delete();

        return redirect()->route('admin.blog')->with('status', __('Post deleted.'));
    }

    public function regenerate(): void
    {
        if ($this->post->content_source !== 'ai') {
            return;
        }
        $this->post->update(['ai_generated_at' => now()]);
        $this->post->refresh();
        session()->flash('status', __('Regenerate queued (timestamp updated).'));
    }

    public function render()
    {
        $this->post->load(['user', 'category', 'listing', 'media', 'promotions.plan']);

        $featured = $this->post->media->firstWhere('is_primary', true) ?? $this->post->media->first();

        return view('livewire.admin.admin-blog-post-show', [
            'featured' => $featured,
        ]);
    }
}
