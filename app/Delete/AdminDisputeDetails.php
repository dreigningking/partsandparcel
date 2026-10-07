<?php

namespace App\Livewire\Admin;


use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Admin Control Center')]
class AdminDisputeDetails extends Component
{
    
    public function render()
    {
        return view('livewire.admin.admin-dispute-view');
    }

}
