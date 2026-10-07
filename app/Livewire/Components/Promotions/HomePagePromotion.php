<?php

namespace App\Livewire\Components\Promotions;

use App\Services\Promotion\PromotionService;
use Illuminate\Support\Collection;
use Livewire\Component;

class HomePagePromotion extends Component
{
    public bool $isLoaded = false;
    public ?string $stateName = null;
    public ?string $countryName = null;

    /**
     * @var Collection|\App\Models\Listing[]
     */
    public $listings = [];

    public function loadFeaturedListings(PromotionService $promotionService)
    {
        $loc = $promotionService->resolveVisitorLocation();
        $this->stateName = $loc['state_name'];
        $this->countryName = $loc['country_name'];

        $retrieved = $promotionService->getFeaturedListings(limit: 10, backfill: true);

        // Process impressions for active promotions with type === 'views' (deduplicated by ViewedEntity)
        $promotionService->processImpressionPromotions($retrieved);

        $this->listings = $retrieved;
        $this->isLoaded = true;
    }

    public function render()
    {
        return view('livewire.components.promotions.home-page-promotion');
    }
}
