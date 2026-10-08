<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Blog Articles — Admin Console')]
class AdminBlog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'category')]
    public ?int $category = null;

    #[Url(as: 'type')]
    public string $type = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function deletePost(int $id): void
    {
        $post = Post::findOrFail($id);
        $post->delete();

        session()->flash('status', 'Article deleted successfully.');
    }

    public function togglePublish(int $id): void
    {
        $post = Post::findOrFail($id);
        if ($post->status === 'published') {
            $post->update(['status' => 'draft']);
            session()->flash('status', 'Article moved to draft.');
        } else {
            $post->update([
                'status' => 'published',
                'published_at' => $post->published_at ?: now(),
            ]);
            session()->flash('status', 'Article published successfully.');
        }
    }

    public function render()
    {
        $posts = Post::query()
            ->with(['user', 'category', 'media', 'comments'])
            ->withCount('comments')
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                        ->orWhere('tags', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->category, fn (Builder $query) => $query->where('category_id', $this->category))
            ->when($this->type === 'blog', fn (Builder $query) => $query->where('is_help', false))
            ->when($this->type === 'help', fn (Builder $query) => $query->where('is_help', true))
            ->latest('created_at')
            ->paginate(12);

        $pendingCommentsCount = PostComment::pending()->count();
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $helpPostsCount = Post::help()->count();
        $blogPostsCount = Post::blog()->count();

        return view('livewire.admin.admin-blog', [
            'posts' => $posts,
            'statuses' => ['published', 'draft', 'archived'],
            'categories' => Category::query()->orderBy('name', 'asc')->get(['id', 'name']),
            'pendingCommentsCount' => $pendingCommentsCount,
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'draftPosts' => $draftPosts,
            'helpPostsCount' => $helpPostsCount,
            'blogPostsCount' => $blogPostsCount,
        ]);
    }
}
