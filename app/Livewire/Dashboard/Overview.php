<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class Overview extends Component
{
    public $title = "Dashboard";

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}
