<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CategoryScrapsFilter extends Component
{
    public $isMobile = false;

    public function mount($isMobile = false)
    {
        $this->isMobile = $isMobile;
    }

    public function render()
    {
        return view('livewire.components.category-scraps-filter');
    }
}
