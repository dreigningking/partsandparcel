<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class Wishlists extends Component
{
    public function render()
    {
        return view('livewire.dashboard.wishlists');
    }
}
