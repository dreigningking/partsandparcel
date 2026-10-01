<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCoupons extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'qty_remaining';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

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
        $coupon->update(['is_published' => ! $coupon->is_published]);
    }

    public function deleteCoupon(int $couponId): void
    {
        Coupon::query()->findOrFail($couponId)->delete();
    }

    public function render()
    {
        $dir = strtolower($this->sortDir) === 'asc' ? 'asc' : 'desc';

        $coupons = Coupon::query()
            ->withCount('payments')
            ->when($this->search !== '', fn (Builder $query) => $query->where('code', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', function (Builder $query) {
                $query->where('is_published', $this->status === 'published');
            })
            ->when($this->sortBy === 'qty_used', fn (Builder $query) => $query->orderBy('payments_count', $dir))
            ->when($this->sortBy === 'qty_remaining', fn (Builder $query) => $query->orderByRaw('(quantity - payments_count) '.$dir))
            ->paginate(15);

        foreach ($coupons as $coupon) {
            $coupon->qty_used = (int) $coupon->payments_count;
            $coupon->qty_remaining = max(0, (int) $coupon->quantity - (int) $coupon->payments_count);
        }

        return view('livewire.admin.admin-coupons', [
            'coupons' => $coupons,
        ]);
    }
}
