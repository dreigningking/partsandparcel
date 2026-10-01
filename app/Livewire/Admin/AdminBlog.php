<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminBlog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'category')]
    public ?int $category = null;

    public function render()
    {
        $posts = Post::query()
            ->with(['user', 'category', 'listing'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('title', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->category, fn (Builder $query) => $query->where('category_id', $this->category))
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.admin.admin-blog', [
            'posts' => $posts,
            'statuses' => Post::query()->distinct()->pluck('status')->filter()->values(),
            'categories' => Category::query()->orderBy('name','asc')->get(['id', 'name']),
        ]);
    }
}
