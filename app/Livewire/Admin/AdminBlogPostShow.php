<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\Watchlist;
use App\Notifications\PostCommentApprovedNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('View Blog Post — Admin Console')]
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

        return redirect()->route('admin.blog')->with('status', 'Article deleted successfully.');
    }

    public function togglePublish(): void
    {
        if ($this->post->status === 'published') {
            $this->post->update(['status' => 'draft']);
            session()->flash('status', 'Article moved to draft.');
        } else {
            $this->post->update([
                'status' => 'published',
                'published_at' => $this->post->published_at ?: now(),
            ]);
            session()->flash('status', 'Article published successfully.');
        }

        $this->post->refresh();
    }

    public function approveComment(int $commentId): void
    {
        $comment = PostComment::with('latestModeration')->findOrFail($commentId);

        $moderation = $comment->latestModeration;
        if ($moderation) {
            $moderation->update([
                'status' => 'approved',
                'moderated_by' => auth()->id(),
                'reason' => null,
            ]);
        }

        // Notify watchers
        $watchers = Watchlist::where('watchable_type', Post::class)
            ->where('watchable_id', $this->post->id)
            ->with('user')
            ->get();

        foreach ($watchers as $watcher) {
            if ($watcher->user && strtolower($watcher->user->email) !== strtolower($comment->email)) {
                $watcher->user->notify(new PostCommentApprovedNotification($this->post, $comment));
            }
        }

        session()->flash('status', "Comment by '{$comment->name}' approved and watchers notified.");
    }

    public function rejectComment(int $commentId): void
    {
        $comment = PostComment::with('latestModeration')->findOrFail($commentId);

        $moderation = $comment->latestModeration;
        if ($moderation) {
            $moderation->update([
                'status' => 'rejected',
                'moderated_by' => auth()->id(),
                'reason' => 'Declined by moderator',
            ]);
        }

        session()->flash('status', "Comment by '{$comment->name}' rejected.");
    }

    public function deleteComment(int $commentId): void
    {
        $comment = PostComment::findOrFail($commentId);
        $comment->moderations()->delete();
        $comment->delete();

        session()->flash('status', 'Comment deleted.');
    }

    public function render()
    {
        $this->post->load(['user', 'category', 'media', 'comments.latestModeration']);

        $featuredImage = $this->post->featured_image_url;
        $featuredVideo = $this->post->featured_video_url;
        $viewsCount = $this->post->views()->count();
        $watchersCount = $this->post->watchlists()->count();

        $comments = $this->post->comments()->with('latestModeration')->latest()->get();

        return view('livewire.admin.admin-blog-post-show', [
            'featuredImage' => $featuredImage,
            'featuredVideo' => $featuredVideo,
            'viewsCount' => $viewsCount,
            'watchersCount' => $watchersCount,
            'comments' => $comments,
        ]);
    }
}
