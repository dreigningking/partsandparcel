<?php

namespace App\Livewire\Admin;

use App\Models\Revenue;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Platform Revenue — Admin Control Center')]
class AdminRevenue extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = '';

    public function render()
    {
        $revenues = Revenue::query()
            ->with(['invoice.buyer', 'invoice.seller', 'payment'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->whereHas('invoice', fn ($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('payment', fn ($q) => $q->where('reference', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->typeFilter !== '', fn (Builder $query) => $query->where('type', $this->typeFilter))
            ->latest()
            ->paginate(15);

        $totalRevenue = Revenue::sum('amount') ?: 0;
        $commissionRevenue = Revenue::where('type', 'commission')->sum('amount') ?: 0;
        $subscriptionRevenue = Revenue::where('type', 'subscription')->sum('amount') ?: 0;
        $serviceFeeRevenue = Revenue::where('type', 'service_fee')->sum('amount') ?: 0;

        return view('livewire.admin.admin-revenue', [
            'revenues' => $revenues,
            'totalRevenue' => $totalRevenue,
            'commissionRevenue' => $commissionRevenue,
            'subscriptionRevenue' => $subscriptionRevenue,
            'serviceFeeRevenue' => $serviceFeeRevenue,
        ]);
    }
}
