<?php

namespace App\Livewire\Marketplace\Listings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Category extends Component
{
    public $title = "Electronics & Devices — Parts & Parcel";

    #[Url(as: 'tab', keep: true)]
    public $tab = 'complete';

    public function switchTab($tabName)
    {
        if (in_array($tabName, ['complete', 'parts', 'scrap', 'community', 'requests'])) {
            $this->tab = ($tabName === 'community') ? 'requests' : $tabName;
        }
    }

    public function render()
    {
        return view('livewire.marketplace.listings.category');
    }
}
