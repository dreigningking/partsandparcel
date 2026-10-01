<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'name';

    #[Url(as: 'dir')]
    public string $sortDir = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $field;
        $this->sortDir = 'asc';
    }

    public function render()
    {
        $allowedSorts = ['name', 'properties_count', 'subscription_seats'];
        $sortBy = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'name';

        $users = User::query()
            ->with(['country', 'subscriptions'])
            ->withCount('properties')
            ->withSum('subscriptions as subscription_seats', 'seats')
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($sortBy, $this->sortDir)
            ->paginate(10);

        return view('livewire.admin.admin-users', ['users' => $users]);
    }
}
