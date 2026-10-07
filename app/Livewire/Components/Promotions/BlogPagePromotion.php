<?php

namespace App\Livewire\Components\Promotions;

use App\Models\Listing;
use App\Services\Promotion\PromotionService;
use Livewire\Component;

class BlogPagePromotion extends Component
{
    public bool $isLoaded = false;
    public ?Listing $listing = null;

    public function loadFeaturedListing(PromotionService $promotionService)
    {
        $featured = $promotionService->getFeaturedListings(limit: 1);

        if ($featured->isNotEmpty()) {
            $this->listing = $featured->first();

            // Process impressions for active promotions with type === 'views'
            $promotionService->processImpressionPromotions(collect([$this->listing]));
        }

        $this->isLoaded = true;
    }

    public function render()
    {
        return view('livewire.components.promotions.blog-page-promotion');
    }
}
