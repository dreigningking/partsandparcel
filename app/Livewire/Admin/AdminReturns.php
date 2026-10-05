<?php

namespace App\Livewire\Admin;

use App\Models\Replacement;
use App\Models\ReturnRecord;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Returns & Replacements — Admin Control Center')]
class AdminReturns extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'returns'; // returns, replacements

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $selectedRecordId = null;

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['returns', 'replacements'])) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function showRecord(int $id): void
    {
        $this->selectedRecordId = $id;
    }

    public function closeRecord(): void
    {
        $this->selectedRecordId = null;
    }

    public function updateReturnStatus(int $id, string $status): void
    {
        $return = ReturnRecord::findOrFail($id);
        $return->status = $status;
        if ($status === 'received' && !$return->received_at) {
            $return->received_at = now();
        } elseif ($status === 'accepted' && !$return->accepted_at) {
            $return->accepted_at = now();
        }
        $return->save();
        session()->flash('status', __("Return #{$return->id} marked as {$status}."));
    }

    public function render()
    {
        if ($this->activeTab === 'returns') {
            $records = ReturnRecord::query()
                ->with(['invoice', 'issue', 'buyer', 'seller', 'items.item'])
                ->when($this->search !== '', function (Builder $query) {
                    $query->where(function (Builder $inner) {
                        $inner->whereHas('buyer', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('seller', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('invoice', fn ($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'));
                    });
                })
                ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(12);

            $selectedRecord = $this->selectedRecordId
                ? ReturnRecord::with(['invoice', 'issue', 'buyer', 'seller', 'shipment', 'items.item'])->find($this->selectedRecordId)
                : null;
        } else {
            $records = Replacement::query()
                ->with(['invoice', 'issue', 'buyer', 'seller', 'items'])
                ->when($this->search !== '', function (Builder $query) {
                    $query->where(function (Builder $inner) {
                        $inner->whereHas('buyer', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('seller', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                    });
                })
                ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(12);

            $selectedRecord = $this->selectedRecordId
                ? Replacement::with(['invoice', 'issue', 'buyer', 'seller', 'items'])->find($this->selectedRecordId)
                : null;
        }

        return view('livewire.admin.admin-returns', [
            'activeTab' => $this->activeTab,
            'search' => $this->search,
            'status' => $this->status,
            'records' => $records,
            'selectedRecord' => $selectedRecord,
            'returnsCount' => ReturnRecord::count(),
            'replacementsCount' => Replacement::count(),
            'pendingReturnsCount' => ReturnRecord::whereIn('status', ['requested', 'pending'])->count(),
        ]);
    }
}
