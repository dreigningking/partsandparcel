<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Free',
                'price' => 0.00,
                'price_annual' => 0.00,
                'billing_interval' => 'monthly',
                'request_limit' => 1,
                'response_limit' => 1,
                'listing_limit' => 10,
                'features' => [
                    'daily_request_limit' => 1,
                    'daily_response_limit' => 1,
                    'listing_limit' => 10,
                    'price_monthly' => 0.00,
                    'price_annual' => 0.00,
                    'priority_placement' => false,
                    'description' => '1 community request/day, 1 quote response/day, up to 10 active listings',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro Technician & Vendor',
                'price' => 2000.00,
                'price_annual' => 20000.00,
                'billing_interval' => 'monthly',
                'request_limit' => 10,
                'response_limit' => 10,
                'listing_limit' => 50,
                'features' => [
                    'daily_request_limit' => 10,
                    'daily_response_limit' => 10,
                    'listing_limit' => 50,
                    'price_monthly' => 2000.00,
                    'price_annual' => 20000.00,
                    'priority_placement' => true,
                    'description' => '10 community requests/day, 10 quote responses/day, up to 50 active listings, priority placement',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise Salvage & Dealer',
                'price' => 5000.00,
                'price_annual' => 50000.00,
                'billing_interval' => 'monthly',
                'request_limit' => 100,
                'response_limit' => 100,
                'listing_limit' => 500,
                'features' => [
                    'daily_request_limit' => 100,
                    'daily_response_limit' => 100,
                    'listing_limit' => 500,
                    'price_monthly' => 5000.00,
                    'price_annual' => 50000.00,
                    'priority_placement' => true,
                    'dedicated_support' => true,
                    'description' => '100 community requests/day, 100 quote responses/day, up to 500 active listings, dedicated arbitration',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );

            // Seed price record for Nigeria (NGN)
            SubscriptionPlanPrice::updateOrCreate(
                [
                    'subscription_plan_id' => $plan->id,
                    'country_code' => 'NG',
                    'currency' => 'NGN',
                ],
                [
                    'price_monthly' => $planData['price'],
                    'price_annual' => $planData['price_annual'],
                    'is_active' => true,
                ]
            );
        }
    }
}
