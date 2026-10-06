<?php

namespace App\Livewire\Dashboard\Disputes;

use App\Models\Dispute;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class DisputeView extends Component
{
    public ?Dispute $dispute = null;

    public function mount($dispute_id = null): void
    {
        if ($dispute_id) {
            $this->dispute = Dispute::with(['opener', 'resolver', 'issue.invoice', 'items'])->find($dispute_id);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.disputes.dispute-view', [
            'dispute' => $this->dispute,
        ]);
    }
}
