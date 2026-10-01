<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminListings extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?int $category = null;

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $listingId): void
    {
        Listing::query()->findOrFail($listingId)->delete();
    }

    public function render()
    {
        $properties = Listing::query()
            ->with(['user', 'category'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', fn(Builder $userQuery) => $userQuery->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->category, fn(Builder $query) => $query->where('category_id', $this->category))
            ->when($this->status !== '', function (Builder $query) {
                if ($this->status === 'draft') {
                    $query->where('is_published', false);

                    return;
                }

                $query->whereHas('latestModeration', fn(Builder $m) => $m->where('status', $this->status));
            })
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.admin.admin-properties', [
            'properties' => $properties,
            'categories' => Category::query()->where('is_listing', true)->orderBy('name', 'asc')->get(['id', 'name']),
            'statusOptions' => ['draft', 'pending', 'live', 'no subscription', 'rejected'],
        ]);
    }
}
