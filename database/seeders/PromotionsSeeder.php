<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Listing;
use App\Models\Promotion;
use App\Models\PromotionPlan;
use Illuminate\Database\Seeder;

class PromotionsSeeder extends Seeder
{
    public function run(): void
    {
        $nigeria = Country::where('code', 'NG')->first() ?? Country::where('is_default', true)->first();

        // 1. Create Promotion Plan for Nigeria (views 0.0050 per view, clicks 20 naira per click)
        $plan = PromotionPlan::updateOrCreate(
            ['slug' => 'nigeria-standard-promotion-plan'],
            [
                'name' => 'Nigeria Standard PPC & Impression Plan',
                'country_id' => $nigeria?->id,
                'views' => 0.0050,
                'clicks' => 20.00,
            ]
        );

        // 2. Active Promotions for Published Listings
        $listings = Listing::where('is_published', true)->where('is_active', true)->get();
        if ($listings->isEmpty()) {
            $this->call(DemoItemsAndListingsSeeder::class);
            $listings = Listing::where('is_published', true)->where('is_active', true)->get();
        }

        // Promote a healthy sample of listings (e.g. 8-12 listings across different sellers)
        $promotedListings = $listings->take(12);

        $types = ['clicks', 'views'];

        foreach ($promotedListings as $index => $listing) {
            $type = $types[$index % 2];
            $achieved = $type === 'clicks' ? rand(5, 75) : rand(120, 1500);

            Promotion::updateOrCreate(
                [
                    'user_id' => $listing->user_id,
                    'listing_id' => $listing->id,
                ],
                [
                    'type' => $type,
                    'achieved_count' => $achieved,
                    'status' => 'active', // USER REQUIREMENT: promotions should be active
                ]
            );
        }
    }
}
