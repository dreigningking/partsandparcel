<?php

namespace App\Livewire\Marketplace\Blog;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Blog & Knowledge Base — Parts & Parcel')]
class BlogList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?int $category = null;

    #[Url(as: 'tag')]
    public string $tag = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedTag(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->category = null;
        $this->tag = '';
        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['user', 'category', 'media'])
            ->withCount(['comments' => fn ($q) => $q->approved(), 'views'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                        ->orWhere('content', 'like', '%' . $this->search . '%')
                        ->orWhere('tags', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, fn (Builder $query) => $query->where('category_id', $this->category))
            ->when($this->tag !== '', fn (Builder $query) => $query->where('tags', 'like', '%' . $this->tag . '%'))
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::query()
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name', 'asc')
            ->get();

        $featuredPost = null;
        if (empty($this->search) && empty($this->category) && empty($this->tag) && $this->getPage() === 1) {
            $featuredPost = Post::query()
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->with(['user', 'category', 'media'])
                ->withCount(['comments' => fn ($q) => $q->approved(), 'views'])
                ->latest('published_at')
                ->first();
        }

        return view('livewire.marketplace.blog.blog-list', [
            'posts' => $posts,
            'categories' => $categories,
            'featuredPost' => $featuredPost,
            'activeCategory' => $this->category ? Category::find($this->category) : null,
        ]);
    }
}
