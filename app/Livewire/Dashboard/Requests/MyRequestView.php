<?php

namespace App\Livewire\Dashboard\Requests;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyRequestView extends Component
{
    public function render()
    {
        return view('livewire.dashboard.requests.my-request-view');
    }
}
