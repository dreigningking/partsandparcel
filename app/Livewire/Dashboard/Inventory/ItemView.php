<?php

namespace App\Livewire\Dashboard\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]

class ItemView extends Component
{
    public function render()
    {
        return view('livewire.dashboard.inventory.item-view');
    }
}
