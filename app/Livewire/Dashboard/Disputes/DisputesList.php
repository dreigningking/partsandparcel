<?php

namespace App\Livewire\Dashboard\Disputes;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class DisputesList extends Component
{
    public function render()
    {
        return view('livewire.dashboard.disputes.disputes-list');
    }
}
