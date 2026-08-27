<?php

namespace App\Livewire\Dashboard\Responses;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyResponseView extends Component
{
    public function render()
    {
        return view('livewire.dashboard.responses.my-response-view');
    }
}
