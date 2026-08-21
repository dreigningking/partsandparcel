<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class Dashboard extends Component
{
    public $title = "Dashboard";

    public function render()
    {
        return view('livewire.dashboard');
    }
}
