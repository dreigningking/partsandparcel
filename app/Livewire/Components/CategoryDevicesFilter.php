<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CategoryDevicesFilter extends Component
{
    public $isMobile = false;

    public function mount($isMobile = false)
    {
        $this->isMobile = $isMobile;
    }

    public function render()
    {
        return view('livewire.components.category-devices-filter');
    }
}
