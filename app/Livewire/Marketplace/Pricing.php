<?php

namespace App\Livewire\Marketplace;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pricing & Subscription Plans — Parts & Parcel')]
class Pricing extends Component
{
    public function render()
    {
        return view('livewire.marketplace.pricing');
    }
}
