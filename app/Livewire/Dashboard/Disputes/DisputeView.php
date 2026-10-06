<?php

namespace App\Livewire\Dashboard\Disputes;

use App\Models\Dispute;
use App\Services\PostSale\IssueResolutionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Dispute Case Arbitration — Dashboard')]
class DisputeView extends Component
{
    public ?Dispute $dispute = null;

    // Admin resolution actions
    public string $adminDecision = 'buyer_favor'; // buyer_favor, seller_favor, split
    public ?float $adminRefundAmount = null;
    public string $adminResolutionNotes = '';

    public function mount($dispute_id = null, $dispute = null): void
    {
        $id = $dispute_id ?? ($dispute instanceof Dispute ? $dispute->id : $dispute);

        $relations = [
            'invoice.buyer',
            'invoice.seller',
            'invoice.items',
            'invoice.settlement',
            'opener',
            'respondent',
            'resolver',
            'issue.items.invoiceItem',
            'warrantyClaim.item',
            'warrantyClaim.invoiceItem',
            'replacement.shipment',
            'returnRecord.shipment',
            'items',
        ];

        if ($id) {
            $this->dispute = Dispute::with($relations)->find($id);
        }

        if (! $this->dispute) {
            $user = Auth::user();
            if ($user) {
                $this->dispute = Dispute::with($relations)
                    ->where(function ($q) use ($user) {
                        $q->where('opened_by', $user->id)
                          ->orWhere('respondent_id', $user->id);
                    })
                    ->latest()
                    ->first();
            }
        }
    }

    public function adminResolve(): void
    {
        $user = Auth::user();
        if (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin()) {
            session()->flash('error', 'Only platform administrators can resolve dispute arbitrations.');
            return;
        }

        if (! $this->dispute || $this->dispute->status === 'resolved') {
            return;
        }

        $this->validate([
            'adminResolutionNotes' => 'required|string|min:10|max:1000',
        ]);

        app(IssueResolutionService::class)->adminResolveDispute(
            dispute: $this->dispute,
            admin: $user,
            decision: $this->adminDecision,
            refundAmount: $this->adminRefundAmount,
            resolutionNotes: $this->adminResolutionNotes
        );

        $this->dispute->refresh();
        session()->flash('resolution_success', 'Dispute has been successfully arbitrated and resolved. Escrow accounts have been updated.');
    }

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        $isParty = $this->dispute && ($this->dispute->opened_by === $user?->id || $this->dispute->respondent_id === $user?->id);

        return view('livewire.dashboard.disputes.dispute-view', [
            'dispute' => $this->dispute,
            'isAdmin' => $isAdmin,
            'isParty' => $isParty,
        ]);
    }
}
