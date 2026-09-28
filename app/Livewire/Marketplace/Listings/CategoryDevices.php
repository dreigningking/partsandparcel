<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Item;
use App\Models\Listing;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CategoryDevices extends Component
{
    public function render()
    {
        $listings = Listing::with(['assetable.deviceModel.category', 'assetable.deviceModel.brand', 'location'])
            ->where('status', 'active')
            ->whereHasMorph('assetable', [Item::class], fn($q) => $q->where('item_type', 'whole'))
            ->latest()
            ->get();

        return view('livewire.marketplace.listings.category-devices', [
            'listings' => $listings,
        ]);
    }
}
