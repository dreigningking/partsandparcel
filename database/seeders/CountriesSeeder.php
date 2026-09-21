<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
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
                'is_active' => true,
            ]
        );

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

        // 2. United States
        $usa = Country::updateOrCreate(
            ['code' => 'US'],
            [
                'name' => 'United States',
                'phone_code' => '+1',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'timezone' => 'America/New_York',
                'is_active' => true,
            ]
        );

        $usStates = [
            ['name' => 'California', 'code' => 'CA'],
            ['name' => 'Texas', 'code' => 'TX'],
            ['name' => 'New York', 'code' => 'NY'],
            ['name' => 'Florida', 'code' => 'FL'],
            ['name' => 'Illinois', 'code' => 'IL'],
            ['name' => 'Georgia', 'code' => 'GA'],
            ['name' => 'Washington', 'code' => 'WA'],
            ['name' => 'Ohio', 'code' => 'OH'],
        ];

        foreach ($usStates as $state) {
            State::updateOrCreate(
                ['country_id' => $usa->id, 'name' => $state['name']],
                ['code' => $state['code'], 'is_active' => true]
            );
        }
    }
}
