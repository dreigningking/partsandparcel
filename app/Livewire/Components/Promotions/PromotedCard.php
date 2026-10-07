<?php

namespace App\Livewire\Components\Promotions;

use App\Models\Listing;
use Livewire\Component;

class PromotedCard extends Component
{
    public Listing $listing;
    public bool $isPromoted = false;

    public function mount(Listing $listing, bool $isPromoted = false)
    {
        $this->listing = $listing;
        $this->isPromoted = $isPromoted || (bool) $listing->activePromotion;
    }

    public function render()
    {
        return view('livewire.components.promotions.promoted-card');
    }
}
