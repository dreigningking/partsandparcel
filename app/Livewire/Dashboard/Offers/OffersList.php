<?php

namespace App\Livewire\Dashboard\Offers;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OffersList extends Component
{
    public $activeTab = 'received'; // 'received', 'sent', 'accepted', 'declined', 'all'
    public $searchQuery = '';

    public function render()
    {
        return view('livewire.dashboard.offers.offers-list');
    }
}