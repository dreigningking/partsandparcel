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
                'response_limit' => 5,
                'features' => [
                    '5 quote responses to buyer requests per month',
                    'Direct chat with buyers',
                    'Standard marketplace search placement',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro Technician & Vendor',
                'price' => 5000.00,
                'billing_interval' => 'monthly',
                'response_limit' => 50,
                'features' => [
                    '50 quote responses to buyer requests per month',
                    'Verified technician/seller badge',
                    'Priority quote visibility to buyers',
                    'Service job bookings & escrow protection',
                    'Direct payouts to bank account',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise Salvage & Dealer',
                'price' => 20000.00,
                'billing_interval' => 'monthly',
                'response_limit' => 1000,
                'features' => [
                    'Unlimited quote responses to buyer requests',
                    'Gold verified merchant badge',
                    'Bulk inventory upload & automated matching',
                    'Dedicated dispute resolution line',
                    'Lowest platform escrow fee tier',
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
