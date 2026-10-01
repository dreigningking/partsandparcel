<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class DemoPromoCodesSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_amount' => 0.00,
                'max_discount' => 5000.00,
                'expires_at' => now()->addYear(),
                'usage_limit' => 1000,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'PARTS20',
                'type' => 'percentage',
                'value' => 20.00,
                'min_order_amount' => 0.00,
                'max_discount' => 10000.00,
                'expires_at' => now()->addMonths(6),
                'usage_limit' => 500,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SAVE5000',
                'type' => 'fixed',
                'value' => 5000.00,
                'min_order_amount' => 20000.00,
                'max_discount' => 5000.00,
                'expires_at' => now()->addYear(),
                'usage_limit' => 200,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'PROMO50',
                'type' => 'percentage',
                'value' => 50.00,
                'min_order_amount' => 0.00,
                'max_discount' => 15000.00,
                'expires_at' => now()->addMonths(3),
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($promos as $promo) {
            PromoCode::updateOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
