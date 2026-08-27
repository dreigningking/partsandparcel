<?php

namespace App\Livewire\Dashboard\Shipments;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class ShipmentsList extends Component
{
    public function render()
    {
        return view('livewire.dashboard.shipments.shipments-list');
    }
}
