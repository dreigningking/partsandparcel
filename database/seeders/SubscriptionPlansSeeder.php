<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $nigeria = Country::where('code', 'NG')->first() ?? Country::where('is_default', true)->first();

        $plans = [
            [
                'name' => 'Starter Free',
                'slug' => 'starter-free',
                'request_limit' => 1,
                'response_limit' => 1,
                'listing_limit' => 10,
                'escrow_percentage' => 10.00,
                'escrow_cap' => 5000.00,
                'price_monthly' => 0.00,
                'price_annual' => 0.00,
                'features' => [
                    'daily_request_limit' => 1,
                    'daily_response_limit' => 1,
                    'listing_limit' => 10,
                    'escrow_fee' => '10%',
                    'escrow_cap' => 5000.00,
                    'priority_placement' => false,
                    'dedicated_support' => false,
                    'description' => '1 community request/day, 1 quote response/day, up to 10 active listings, 10% escrow fee (capped at ₦5,000)',
                ],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Pro Technician & Vendor',
                'slug' => 'pro-technician-vendor',
                'request_limit' => 10,
                'response_limit' => 10,
                'listing_limit' => 50,
                'escrow_percentage' => 7.00,
                'escrow_cap' => 15000.00,
                'price_monthly' => 2000.00,
                'price_annual' => 20000.00,
                'features' => [
                    'daily_request_limit' => 10,
                    'daily_response_limit' => 10,
                    'listing_limit' => 50,
                    'escrow_fee' => '7%',
                    'escrow_cap' => 15000.00,
                    'priority_placement' => true,
                    'verified_badge' => true,
                    'dedicated_support' => false,
                    'description' => '10 community requests/day, 10 quote responses/day, up to 50 active listings, 7% escrow fee (capped at ₦15,000)',
                ],
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Enterprise Salvage & Dealer',
                'slug' => 'enterprise-salvage-dealer',
                'request_limit' => 50,
                'response_limit' => 50,
                'listing_limit' => 500,
                'escrow_percentage' => 5.00,
                'escrow_cap' => 25000.00,
                'price_monthly' => 5000.00,
                'price_annual' => 50000.00,
                'features' => [
                    'daily_request_limit' => 50,
                    'daily_response_limit' => 50,
                    'listing_limit' => 500,
                    'escrow_fee' => '5%',
                    'escrow_cap' => 25000.00,
                    'priority_placement' => true,
                    'dedicated_arbitration' => true,
                    'dedicated_support' => true,
                    'description' => '50 community requests/day, 50 quote responses/day, up to 500 active listings, 5% escrow fee (capped at ₦25,000), dedicated arbitration',
                ],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                [
                    'name' => $planData['name'],
                    'request_limit' => $planData['request_limit'],
                    'response_limit' => $planData['response_limit'],
                    'listing_limit' => $planData['listing_limit'],
                    'escrow_percentage' => $planData['escrow_percentage'],
                    'escrow_cap' => $planData['escrow_cap'] ?? null,
                    'features' => $planData['features'],
                    'is_active' => $planData['is_active'],
                    'is_default' => $planData['is_default'],
                ]
            );

            // Incorporate plan prices for Nigeria
            if ($nigeria) {
                SubscriptionPlanPrice::updateOrCreate(
                    [
                        'subscription_plan_id' => $plan->id,
                        'country_id' => $nigeria->id,
                    ],
                    [
                        'price_monthly' => $planData['price_monthly'],
                        'price_annual' => $planData['price_annual'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
