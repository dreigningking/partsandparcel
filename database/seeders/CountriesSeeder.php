<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use App\Services\GeographyService;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nigeria
        $nigeria = Country::updateOrCreate(
            ['code' => 'NG'],
            [
                'name' => 'Nigeria',
                'phone_code' => '+234',
                'currency' => 'NGN',
                'currency_symbol' => '₦',
                'timezone' => 'Africa/Lagos',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        /*
        $nigerianStates = [
            ['name' => 'Lagos', 'code' => 'LA'],
            ['name' => 'Abuja (FCT)', 'code' => 'FC'],
            ['name' => 'Rivers', 'code' => 'RI'],
            ['name' => 'Oyo', 'code' => 'OY'],
            ['name' => 'Kano', 'code' => 'KN'],
            ['name' => 'Kaduna', 'code' => 'KD'],
            ['name' => 'Edo', 'code' => 'ED'],
            ['name' => 'Ogun', 'code' => 'OG'],
            ['name' => 'Delta', 'code' => 'DE'],
            ['name' => 'Enugu', 'code' => 'EN'],
            ['name' => 'Anambra', 'code' => 'AN'],
            ['name' => 'Plateau', 'code' => 'PL'],
        ];

        foreach ($nigerianStates as $state) {
            State::updateOrCreate(
                ['country_id' => $nigeria->id, 'name' => $state['name']],
                ['code' => $state['code'], 'is_active' => true]
            );
        }
        */

        try {
            app(GeographyService::class)->fetchAndSave($nigeria);
        } catch (\Throwable $e) {
            // Swallow errors in seeder but log if available
            if (function_exists('logger')) {
                logger()->warning('GeographySeeder: failed to fetch states/cities for Nigeria: '.$e->getMessage());
            }
        }
    }
}
