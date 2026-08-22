<?php

namespace App\Livewire\Marketplace\Listings;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ItemDetails extends Component
{
    public $title = "Dell Latitude 5420 Laptop For Parts / Salvage — Parts & Parcel";

    public function render()
    {
        return view('livewire.marketplace.listings.item-details');
    }
}
