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
                'payment_gateway' => json_encode(['paystack', 'flutterwave', 'opay']),
                'views' => 0.0050,
                'clicks' => 20.00,
            ]
        );

        $nigerianStates = [
            ['name' => 'Abia', 'code' => 'AB', 'latitude' => 5.4527, 'longitude' => 7.5248],
            ['name' => 'Abuja (FCT)', 'code' => 'FC', 'latitude' => 9.0765, 'longitude' => 7.3986],
            ['name' => 'Adamawa', 'code' => 'AD', 'latitude' => 9.3265, 'longitude' => 12.4416],
            ['name' => 'Akwa Ibom', 'code' => 'AK', 'latitude' => 5.0377, 'longitude' => 7.9128],
            ['name' => 'Anambra', 'code' => 'AN', 'latitude' => 6.2209, 'longitude' => 7.0723],
            ['name' => 'Bauchi', 'code' => 'BA', 'latitude' => 10.3158, 'longitude' => 9.8442],
            ['name' => 'Bayelsa', 'code' => 'BY', 'latitude' => 4.7719, 'longitude' => 6.0699],
            ['name' => 'Benue', 'code' => 'BE', 'latitude' => 7.3369, 'longitude' => 8.7404],
            ['name' => 'Borno', 'code' => 'BO', 'latitude' => 11.8333, 'longitude' => 13.1500],
            ['name' => 'Cross River', 'code' => 'CR', 'latitude' => 5.8702, 'longitude' => 8.5988],
            ['name' => 'Delta', 'code' => 'DE', 'latitude' => 5.7040, 'longitude' => 5.9339],
            ['name' => 'Ebonyi', 'code' => 'EB', 'latitude' => 6.2649, 'longitude' => 8.0137],
            ['name' => 'Edo', 'code' => 'ED', 'latitude' => 6.5438, 'longitude' => 5.8987],
            ['name' => 'Ekiti', 'code' => 'EK', 'latitude' => 7.7190, 'longitude' => 5.3110],
            ['name' => 'Enugu', 'code' => 'EN', 'latitude' => 6.4584, 'longitude' => 7.5464],
            ['name' => 'Gombe', 'code' => 'GO', 'latitude' => 10.2791, 'longitude' => 11.1731],
            ['name' => 'Imo', 'code' => 'IM', 'latitude' => 5.4836, 'longitude' => 7.0332],
            ['name' => 'Jigawa', 'code' => 'JI', 'latitude' => 12.2280, 'longitude' => 9.5616],
            ['name' => 'Kaduna', 'code' => 'KD', 'latitude' => 10.5105, 'longitude' => 7.4165],
            ['name' => 'Kano', 'code' => 'KN', 'latitude' => 12.0022, 'longitude' => 8.5920],
            ['name' => 'Katsina', 'code' => 'KT', 'latitude' => 12.9855, 'longitude' => 7.6171],
            ['name' => 'Kebbi', 'code' => 'KE', 'latitude' => 12.4504, 'longitude' => 4.1999],
            ['name' => 'Kogi', 'code' => 'KO', 'latitude' => 7.7337, 'longitude' => 6.6906],
            ['name' => 'Kwara', 'code' => 'KW', 'latitude' => 8.9669, 'longitude' => 4.5996],
            ['name' => 'Lagos', 'code' => 'LA', 'latitude' => 6.5244, 'longitude' => 3.3792],
            ['name' => 'Nasarawa', 'code' => 'NA', 'latitude' => 8.5378, 'longitude' => 8.3244],
            ['name' => 'Niger', 'code' => 'NI', 'latitude' => 9.9309, 'longitude' => 5.5983],
            ['name' => 'Ogun', 'code' => 'OG', 'latitude' => 7.1604, 'longitude' => 3.3483],
            ['name' => 'Ondo', 'code' => 'ON', 'latitude' => 7.2508, 'longitude' => 5.2103],
            ['name' => 'Osun', 'code' => 'OS', 'latitude' => 7.5629, 'longitude' => 4.5200],
            ['name' => 'Oyo', 'code' => 'OY', 'latitude' => 7.3775, 'longitude' => 3.9470],
            ['name' => 'Plateau', 'code' => 'PL', 'latitude' => 9.2182, 'longitude' => 9.5179],
            ['name' => 'Rivers', 'code' => 'RI', 'latitude' => 4.8156, 'longitude' => 7.0498],
            ['name' => 'Sokoto', 'code' => 'SO', 'latitude' => 13.0609, 'longitude' => 5.2343],
            ['name' => 'Taraba', 'code' => 'TA', 'latitude' => 7.8704, 'longitude' => 9.7800],
            ['name' => 'Yobe', 'code' => 'YO', 'latitude' => 12.0000, 'longitude' => 11.5000],
            ['name' => 'Zamfara', 'code' => 'ZA', 'latitude' => 12.1222, 'longitude' => 6.2236],
        ];

        foreach ($nigerianStates as $state) {
            State::updateOrCreate(
                ['country_id' => $nigeria->id, 'name' => $state['name']],
                [
                    'code' => $state['code'],
                    'latitude' => $state['latitude'],
                    'longitude' => $state['longitude'],
                    'is_active' => true,
                ]
            );
        }

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
