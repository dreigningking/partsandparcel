<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Free',
                'price' => 0.00,
                'billing_interval' => 'monthly',
                'response_limit' => 1, // 1 response per day
                'features' => [
                    'daily_response_limit' => 1,
                    'listing_limit' => 10,
                    'response_period' => 'daily',
                    'disassembly_tool' => false,
                    'priority_placement' => false,
                    'description' => '1 quote response per day, up to 10 active listings',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro Technician & Vendor',
                'price' => 5000.00,
                'billing_interval' => 'monthly',
                'response_limit' => 20, // 20 responses per day
                'features' => [
                    'daily_response_limit' => 20,
                    'listing_limit' => 100,
                    'response_period' => 'daily',
                    'disassembly_tool' => true,
                    'priority_placement' => true,
                    'description' => '20 quote responses per day, up to 100 active listings, disassembly tool',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise Salvage & Dealer',
                'price' => 20000.00,
                'billing_interval' => 'monthly',
                'response_limit' => 100, // 100 responses per day
                'features' => [
                    'daily_response_limit' => 100,
                    'listing_limit' => 1000,
                    'response_period' => 'daily',
                    'disassembly_tool' => true,
                    'priority_placement' => true,
                    'dedicated_support' => true,
                    'description' => '100 quote responses per day, up to 1,000 active listings, priority arbitration',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
    }
}
