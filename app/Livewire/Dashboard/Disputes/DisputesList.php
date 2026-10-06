<?php

namespace App\Livewire\Dashboard\Disputes;

use App\Models\Dispute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Dispute & Mediation Resolution Center — Dashboard')]
class DisputesList extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $statusFilter = 'open'; // 'open', 'resolved', 'all'

    #[Url(as: 'q')]
    public string $search = '';

    public function setFilter(string $filter): void
    {
        if (in_array($filter, ['open', 'resolved', 'all'], true)) {
            $this->statusFilter = $filter;
            $this->resetPage();
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

        $baseQuery = Dispute::query()
            ->when(! $isAdmin, function (Builder $query) use ($userId) {
                $query->where(function (Builder $sub) use ($userId) {
                    $sub->where('opened_by', $userId)
                        ->orWhere('respondent_id', $userId);
                });
            });

        $openCount = (clone $baseQuery)->whereIn('status', ['open', 'escalated', 'pending'])->count();
        $resolvedCount = (clone $baseQuery)->where('status', 'resolved')->count();
        $totalCount = (clone $baseQuery)->count();

        $disputes = (clone $baseQuery)
            ->with(['invoice.buyer', 'invoice.seller', 'opener', 'respondent', 'resolver', 'issue', 'warrantyClaim', 'replacement', 'returnRecord'])
            ->when($this->statusFilter === 'open', fn (Builder $q) => $q->whereIn('status', ['open', 'escalated', 'pending']))
            ->when($this->statusFilter === 'resolved', fn (Builder $q) => $q->where('status', 'resolved'))
            ->when($this->search !== '', function (Builder $query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function (Builder $sub) use ($term) {
                    $sub->where('id', 'like', $term)
                        ->orWhere('reason', 'like', $term)
                        ->orWhere('type', 'like', $term)
                        ->orWhereHas('invoice', fn ($iq) => $iq->where('invoice_number', 'like', $term))
                        ->orWhereHas('opener', fn ($uq) => $uq->where('name', 'like', $term))
                        ->orWhereHas('respondent', fn ($uq) => $uq->where('name', 'like', $term));
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.disputes.disputes-list', [
            'disputes' => $disputes,
            'openCount' => $openCount,
            'resolvedCount' => $resolvedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
