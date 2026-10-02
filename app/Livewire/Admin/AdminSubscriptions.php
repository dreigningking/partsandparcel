<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Subscriptions & Member Tiers — Admin Control Center')]
class AdminSubscriptions extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'plan')]
    public string $planId = '';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'starts_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public ?int $selectedSubscriptionId = null;

    public bool $showDetailsModal = false;

    public bool $showEditModal = false;

    // Edit fields
    public string $editStatus = 'active';

    public ?int $editPlanId = null;

    public string $editStartsAt = '';

    public string $editEndsAt = '';

    public int $editRequestLimit = 1;

    public int $editResponseLimit = 1;

    public int $editListingLimit = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPlanId(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
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

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->planId = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->sortBy = 'starts_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function openDetails(int $id): void
    {
        $this->selectedSubscriptionId = $id;
        $this->showDetailsModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedSubscriptionId = null;
    }

    public function openEdit(int $id): void
    {
        $sub = Subscription::query()->findOrFail($id);

        $this->selectedSubscriptionId = $sub->id;
        $this->editStatus = $sub->status;
        $this->editPlanId = $sub->subscription_plan_id;
        $this->editStartsAt = optional($sub->starts_at)->format('Y-m-d') ?? '';
        $this->editEndsAt = optional($sub->ends_at)->format('Y-m-d') ?? '';
        $this->editRequestLimit = (int) $sub->request_limit;
        $this->editResponseLimit = (int) $sub->response_limit;
        $this->editListingLimit = (int) $sub->listing_limit;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->selectedSubscriptionId = null;
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editStatus' => ['required', Rule::in(['active', 'cancelled', 'expired', 'pending'])],
            'editPlanId' => ['required', 'exists:subscription_plans,id'],
            'editStartsAt' => ['required', 'date'],
            'editEndsAt' => ['required', 'date', 'after_or_equal:editStartsAt'],
            'editRequestLimit' => ['required', 'integer', 'min:0'],
            'editResponseLimit' => ['required', 'integer', 'min:0'],
            'editListingLimit' => ['required', 'integer', 'min:0'],
        ]);

        $sub = Subscription::query()->findOrFail($this->selectedSubscriptionId);
        $sub->update([
            'status' => $this->editStatus,
            'subscription_plan_id' => $this->editPlanId,
            'starts_at' => Carbon::parse($this->editStartsAt)->startOfDay(),
            'ends_at' => Carbon::parse($this->editEndsAt)->endOfDay(),
            'request_limit' => $this->editRequestLimit,
            'response_limit' => $this->editResponseLimit,
            'listing_limit' => $this->editListingLimit,
        ]);

        session()->flash('status', __('Subscription updated successfully.'));
        $this->closeEdit();
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        if (! in_array($newStatus, ['active', 'cancelled', 'expired', 'pending'], true)) {
            return;
        }

        $sub = Subscription::query()->findOrFail($id);
        $sub->update(['status' => $newStatus]);

        session()->flash('status', __('Subscription marked as :status.', ['status' => ucfirst($newStatus)]));
    }

    public function deleteSubscription(int $id): void
    {
        $sub = Subscription::query()->findOrFail($id);
        $sub->delete();

        if ($this->selectedSubscriptionId === $id) {
            $this->closeDetails();
            $this->closeEdit();
        }

        session()->flash('status', __('Subscription record deleted successfully.'));
    }

    public function render()
    {
        $allowedSorts = ['starts_at', 'ends_at', 'id'];
        $sortColumn = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'starts_at';
        $sortDirection = in_array(strtolower($this->sortDir), ['asc', 'desc'], true) ? $this->sortDir : 'desc';

        $subscriptions = Subscription::query()
            ->with(['user', 'plan.prices.country', 'payments'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $sub) {
                    $sub->whereHas('user', function (Builder $uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })->orWhereHas('plan', function (Builder $pq) {
                        $pq->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('slug', 'like', '%'.$this->search.'%');
                    });
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->planId !== '', fn (Builder $query) => $query->where('subscription_plan_id', $this->planId))
            ->when($this->dateFrom !== '', fn (Builder $query) => $query->whereDate('starts_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn (Builder $query) => $query->whereDate('starts_at', '<=', $this->dateTo))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12);

        $totalSubscriptions = Subscription::query()->count();
        $activeSubscriptions = Subscription::query()->where('status', 'active')->count();
        $expiredSubscriptions = Subscription::query()
            ->where(function (Builder $q) {
                $q->where('status', 'expired')
                    ->orWhere(fn (Builder $sq) => $sq->where('ends_at', '<', now())->where('status', '!=', 'active'));
            })
            ->count();

        $totalRevenue = (float) Payment::query()
            ->where('paymentable_type', Subscription::class)
            ->whereIn('status', ['completed', 'success'])
            ->sum('amount');

        $plans = SubscriptionPlan::query()->orderBy('name')->get();

        $selectedSubscription = $this->selectedSubscriptionId
            ? Subscription::query()->with(['user', 'plan.prices.country', 'payments'])->find($this->selectedSubscriptionId)
            : null;

        return view('livewire.admin.admin-subscriptions', [
            'subscriptions' => $subscriptions,
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'expiredSubscriptions' => $expiredSubscriptions,
            'totalRevenue' => $totalRevenue,
            'plans' => $plans,
            'selectedSubscription' => $selectedSubscription,
            'search' => $this->search,
            'status' => $this->status,
            'planId' => $this->planId,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'sortBy' => $this->sortBy,
            'sortDir' => $this->sortDir,
            'showDetailsModal' => $this->showDetailsModal,
            'showEditModal' => $this->showEditModal,
            'editStatus' => $this->editStatus,
            'editPlanId' => $this->editPlanId,
            'editStartsAt' => $this->editStartsAt,
            'editEndsAt' => $this->editEndsAt,
            'editRequestLimit' => $this->editRequestLimit,
            'editResponseLimit' => $this->editResponseLimit,
            'editListingLimit' => $this->editListingLimit,
        ]);
    }
}
