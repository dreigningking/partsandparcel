<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Coupons & Discounts — Admin Control Center')]
class AdminCoupons extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
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
        $this->type = '';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function setSort(string $by): void
    {
        if ($this->sortBy === $by) {
            $this->sortDir = $this->sortDir === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sortBy = $by;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function toggleStatus(int $couponId): void
    {
        $coupon = Coupon::query()->findOrFail($couponId);
        $coupon->update(['is_active' => ! $coupon->is_active]);

        session()->flash('status', __('Coupon :code status updated to :status.', [
            'code' => $coupon->code,
            'status' => $coupon->is_active ? 'Active' : 'Inactive',
        ]));
    }

    public function deleteCoupon(int $couponId): void
    {
        $coupon = Coupon::query()->findOrFail($couponId);
        $code = $coupon->code;
        $coupon->delete();

        session()->flash('status', __('Coupon :code deleted successfully.', ['code' => $code]));
    }

    public function render()
    {
        $allowedSorts = ['created_at', 'code', 'value', 'used_count', 'expires_at'];
        $sortColumn = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'created_at';
        $sortDirection = in_array(strtolower($this->sortDir), ['asc', 'desc'], true) ? $this->sortDir : 'desc';

        $coupons = Coupon::query()
            ->when($this->search !== '', fn (Builder $query) => $query->where('code', 'like', '%'.$this->search.'%'))
            ->when($this->type !== '', fn (Builder $query) => $query->where('type', $this->type))
            ->when($this->status !== '', function (Builder $query) {
                if ($this->status === 'active') {
                    $query->where('is_active', true)
                        ->where(fn (Builder $sq) => $sq->whereNull('expires_at')->orWhere('expires_at', '>', now()));
                } elseif ($this->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($this->status === 'expired') {
                    $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
                }
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12);

        $totalCoupons = Coupon::query()->count();
        $activeCoupons = Coupon::query()
            ->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
        $totalRedemptions = (int) Coupon::query()->sum('used_count');
        $expiredCoupons = Coupon::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->count();

        return view('livewire.admin.admin-coupons', [
            'coupons' => $coupons,
            'totalCoupons' => $totalCoupons,
            'activeCoupons' => $activeCoupons,
            'totalRedemptions' => $totalRedemptions,
            'expiredCoupons' => $expiredCoupons,
            'search' => $this->search,
            'status' => $this->status,
            'type' => $this->type,
            'sortBy' => $this->sortBy,
            'sortDir' => $this->sortDir,
        ]);
    }
}
