<?php

namespace App\Livewire\Admin;

use App\Models\Payout;
use App\Models\Settlement;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Payouts & Settlements — Admin Control Center')]
class AdminPayouts extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'payouts'; // payouts, settlements

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['payouts', 'settlements'])) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function markSettlementEligible(int $id): void
    {
        $settlement = Settlement::findOrFail($id);
        if ($settlement->seller && $settlement->seller->freeze_payout) {
            session()->flash('error', __("Cannot mark eligible: Seller payouts are frozen."));
            return;
        }
        $settlement->status = 'eligible';
        $settlement->save();
        session()->flash('status', __("Settlement #{$settlement->id} marked as eligible for payout."));
    }

    public function markSettlementPaid(int $id): void
    {
        $settlement = Settlement::findOrFail($id);
        if ($settlement->seller && $settlement->seller->freeze_payout) {
            session()->flash('error', __("Cannot process payout: Seller payouts are frozen."));
            return;
        }
        $settlement->update([
            'status' => 'settled',
            'settled_at' => now(),
            'payout_reference' => 'PAYOUT-' . strtoupper(\Illuminate\Support\Str::random(10)),
        ]);
        session()->flash('status', __("Settlement #{$settlement->id} marked as settled/paid."));
    }

    public function markPayoutCompleted(int $id): void
    {
        $payout = Payout::findOrFail($id);
        $payout->status = 'paid';
        $payout->paid_at = now();
        $payout->save();

        // Mark associated settlements as settled
        foreach ($payout->settlements as $settlement) {
            $settlement->status = 'settled';
            $settlement->settled_at = now();
            $settlement->save();
        }

        session()->flash('status', __("Payout #{$payout->reference} marked as completed."));
    }

    public function render()
    {
        if ($this->activeTab === 'payouts') {
            $items = Payout::query()
                ->with(['seller', 'settlements'])
                ->when($this->search !== '', function (Builder $query) {
                    $query->where(function (Builder $inner) {
                        $inner->where('reference', 'like', '%' . $this->search . '%')
                            ->orWhereHas('seller', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                    });
                })
                ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(12);
        } else {
            $items = Settlement::query()
                ->with(['seller', 'invoice', 'payment'])
                ->when($this->search !== '', function (Builder $query) {
                    $query->where(function (Builder $inner) {
                        $inner->whereHas('seller', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('invoice', fn ($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('payment', fn ($q) => $q->where('reference', 'like', '%' . $this->search . '%'));
                    });
                })
                ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(12);
        }

        $totalPaidOut = Payout::where('status', 'paid')->sum('amount') ?: 0;
        $pendingSettlementsAmount = Settlement::whereIn('status', ['pending', 'eligible'])->sum('amount') ?: 0;

        return view('livewire.admin.admin-payouts', [
            'items' => $items,
            'activeTab' => $this->activeTab,
            'search' => $this->search,
            'status' => $this->status,
            'totalPaidOut' => $totalPaidOut,
            'pendingSettlementsAmount' => $pendingSettlementsAmount,
            'payoutsCount' => Payout::count(),
            'settlementsCount' => Settlement::count(),
        ]);
    }
}
