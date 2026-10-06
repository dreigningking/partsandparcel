<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('User Accounts — Admin Control Center')]
class AdminUsers extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'country')]
    public string $country = 'all';

    #[Url(as: 'plan')]
    public string $subscriptionPlan = 'all';

    #[Url(as: 'filter')]
    public string $filter = 'all';

    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public ?int $previewUserId = null;
    public bool $showPreviewModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCountry(): void
    {
        $this->resetPage();
    }

    public function updatingSubscriptionPlan(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function updatingSortDir(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = in_array($field, ['name', 'business_name'], true) ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->country = 'all';
        $this->subscriptionPlan = 'all';
        $this->filter = 'all';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function preview(int $userId): void
    {
        $this->previewUserId = $userId;
        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewUserId = null;
    }

    public function toggleSuspend(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot suspend your own admin account.'));
            return;
        }

        if ($user->isSuspended()) {
            $user->update(['suspended_at' => null]);
            session()->flash('status', __("User account ':name' has been unsuspended.", ['name' => $user->name]));
        } else {
            $user->update(['suspended_at' => now()]);
            session()->flash('status', __("User account ':name' has been suspended.", ['name' => $user->name]));
        }

        $this->dispatch('user-status-updated');
    }

    public function render()
    {
        // High-level KPI Stats
        $totalCount = User::count();
        $activeSubscribersCount = User::whereHas('activeSubscription', function (Builder $q) {
            $q->whereHas('plan', fn (Builder $p) => $p->where('slug', '!=', 'starter-free'));
        })->count();
        $verifiedCount = User::where('is_verified', true)->count();
        $businessCount = User::whereNotNull('business_name')->where('business_name', '!=', '')->count();
        $sellersCount = User::has('listings')->count();
        $suspendedCount = User::whereNotNull('suspended_at')->count();

        // Build User query
        $allowedSorts = ['name', 'business_name', 'listings_count', 'live_listings_count', 'created_at'];
        $sortBy = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'created_at';
        $sortDir = in_array(strtolower($this->sortDir), ['asc', 'desc'], true) ? strtolower($this->sortDir) : 'desc';

        $users = User::query()
            ->with([
                'country',
                'activeSubscription.plan',
                'role',
            ])
            ->withCount([
                'listings',
                'listings as live_listings_count' => function (Builder $query) {
                    $query->where('is_published', true)
                        ->where('is_active', true)
                        ->whereDoesntHave('latestModeration', fn (Builder $m) => $m->where('status', 'rejected'));
                },
            ])
            // Search filter: name, business_name, email, phone
            ->when($this->search !== '', function (Builder $query) {
                $term = '%'.$this->search.'%';
                $query->where(function (Builder $inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('business_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            // Country filter
            ->when($this->country !== 'all' && is_numeric($this->country), function (Builder $query) {
                $query->where('country_id', (int) $this->country);
            })
            // Subscription Plan filter
            ->when($this->subscriptionPlan !== 'all', function (Builder $query) {
                if ($this->subscriptionPlan === 'free' || $this->subscriptionPlan === 'none') {
                    $query->where(function (Builder $q) {
                        $q->whereDoesntHave('activeSubscription')
                            ->orWhereHas('activeSubscription', function (Builder $sub) {
                                $sub->whereHas('plan', fn (Builder $p) => $p->where('slug', 'starter-free')->orWhere('name', 'like', '%free%'));
                            });
                    });
                } elseif (is_numeric($this->subscriptionPlan)) {
                    $query->whereHas('activeSubscription', function (Builder $sub) {
                        $sub->where('subscription_plan_id', (int) $this->subscriptionPlan);
                    });
                }
            })
            // Status Tab Quick filter
            ->when($this->filter === 'subscribers', function (Builder $query) {
                $query->whereHas('activeSubscription', function (Builder $q) {
                    $q->whereHas('plan', fn (Builder $p) => $p->where('slug', '!=', 'starter-free'));
                });
            })
            ->when($this->filter === 'verified', function (Builder $query) {
                $query->where('is_verified', true);
            })
            ->when($this->filter === 'business', function (Builder $query) {
                $query->whereNotNull('business_name')->where('business_name', '!=', '');
            })
            ->when($this->filter === 'sellers', function (Builder $query) {
                $query->has('listings');
            })
            ->when($this->filter === 'suspended', function (Builder $query) {
                $query->whereNotNull('suspended_at');
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate($this->perPage);

        // Preview User (if modal open)
        $previewUser = null;
        if ($this->showPreviewModal && $this->previewUserId) {
            $previewUser = User::query()
                ->with([
                    'country',
                    'primaryLocation',
                    'locations.state',
                    'activeSubscription.plan',
                    'role',
                    'latestVerification',
                    'listings' => fn ($l) => $l->latest()->take(5),
                ])
                ->withCount([
                    'listings',
                    'listings as live_listings_count' => fn (Builder $q) => $q->where('is_published', true)->where('is_active', true),
                ])
                ->find($this->previewUserId);
        }

        $countries = Country::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'currency_symbol']);

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('livewire.admin.admin-users', [
            'users' => $users,
            'countries' => $countries,
            'plans' => $plans,
            'totalCount' => $totalCount,
            'activeSubscribersCount' => $activeSubscribersCount,
            'verifiedCount' => $verifiedCount,
            'businessCount' => $businessCount,
            'sellersCount' => $sellersCount,
            'suspendedCount' => $suspendedCount,
            'previewUser' => $previewUser,
            'search' => $this->search,
            'country' => $this->country,
            'subscriptionPlan' => $this->subscriptionPlan,
            'filter' => $this->filter,
            'sortBy' => $this->sortBy,
            'sortDir' => $this->sortDir,
            'perPage' => $this->perPage,
            'showPreviewModal' => $this->showPreviewModal,
        ]);
    }
}
