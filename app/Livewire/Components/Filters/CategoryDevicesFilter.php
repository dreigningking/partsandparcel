<?php

namespace App\Livewire\Components\Filters;

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
        return view('livewire.components.filters.category-devices-filter');
    }
}
