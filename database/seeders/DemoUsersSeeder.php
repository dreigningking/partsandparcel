<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Country;
use App\Models\Location;
use App\Models\Role;
use App\Models\State;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $nigeria = Country::where('code', 'NG')->first() ?? Country::where('is_default', true)->first();

        // Ensure roles exist
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $arbitratorRole = Role::where('slug', 'dispute_arbitrator')->first();
        $catalogRole = Role::where('slug', 'catalog_manager')->first();
        $supportRole = Role::where('slug', 'customer_support')->first();

        if (!$superAdminRole || !$arbitratorRole || !$catalogRole || !$supportRole) {
            $this->call(RolesAndPermissionsSeeder::class);
            $superAdminRole = Role::where('slug', 'super_admin')->first();
            $arbitratorRole = Role::where('slug', 'dispute_arbitrator')->first();
            $catalogRole = Role::where('slug', 'catalog_manager')->first();
            $supportRole = Role::where('slug', 'customer_support')->first();
        }

        // Ensure plans exist
        $allPlans = SubscriptionPlan::where('is_active', true)->get();
        if ($allPlans->isEmpty()) {
            $this->call(SubscriptionPlansSeeder::class);
            $allPlans = SubscriptionPlan::where('is_active', true)->get();
        }

        // Resolve States safely
        $getState = function (string $nameSearch, string $code, string $defaultName) use ($nigeria) {
            $state = State::where('name', 'like', "%{$nameSearch}%")->first();
            if (!$state && $nigeria) {
                $state = State::firstOrCreate(
                    ['country_id' => $nigeria->id, 'name' => $defaultName],
                    ['code' => $code, 'is_active' => true]
                );
            }
            return $state;
        };

        $lagosState = $getState('Lagos', 'LA', 'Lagos');
        $abujaState = $getState('Abuja', 'FC', 'Abuja (FCT)');
        $riversState = $getState('Rivers', 'RI', 'Rivers');
        $oyoState = $getState('Oyo', 'OY', 'Oyo');
        $kanoState = $getState('Kano', 'KN', 'Kano');

        // 1. Staff / Admin Users (at least one user for each existing role)
        $staffUsers = [
            [
                'email' => 'admin@partsandparcel.com',
                'name' => 'Platform Administrator',
                'role_id' => $superAdminRole?->id,
                'phone' => '+2348011112222',
                'location_label' => 'HQ Command Center',
                'address' => 'Plot 10 Commercial Avenue, Victoria Island',
                'city' => 'Lagos',
                'state_id' => $lagosState?->id,
                'bank_name' => 'First Bank of Nigeria',
                'bank_code' => '011',
                'account_number' => '0011223344',
                'account_name' => 'Parts & Parcel Escrow Admin',
                'recipient_code' => 'RCP_admin_super',
            ],
            [
                'email' => 'arbitrator@partsandparcel.com',
                'name' => 'Kareem Bello (Arbitrator)',
                'role_id' => $arbitratorRole?->id,
                'phone' => '+2348011113333',
                'location_label' => 'Arbitration Tribunal Office',
                'address' => 'Floor 4, Central Business District',
                'city' => 'Abuja',
                'state_id' => $abujaState?->id,
                'bank_name' => 'Zenith Bank',
                'bank_code' => '057',
                'account_number' => '0022334455',
                'account_name' => 'Kareem Bello Escrow Arbitration',
                'recipient_code' => 'RCP_staff_arbitrator',
            ],
            [
                'email' => 'catalog@partsandparcel.com',
                'name' => 'Ngozi Okafor (Catalog Lead)',
                'role_id' => $catalogRole?->id,
                'phone' => '+2348011114444',
                'location_label' => 'Catalog Verification Lab',
                'address' => '15 Otigba Street, Computer Village, Ikeja',
                'city' => 'Lagos',
                'state_id' => $lagosState?->id,
                'bank_name' => 'Guaranty Trust Bank (GTB)',
                'bank_code' => '058',
                'account_number' => '0033445566',
                'account_name' => 'Ngozi Okafor Catalog Management',
                'recipient_code' => 'RCP_staff_catalog',
            ],
            [
                'email' => 'support@partsandparcel.com',
                'name' => 'Amina Yusuf (Support Supervisor)',
                'role_id' => $supportRole?->id,
                'phone' => '+2348011115555',
                'location_label' => 'Customer Care Hub',
                'address' => 'Bompai Business Tower, Suite 2B',
                'city' => 'Kano',
                'state_id' => $kanoState?->id,
                'bank_name' => 'Access Bank',
                'bank_code' => '044',
                'account_number' => '0044556677',
                'account_name' => 'Amina Yusuf Customer Service',
                'recipient_code' => 'RCP_staff_support',
            ],
        ];

        $enterprisePlan = $allPlans->where('slug', 'enterprise-salvage-dealer')->first() ?? $allPlans->first();

        foreach ($staffUsers as $staff) {
            $user = User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $staff['role_id'],
                    'phone' => $staff['phone'],
                    'is_verified' => true,
                    'gender' => 'other',
                    'country_id' => $nigeria?->id,
                    'email_verified_at' => now(),
                ]
            );

            Location::updateOrCreate(
                ['user_id' => $user->id, 'label' => $staff['location_label']],
                [
                    'contact_name' => $user->name,
                    'phone' => $user->phone,
                    'address_line_1' => $staff['address'],
                    'city' => $staff['city'],
                    'state_id' => $staff['state_id'],
                    'country_id' => $nigeria?->id,
                    'is_default' => true,
                ]
            );

            BankAccount::updateOrCreate(
                ['user_id' => $user->id, 'account_number' => $staff['account_number']],
                [
                    'bank_name' => $staff['bank_name'],
                    'bank_code' => $staff['bank_code'],
                    'account_name' => $staff['account_name'],
                    'recipient_code' => $staff['recipient_code'],
                    'currency' => 'NGN',
                    'is_default' => true,
                    'verified_at' => now(),
                ]
            );

            if ($enterprisePlan) {
                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'subscription_plan_id' => $enterprisePlan->id,
                        'status' => 'active',
                        'starts_at' => now()->subMonth(),
                        'ends_at' => now()->addYear(),
                        'response_limit' => $enterprisePlan->response_limit,
                        'request_limit' => $enterprisePlan->request_limit,
                        'listing_limit' => $enterprisePlan->listing_limit,
                    ]
                );
            }
        }

        // Guarantee at least one user for ALL existing roles in the system
        $allRoles = Role::where('is_active', true)->get();
        foreach ($allRoles as $role) {
            $hasUser = User::where('role_id', $role->id)->exists();
            if (!$hasUser) {
                $roleSlug = Str::slug($role->slug ?: $role->name);
                $roleUser = User::updateOrCreate(
                    ['email' => "{$roleSlug}@partsandparcel.com"],
                    [
                        'name' => "{$role->name} Staff",
                        'password' => Hash::make('password'),
                        'role_id' => $role->id,
                        'phone' => '+2348011' . rand(100000, 999999),
                        'is_verified' => true,
                        'gender' => 'other',
                        'country_id' => $nigeria?->id,
                        'email_verified_at' => now(),
                    ]
                );

                Location::updateOrCreate(
                    ['user_id' => $roleUser->id, 'label' => 'Staff Operating Hub'],
                    [
                        'contact_name' => $roleUser->name,
                        'phone' => $roleUser->phone,
                        'address_line_1' => 'Plot 10 Commercial Avenue',
                        'city' => 'Lagos',
                        'state_id' => $lagosState?->id,
                        'country_id' => $nigeria?->id,
                        'is_default' => true,
                    ]
                );

                BankAccount::updateOrCreate(
                    ['user_id' => $roleUser->id, 'account_number' => '99' . str_pad((string)$role->id, 8, '0', STR_PAD_LEFT)],
                    [
                        'bank_name' => 'Zenith Bank',
                        'bank_code' => '057',
                        'account_name' => "{$roleUser->name} Operations",
                        'recipient_code' => "RCP_role_{$role->id}",
                        'currency' => 'NGN',
                        'is_default' => true,
                        'verified_at' => now(),
                    ]
                );

                if ($enterprisePlan) {
                    Subscription::updateOrCreate(
                        ['user_id' => $roleUser->id],
                        [
                            'subscription_plan_id' => $enterprisePlan->id,
                            'status' => 'active',
                            'starts_at' => now()->subMonth(),
                            'ends_at' => now()->addYear(),
                            'response_limit' => $enterprisePlan->response_limit,
                            'request_limit' => $enterprisePlan->request_limit,
                            'listing_limit' => $enterprisePlan->listing_limit,
                        ]
                    );
                }
            }
        }

        // 2. Standard Users (role_id => null): Exactly 3 in each of Lagos, Abuja, Rivers, Oyo, Kano (15 users total)
        $standardUsersByState = [
            // Lagos State (3 users)
            [
                'email' => 'emeka@partsandparcel.com',
                'name' => 'Emeka Okafor',
                'phone' => '+2348033334441',
                'business_name' => 'Apex Auto & Tech Salvage',
                'bio' => 'Certified dismantler and original OEM parts supplier in Ladipo Market.',
                'theme' => 'dark',
                'city' => 'Lagos',
                'state_id' => $lagosState?->id,
                'address' => 'Plot 14 Ladipo Auto Spares Market, Mushin',
                'bank_name' => 'Zenith Bank',
                'bank_code' => '057',
                'account_number' => '1000000001',
            ],
            [
                'email' => 'tunde@partsandparcel.com',
                'name' => 'Tunde Bakare',
                'phone' => '+2348033334442',
                'business_name' => 'FixLogic Diagnostics & Repairs',
                'bio' => 'Specialist in automobile ECU diagnostics, ECM programming and device repairs in Ikeja.',
                'theme' => 'system',
                'city' => 'Lagos',
                'state_id' => $lagosState?->id,
                'address' => '12 Computer Village Road, Ikeja',
                'bank_name' => 'Access Bank',
                'bank_code' => '044',
                'account_number' => '1000000002',
            ],
            [
                'email' => 'chioma@partsandparcel.com',
                'name' => 'Chioma Adeyemi',
                'phone' => '+2348033334443',
                'business_name' => 'SwiftParts Logistics & Retail',
                'bio' => 'Wholesale supplier of luxury vehicle body components and electronics in Lekki.',
                'theme' => 'light',
                'city' => 'Lagos',
                'state_id' => $lagosState?->id,
                'address' => 'Flat 4B Admiralty Way, Lekki Phase 1',
                'bank_name' => 'Guaranty Trust Bank (GTB)',
                'bank_code' => '058',
                'account_number' => '1000000003',
            ],

            // Abuja (FCT) (3 users)
            [
                'email' => 'usman.fct@partsandparcel.com',
                'name' => 'Usman Bello',
                'phone' => '+2348044445551',
                'business_name' => 'Capital Auto Salvage & Dismantlers',
                'bio' => 'Accident salvage vehicle buyer, engine overhaul expert at Apo Mechanic Village.',
                'theme' => 'dark',
                'city' => 'Abuja',
                'state_id' => $abujaState?->id,
                'address' => 'Shop 42 Apo Mechanic Village Extension',
                'bank_name' => 'Stanbic IBTC Bank',
                'bank_code' => '221',
                'account_number' => '1000000004',
            ],
            [
                'email' => 'zainab.fct@partsandparcel.com',
                'name' => 'Zainab Aliyu',
                'phone' => '+2348044445552',
                'business_name' => 'Z-Tech Microelectronics & Logic Hub',
                'bio' => 'Apple MacBook and high-end laptop logic board technician in Wuse 2.',
                'theme' => 'system',
                'city' => 'Abuja',
                'state_id' => $abujaState?->id,
                'address' => 'Plaza 8 Aminu Kano Crescent, Wuse 2',
                'bank_name' => 'United Bank for Africa (UBA)',
                'bank_code' => '033',
                'account_number' => '1000000005',
            ],
            [
                'email' => 'chinedu.fct@partsandparcel.com',
                'name' => 'Chinedu Eze',
                'phone' => '+2348044445553',
                'business_name' => 'Federal Spares & Generator Clinic',
                'bio' => 'Diesel generators (Perkins, Cummins, Mikano) servicing and genuine spares in Garki.',
                'theme' => 'light',
                'city' => 'Abuja',
                'state_id' => $abujaState?->id,
                'address' => 'Plot 88 Garki 2 Commercial Complex',
                'bank_name' => 'Fidelity Bank',
                'bank_code' => '070',
                'account_number' => '1000000006',
            ],

            // Rivers State (3 users)
            [
                'email' => 'blessing.ph@partsandparcel.com',
                'name' => 'Blessing Okon',
                'phone' => '+2348055556661',
                'business_name' => 'Calabar & PH Appliance Clinic',
                'bio' => 'Commercial refrigeration, industrial chiller compressors and inverter split AC repairs.',
                'theme' => 'dark',
                'city' => 'Port Harcourt',
                'state_id' => $riversState?->id,
                'address' => '88 Trans-Amadi Industrial Layout, Port Harcourt',
                'bank_name' => 'Zenith Bank',
                'bank_code' => '057',
                'account_number' => '1000000007',
            ],
            [
                'email' => 'tamuno.ph@partsandparcel.com',
                'name' => 'Tamuno Briggs',
                'phone' => '+2348055556662',
                'business_name' => 'Oilfield Equipment & Marine Spares',
                'bio' => 'Heavy diesel pumps, hydraulic valves and commercial truck drivelines in Mile 3.',
                'theme' => 'system',
                'city' => 'Port Harcourt',
                'state_id' => $riversState?->id,
                'address' => '22 Ikwerre Road, Mile 3, Diobu, Port Harcourt',
                'bank_name' => 'First City Monument Bank (FCMB)',
                'bank_code' => '214',
                'account_number' => '1000000008',
            ],
            [
                'email' => 'ngozi.ph@partsandparcel.com',
                'name' => 'Ngozi Jumbo',
                'phone' => '+2348055556663',
                'business_name' => 'Garden City Auto Transmission Works',
                'bio' => 'Automatic transmission rebuilding, valve body testing and torque converter distributor.',
                'theme' => 'light',
                'city' => 'Port Harcourt',
                'state_id' => $riversState?->id,
                'address' => '14 Stadium Road, GRA Phase 4, Port Harcourt',
                'bank_name' => 'Access Bank',
                'bank_code' => '044',
                'account_number' => '1000000009',
            ],

            // Oyo State (3 users)
            [
                'email' => 'adebayo.ib@partsandparcel.com',
                'name' => 'Adebayo Adeleke',
                'phone' => '+2348066667771',
                'business_name' => 'Dugbe Auto Electrical & Japanese Spares',
                'bio' => 'Direct tokunbo starter motors, alternators, and wiring looms distributor in Ibadan.',
                'theme' => 'dark',
                'city' => 'Ibadan',
                'state_id' => $oyoState?->id,
                'address' => '5 Lebanon Street, Dugbe Commercial District, Ibadan',
                'bank_name' => 'First Bank of Nigeria',
                'bank_code' => '011',
                'account_number' => '1000000010',
            ],
            [
                'email' => 'folake.ib@partsandparcel.com',
                'name' => 'Folake Ajayi',
                'phone' => '+2348066667772',
                'business_name' => 'Bodija Gadgets & Screen Salvage',
                'bio' => 'Original phone screens, OEM batteries, and tablet components supplier in Bodija.',
                'theme' => 'system',
                'city' => 'Ibadan',
                'state_id' => $oyoState?->id,
                'address' => 'Shop B12 Bodija Market Shopping Complex, Ibadan',
                'bank_name' => 'Guaranty Trust Bank (GTB)',
                'bank_code' => '058',
                'account_number' => '1000000011',
            ],
            [
                'email' => 'rasheed.ib@partsandparcel.com',
                'name' => 'Rasheed Alao',
                'phone' => '+2348066667773',
                'business_name' => 'Iwo Road Dismantlers Yard',
                'bio' => 'Commercial bus and taxi engine blocks, suspension, and steering racks supplier.',
                'theme' => 'light',
                'city' => 'Ibadan',
                'state_id' => $oyoState?->id,
                'address' => 'Opposite Total Filling Station, Iwo Road, Ibadan',
                'bank_name' => 'Sterling Bank',
                'bank_code' => '232',
                'account_number' => '1000000012',
            ],

            // Kano State (3 users)
            [
                'email' => 'ibrahim.kn@partsandparcel.com',
                'name' => 'Ibrahim Musa',
                'phone' => '+2348077778881',
                'business_name' => 'Arewa Diesel & Heavy Machinery Spares',
                'bio' => 'Wholesale supplier of CAT, Komatsu, and Perkins diesel components across Northern Nigeria.',
                'theme' => 'system',
                'city' => 'Kano',
                'state_id' => $kanoState?->id,
                'address' => '25 Bompai Industrial Area, Kano',
                'bank_name' => 'Stanbic IBTC Bank',
                'bank_code' => '221',
                'account_number' => '1000000013',
            ],
            [
                'email' => 'aminu.kn@partsandparcel.com',
                'name' => 'Aminu Sani',
                'phone' => '+2348077778882',
                'business_name' => 'Kofar Ruwa Heavy Truck & Axle Yard',
                'bio' => 'Haulage truck suspensions, leaf springs, Mack differential axles and heavy gearboxes.',
                'theme' => 'dark',
                'city' => 'Kano',
                'state_id' => $kanoState?->id,
                'address' => 'Line 4 Kofar Ruwa Market, Dala, Kano',
                'bank_name' => 'Jaiz Bank',
                'bank_code' => '301',
                'account_number' => '1000000014',
            ],
            [
                'email' => 'fatima.kn@partsandparcel.com',
                'name' => 'Fatima Dangote',
                'phone' => '+2348077778883',
                'business_name' => 'Northern Solar, Inverter & Battery Spares',
                'bio' => 'Lithium storage batteries, pure sine wave inverters, solar panels and charge controllers.',
                'theme' => 'light',
                'city' => 'Kano',
                'state_id' => $kanoState?->id,
                'address' => '18 France Road, Sabon Gari, Kano',
                'bank_name' => 'United Bank for Africa (UBA)',
                'bank_code' => '033',
                'account_number' => '1000000015',
            ],
        ];

        foreach ($standardUsersByState as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => null,
                    'phone' => $userData['phone'],
                    'business_name' => $userData['business_name'],
                    'bio' => $userData['bio'],
                    'is_verified' => true,
                    'gender' => 'male',
                    'country_id' => $nigeria?->id,
                    'email_verified_at' => now(),
                ]
            );

            Location::updateOrCreate(
                ['user_id' => $user->id, 'label' => 'Primary Workshop/Yard'],
                [
                    'contact_name' => $user->name,
                    'phone' => $user->phone,
                    'address_line_1' => $userData['address'],
                    'city' => $userData['city'],
                    'state_id' => $userData['state_id'],
                    'country_id' => $nigeria?->id,
                    'is_default' => true,
                ]
            );

            BankAccount::updateOrCreate(
                ['user_id' => $user->id, 'account_number' => $userData['account_number']],
                [
                    'bank_name' => $userData['bank_name'],
                    'bank_code' => $userData['bank_code'],
                    'account_name' => $userData['business_name'],
                    'recipient_code' => 'RCP_' . $user->id,
                    'currency' => 'NGN',
                    'is_default' => true,
                    'verified_at' => now(),
                ]
            );

            // Assign a random active subscription plan to each user
            $randomPlan = $allPlans->random();
            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'subscription_plan_id' => $randomPlan->id,
                    'status' => 'active',
                    'starts_at' => now()->subDays(rand(1, 15)),
                    'ends_at' => now()->addDays(rand(15, 30)),
                    'response_limit' => $randomPlan->response_limit,
                    'request_limit' => $randomPlan->request_limit,
                    'listing_limit' => $randomPlan->listing_limit,
                ]
            );
        }
    }
}
