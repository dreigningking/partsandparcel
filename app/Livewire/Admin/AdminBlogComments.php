<?php

namespace App\Livewire\Admin;

use App\Models\Moderation;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Watchlist;
use App\Notifications\PostCommentApprovedNotification;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Blog Comments Moderation — Admin Console')]
class AdminBlogComments extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'pending'; // 'all', 'pending', 'approved', 'rejected'

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $postId = null;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPostId(): void
    {
        $this->resetPage();
    }

    public function approveComment(int $id): void
    {
        $comment = PostComment::with(['post', 'latestModeration'])->findOrFail($id);

        $moderation = $comment->latestModeration;

        if ($moderation) {
            $moderation->update([
                'status' => 'approved',
                'moderated_by' => auth()->id(),
                'reason' => null,
            ]);
        } else {
            Moderation::create([
                'moderatable_type' => PostComment::class,
                'moderatable_id' => $comment->id,
                'action' => 'created',
                'status' => 'approved',
                'moderated_by' => auth()->id(),
            ]);
        }

        // Notify all users watching this post!
        $post = $comment->post;
        if ($post) {
            $watchers = $post->watchlists()->with('user')->get();

            foreach ($watchers as $watcher) {
                if ($watcher->user && strtolower($watcher->user->email) !== strtolower($comment->email)) {
                    $watcher->user->notify(new PostCommentApprovedNotification($post, $comment));
                }
            }
        }

        session()->flash('status', "Comment by '{$comment->name}' has been approved and watchers notified.");
    }

    public function rejectComment(int $id, string $reason = 'Content violates community standards'): void
    {
        $comment = PostComment::with('latestModeration')->findOrFail($id);

        $moderation = $comment->latestModeration;

        if ($moderation) {
            $moderation->update([
                'status' => 'rejected',
                'moderated_by' => auth()->id(),
                'reason' => $reason,
            ]);
        } else {
            Moderation::create([
                'moderatable_type' => PostComment::class,
                'moderatable_id' => $comment->id,
                'action' => 'created',
                'status' => 'rejected',
                'reason' => $reason,
                'moderated_by' => auth()->id(),
            ]);
        }

        session()->flash('status', "Comment by '{$comment->name}' has been rejected.");
    }

    public function deleteComment(int $id): void
    {
        $comment = PostComment::findOrFail($id);
        $comment->moderations()->delete();
        $comment->delete();

        session()->flash('status', 'Comment permanently deleted.');
    }

    public function render()
    {
        $query = PostComment::query()
            ->with(['post', 'latestModeration.moderator'])
            ->when($this->search !== '', function (Builder $q) {
                $q->where(function (Builder $sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('comment', 'like', "%{$this->search}%")
                        ->orWhereHas('post', fn (Builder $pq) => $pq->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->postId, fn (Builder $q) => $q->where('post_id', $this->postId));

        if ($this->status === 'pending') {
            $query->pending();
        } elseif ($this->status === 'approved') {
            $query->approved();
        } elseif ($this->status === 'rejected') {
            $query->rejected();
        }

        $comments = $query->latest('created_at')->paginate(15);

        // Counts for tabs
        $pendingCount = PostComment::pending()->count();
        $approvedCount = PostComment::approved()->count();
        $rejectedCount = PostComment::rejected()->count();
        $totalCount = PostComment::count();

        $posts = Post::query()->orderBy('title', 'asc')->get(['id', 'title']);

        return view('livewire.admin.admin-blog-comments', [
            'comments' => $comments,
            'posts' => $posts,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
