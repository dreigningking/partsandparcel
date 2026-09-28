<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Item;
use App\Models\Listing;
use Livewire\Component;

class CategoryScraps extends Component
{
    public function render()
    {
        $listings = Listing::with(['assetable.deviceModel.category', 'assetable.deviceModel.brand', 'location'])
            ->where('status', 'active')
            ->whereHasMorph('assetable', [Item::class], fn($q) => $q->where('item_type', 'scrap'))
            ->latest()
            ->get();

        return view('livewire.marketplace.listings.category-scraps', [
            'listings' => $listings,
        ]);
    }
}
