<?php

namespace App\Livewire\Dashboard\Offers;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OfferNegotiations extends Component
{
    public function render()
    {
        return view('livewire.dashboard.offers.offer-negotiations');
    }
}
