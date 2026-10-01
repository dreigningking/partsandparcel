<?php

namespace Database\Seeders;

use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoItemsAndListingsSeeder extends Seeder
{
    public function run(): void
    {
        // Enforce domain rule: when there is parent_id on an item, the item_type is 'scrap'
        Item::whereNotNull('parent_id')->where('item_type', '!=', 'scrap')->update(['item_type' => 'scrap']);

        // Standard users only (non-admin)
        $users = User::whereNull('role_id')->get();
        if ($users->isEmpty()) {
            $this->call(DemoUsersSeeder::class);
            $users = User::whereNull('role_id')->get();
        }

        // Available models pool
        $models = DeviceModel::all();
        if ($models->isEmpty()) {
            $this->call(CategoriesAndBrandsSeeder::class);
            $models = DeviceModel::all();
        }

        $getModel = function (?string $search = null) use ($models) {
            if ($search) {
                $found = $models->first(fn($m) => Str::contains(strtolower($m->name), strtolower($search)));
                if ($found) return $found;
            }
            return $models->random();
        };

        foreach ($users as $user) {
            $location = $user->locations()->where('is_default', true)->first() ?? $user->locations()->first();
            $subscription = Subscription::where('user_id', $user->id)->where('status', 'active')->first();
            $listingLimit = $subscription ? (int) $subscription->listing_limit : 10;

            // Maximum listings this user is allowed to have must be strictly BELOW what subscription allows
            $maxListingsToCreate = max(1, min($listingLimit - 1, 6));

            $createdItems = [];

            // 1. Create WHOLE devices/machines/vehicles (parent_id => null, item_type => 'whole')
            $wholeItemsData = [
                [
                    'name' => 'Toyota Corolla 1.8L Clean Tokunbo Sedan',
                    'model' => $getModel('Corolla'),
                    'condition' => 'tokunbo',
                    'year' => '2015',
                    'notes' => 'Direct foreign-used, original paint, duty fully paid.',
                    'description' => 'Tested 4-cylinder engine, smooth automatic transmission, chilled factory AC, clean fabric interior.',
                    'price' => 5800000.00,
                ],
                [
                    'name' => 'HP EliteBook 840 G7 UltraBook',
                    'model' => $getModel('EliteBook 840 G7'),
                    'condition' => 'used_clean',
                    'year' => '2021',
                    'notes' => '10th Gen Core i5, 16GB RAM, 512GB NVMe SSD.',
                    'description' => 'Lightweight magnesium body, FHD anti-glare display, backlit keyboard, 6 hours battery life.',
                    'price' => 385000.00,
                ],
                [
                    'name' => 'Samsung 320L Double Door Inverter Refrigerator',
                    'model' => $getModel('Double Door 320L'),
                    'condition' => 'tested_working',
                    'year' => '2022',
                    'notes' => 'Energy saving digital inverter compressor, frost free.',
                    'description' => 'Pristine condition, fast chilling freezer compartment, original factory gas intact.',
                    'price' => 290000.00,
                ],
            ];

            foreach ($wholeItemsData as $data) {
                $item = Item::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'parent_id' => null,
                        'name' => $data['name'],
                    ],
                    [
                        'item_type' => 'whole',
                        'location_id' => $location?->id,
                        'model_id' => $data['model']->id,
                        'year' => $data['year'],
                        'condition_status' => $data['condition'],
                        'condition_notes' => $data['notes'],
                        'description' => $data['description'],
                    ]
                );
                $createdItems[] = ['item' => $item, 'price' => $data['price'], 'warranty_days' => 14];
            }

            // 2. Create standalone PART items (parent_id => null, item_type => 'part')
            // If the user wants to sell a gearbox as a single item, he can list it as 'part'
            $partItemsData = [
                [
                    'name' => 'Toyota Camry 6-Speed Automatic Gearbox Transmission (Single Part)',
                    'model' => $getModel('U660E'),
                    'condition' => 'tested_used',
                    'year' => '2015',
                    'notes' => 'Single standalone part. Fluid clean, torque converter included, tested before removal.',
                    'description' => 'Original standalone 6-speed automatic gearbox ready for direct installation.',
                    'price' => 370000.00,
                ],
                [
                    'name' => 'Toyota 2GR-FE 3.5L V6 Tokunbo Complete Engine',
                    'model' => $getModel('2GR-FE'),
                    'condition' => 'tested_used',
                    'year' => '2016',
                    'notes' => 'Compression tested, complete with intake manifold and wiring harness.',
                    'description' => 'Clean Belgium tokunbo engine compatible with Camry, Avalon, Highlander, and Sienna.',
                    'price' => 650000.00,
                ],
                [
                    'name' => 'Original MacBook Pro 14 M1 Pro Retina Screen Assembly',
                    'model' => $getModel('MacBook Pro 14'),
                    'condition' => 'tested_working',
                    'year' => '2021',
                    'notes' => 'Space Grey casing, zero dead pixels, True Tone ready.',
                    'description' => 'Complete upper lid assembly with webcam and display cables. Tested 100% working.',
                    'price' => 210000.00,
                ],
                [
                    'name' => 'Denso 12V 130A High Output Alternator',
                    'model' => $getModel('Denso ECM'),
                    'condition' => 'tested_working',
                    'year' => '2018',
                    'notes' => 'Tested on test-bench, smooth bearings and strong voltage output.',
                    'description' => 'Original Denso alternator suitable for Toyota and Lexus V6 models.',
                    'price' => 45000.00,
                ],
            ];

            foreach ($partItemsData as $data) {
                $item = Item::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'parent_id' => null,
                        'name' => $data['name'],
                    ],
                    [
                        'item_type' => 'part',
                        'location_id' => $location?->id,
                        'model_id' => $data['model']->id,
                        'year' => $data['year'],
                        'condition_status' => $data['condition'],
                        'condition_notes' => $data['notes'],
                        'description' => $data['description'],
                    ]
                );
                $createdItems[] = ['item' => $item, 'price' => $data['price'], 'warranty_days' => 7];
            }

            // 3. Create SCRAP items (parent unit + child harvested components)
            // Parent scrap unit (accident salvage vehicle or donor device being parted out)
            $scrapParent = Item::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'parent_id' => null,
                    'name' => 'Accident Salvage Toyota Camry (Frontal Impact Dismantle)',
                ],
                [
                    'item_type' => 'scrap',
                    'location_id' => $location?->id,
                    'model_id' => $getModel('Camry 2.5L')->id,
                    'year' => '2014',
                    'condition_status' => 'scrap',
                    'condition_notes' => 'Frontal accident, cabin intact, rear doors and mechanical parts fully recoverable.',
                    'description' => 'Dismantling for usable original body panels, ECU, interior upholstery, and transmission.',
                ]
            );

            // Child components harvested from the scrap vehicle
            // CRITICAL DOMAIN RULE: When there is a parent_id on an item, the item_type is 'scrap'
            $childComponents = [
                [
                    'name' => 'Camry U660E Harvested Automatic Gearbox',
                    'model' => $getModel('U660E'),
                    'condition' => 'tested_used',
                    'year' => '2014',
                    'notes' => 'Harvested from salvage Camry unit. Fluid drained clean, tested torque converter included.',
                    'description' => 'Smooth shifting 6-speed automatic gearbox dismantled from scrap vehicle.',
                    'price' => 350000.00,
                ],
                [
                    'name' => 'Denso OEM Engine Control Unit (Brain Box)',
                    'model' => $getModel('Denso ECM'),
                    'condition' => 'tested_working',
                    'year' => '2014',
                    'notes' => 'Harvested from scrap unit. Pins straight, zero water damage, immobilizer intact.',
                    'description' => 'Original factory ECU brain box pulled from donor Camry.',
                    'price' => 85000.00,
                ],
                [
                    'name' => 'Left Rear Passenger Door Complete Shell',
                    'model' => $getModel('Camry 2.5L'),
                    'condition' => 'used_clean',
                    'year' => '2014',
                    'notes' => 'Original factory silver paint, power window regulator intact.',
                    'description' => 'Rust-free door shell harvested from scrap vehicle body.',
                    'price' => 60000.00,
                ],
            ];

            foreach ($childComponents as $childData) {
                $childItem = Item::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'parent_id' => $scrapParent->id,
                        'name' => $childData['name'],
                    ],
                    [
                        'item_type' => 'scrap', // RULE: when there is parent_id on an item, item_type is 'scrap'
                        'location_id' => $location?->id,
                        'model_id' => $childData['model']->id,
                        'year' => $childData['year'],
                        'condition_status' => $childData['condition'],
                        'condition_notes' => $childData['notes'],
                        'description' => $childData['description'],
                    ]
                );
                $createdItems[] = ['item' => $childItem, 'price' => $childData['price'], 'warranty_days' => 7];
            }

            // A second tech scrap item with harvested parts
            $techScrap = Item::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'parent_id' => null,
                    'name' => 'Dell Latitude 5420 Donor Unit (Broken LCD Screen)',
                ],
                [
                    'item_type' => 'scrap',
                    'location_id' => $location?->id,
                    'model_id' => $getModel('Latitude 5420')->id,
                    'year' => '2021',
                    'condition_status' => 'scrap',
                    'condition_notes' => 'Cracked LCD panel, motherboard posts to external monitor without errors.',
                    'description' => 'Donor laptop being parted out for internal components.',
                ]
            );

            // Child components harvested from the scrap laptop
            // CRITICAL DOMAIN RULE: When there is a parent_id on an item, the item_type is 'scrap'
            $laptopChildParts = [
                [
                    'name' => 'Dell Latitude 5420 Motherboard (Core i5 11th Gen)',
                    'model' => $getModel('Latitude 5420'),
                    'condition' => 'tested_working',
                    'year' => '2021',
                    'notes' => 'Harvested from donor unit. Tested on HDMI monitor, BIOS unlocked, never reworked.',
                    'description' => 'Working motherboard pulled from donor laptop with cracked display.',
                    'price' => 110000.00,
                ],
                [
                    'name' => 'Dell 63Wh 4-Cell OEM Internal Battery',
                    'model' => $getModel('Latitude 5420'),
                    'condition' => 'used_clean',
                    'year' => '2021',
                    'notes' => 'Harvested from donor laptop. Health tested at 91%, holds 5+ hours charge.',
                    'description' => 'Genuine OEM Dell battery model RJ40G removed from scrap unit.',
                    'price' => 28000.00,
                ],
            ];

            foreach ($laptopChildParts as $lcp) {
                $childItem = Item::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'parent_id' => $techScrap->id,
                        'name' => $lcp['name'],
                    ],
                    [
                        'item_type' => 'scrap', // RULE: when there is parent_id on an item, item_type is 'scrap'
                        'location_id' => $location?->id,
                        'model_id' => $lcp['model']->id,
                        'year' => $lcp['year'],
                        'condition_status' => $lcp['condition'],
                        'condition_notes' => $lcp['notes'],
                        'description' => $lcp['description'],
                    ]
                );
                $createdItems[] = ['item' => $childItem, 'price' => $lcp['price'], 'warranty_days' => 14];
            }

            // 4. Create LISTINGS for SOME items (not all), strictly BELOW subscription limit
            // Shuffle and pick a slice so remaining items stay as unlisted inventory
            shuffle($createdItems);
            $itemsToList = array_slice($createdItems, 0, $maxListingsToCreate);

            foreach ($itemsToList as $entry) {
                $itemObj = $entry['item'];
                $price = $entry['price'];
                $warrantyDays = $entry['warranty_days'];

                Listing::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'item_id' => $itemObj->id,
                    ],
                    [
                        'slug' => Str::slug($itemObj->name . '-' . $user->id . '-' . Str::random(5)),
                        'quantity' => rand(1, 3),
                        'reserved_quantity' => 0,
                        'sold_quantity' => 0,
                        'price' => $price,
                        'is_negotiable' => (bool) rand(0, 1),
                        'warranty_period_days' => $warrantyDays,
                        'is_warranty_negotiable' => true,
                        'warranty_terms' => "Testing warranty of {$warrantyDays} days covers electrical and operational defects.",
                        'allow_shipping' => true,
                        'is_published' => true,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
