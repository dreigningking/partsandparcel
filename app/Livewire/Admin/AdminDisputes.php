<?php

namespace App\Livewire\Admin;

use App\Models\Dispute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Disputes Arbitration — Admin Control Center')]
class AdminDisputes extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $selectedDisputeId = null;
    public string $resolutionNote = '';

    public function showDispute(int $id): void
    {
        $this->selectedDisputeId = $id;
        $this->resolutionNote = '';
    }

    public function closeDispute(): void
    {
        $this->selectedDisputeId = null;
    }

    public function resolveDispute(string $resolutionOutcome): void
    {
        $dispute = Dispute::findOrFail($this->selectedDisputeId);
        $dispute->status = 'resolved';
        $dispute->resolution = $resolutionOutcome . ($this->resolutionNote ? ": {$this->resolutionNote}" : '');
        $dispute->resolved_by = Auth::id();
        $dispute->resolved_at = now();
        $dispute->save();

        session()->flash('status', __("Dispute #DSP-{$dispute->id} resolved with outcome: {$resolutionOutcome}."));
        $this->closeDispute();
    }

    public function render()
    {
        $disputes = Dispute::query()
            ->with(['issue.invoice.buyer', 'issue.invoice.seller', 'opener', 'resolver', 'items'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('reason', 'like', '%' . $this->search . '%')
                        ->orWhereHas('opener', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('issue.invoice', fn ($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(12);

        $selectedDispute = $this->selectedDisputeId
            ? Dispute::with(['issue.invoice.buyer', 'issue.invoice.seller', 'opener', 'resolver', 'items.item', 'returnRecord', 'refund'])->find($this->selectedDisputeId)
            : null;

        return view('livewire.admin.admin-disputes', [
            'disputes' => $disputes,
            'selectedDispute' => $selectedDispute,
            'totalCount' => Dispute::count(),
            'openCount' => Dispute::whereIn('status', ['opened', 'pending', 'escalated'])->count(),
            'resolvedCount' => Dispute::where('status', 'resolved')->count(),
        ]);
    }
}
