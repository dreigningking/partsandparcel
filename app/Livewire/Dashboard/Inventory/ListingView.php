<?php

namespace App\Livewire\Dashboard\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class ListingView extends Component
{
    public function render()
    {
        return view('livewire.dashboard.inventory.listing-view');
    }
}
