<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategoryMarketplaceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'emeka@partsandparcel.com')->first() ?? User::first();
        $buyer = User::where('email', 'chioma@partsandparcel.com')->first() ?? User::where('id', '!=', $seller->id)->first();
        $location = Location::where('user_id', $seller->id)->first() ?? Location::first();

        // 1. Fetch Models
        $hpModel = DeviceModel::where('name', 'like', '%EliteBook 840 G5%')->first();
        $dellModel = DeviceModel::where('name', 'like', '%Latitude 5420%')->first();
        $macbookModel = DeviceModel::where('name', 'like', '%MacBook Pro%')->first();
        $iphoneModel = DeviceModel::where('name', 'like', '%iPhone 13%')->first();
        $samsungModel = DeviceModel::where('name', 'like', '%Galaxy S22%')->first();
        $camryModel = DeviceModel::where('name', 'like', '%Camry%')->first();

        // Fallbacks if not found
        $laptopCat = Category::where('slug', 'laptops')->first() ?? Category::first();
        $phonesCat = Category::where('slug', 'phones')->first() ?? Category::first();
        $vehiclesCat = Category::where('slug', 'cars')->first() ?? Category::first();

        // A. WHOLE DEVICES (item_type: 'whole')
        $wholeDevices = [
            [
                'name' => 'HP EliteBook 840 G5',
                'model_id' => $hpModel?->id,
                'condition_status' => 'used_excellent',
                'description' => 'Intel Core i5 · 8GB RAM · 256GB SSD · 14" FHD IPS · Clean foreign used with original charger.',
                'price' => 280000,
                'quantity' => 3,
                'warranty_days' => 30,
            ],
            [
                'name' => 'Dell Latitude 5420',
                'model_id' => $dellModel?->id,
                'condition_status' => 'used_clean',
                'description' => 'Intel Core i5 11th Gen · 16GB RAM · 512GB NVMe SSD · Backlit keyboard, excellent battery backup.',
                'price' => 310000,
                'quantity' => 2,
                'warranty_days' => 14,
            ],
            [
                'name' => 'MacBook Pro 14" (M1 Pro)',
                'model_id' => $macbookModel?->id,
                'condition_status' => 'tokunbo',
                'description' => 'Apple M1 Pro 8-Core · 16GB Unified RAM · 512GB SSD · Liquid Retina XDR · Battery health 94%.',
                'price' => 850000,
                'quantity' => 1,
                'warranty_days' => 30,
            ],
            [
                'name' => 'Samsung Galaxy S22 5G (128GB)',
                'model_id' => $samsungModel?->id,
                'condition_status' => 'brand_new',
                'description' => 'Brand new sealed unit · 8GB RAM / 128GB ROM · Phantom Black · 1-year factory warranty.',
                'price' => 420000,
                'quantity' => 5,
                'warranty_days' => 365,
            ],
            [
                'name' => 'Toyota Camry 2.5L Complete Tokunbo Engine',
                'model_id' => $camryModel?->id,
                'condition_status' => 'tested_used',
                'description' => 'Clean Belgium-used 2.5L 2AR-FE complete engine with wiring harness, alternator and intake manifold.',
                'price' => 450000,
                'quantity' => 2,
                'warranty_days' => 14,
            ],
        ];

        foreach ($wholeDevices as $dev) {
            $item = Item::updateOrCreate(
                ['user_id' => $seller->id, 'name' => $dev['name'], 'item_type' => 'whole'],
                [
                    'model_id' => $dev['model_id'] ?? $dellModel?->id ?? 1,
                    'condition_status' => $dev['condition_status'],
                    'description' => $dev['description'],
                    'status' => 'available',
                    'acquired_at' => now()->subDays(rand(1, 20)),
                ]
            );

            Listing::updateOrCreate(
                ['user_id' => $seller->id, 'item_id' => $item->id],
                [
                    'location_id' => $location?->id,
                    'quantity' => $dev['quantity'],
                    'price' => $dev['price'],
                    'status' => 'active',
                    'warranty_period_days' => $dev['warranty_days'],
                    'warranty_terms' => "{$dev['warranty_days']} days warranty included.",
                    'description' => $dev['description'],
                ]
            );
        }

        // B. SPARE PARTS (item_type: 'part')
        $parts = [
            [
                'name' => 'HP EliteBook 840 G5 Battery (TT03XL)',
                'model_id' => $hpModel?->id,
                'condition_status' => 'tested_working',
                'description' => 'Part #TT03XL · 11.55V 50Wh · Fits EliteBook 840 G5, 840 G6, 745 G5 · High health cycle.',
                'price' => 25000,
                'quantity' => 7,
            ],
            [
                'name' => 'HP 14" FHD IPS Display LCD Screen',
                'model_id' => $hpModel?->id,
                'condition_status' => 'tested_grade_a',
                'description' => '30-Pin EDP Connector · 1920x1080 Full HD Matte Anti-Glare · 0 dead pixels · Tested Grade A.',
                'price' => 45000,
                'quantity' => 12,
            ],
            [
                'name' => 'Dell Latitude 5420 Original Motherboard',
                'model_id' => $dellModel?->id,
                'condition_status' => 'tested_working',
                'description' => 'Intel Core i5 11th Gen integrated · Onboard Intel Iris Xe Graphics · 100% tested and clean.',
                'price' => 110000,
                'quantity' => 2,
            ],
            [
                'name' => 'iPhone 13 OEM Super Retina XDR OLED Display',
                'model_id' => $iphoneModel?->id,
                'condition_status' => 'tested_grade_a',
                'description' => 'Original OLED pulled from clean donor unit · True Tone transferable · Flawless touch & brightness.',
                'price' => 85000,
                'quantity' => 4,
            ],
            [
                'name' => 'Toyota Camry 2018-2022 Tokunbo Alternator',
                'model_id' => $camryModel?->id,
                'condition_status' => 'tested_working',
                'description' => 'OEM Denso 12V 100A Alternator · Tested charging voltage 14.2V · Direct fit for 2.5L engine.',
                'price' => 38000,
                'quantity' => 3,
            ],
        ];

        foreach ($parts as $p) {
            $item = Item::updateOrCreate(
                ['user_id' => $seller->id, 'name' => $p['name'], 'item_type' => 'part'],
                [
                    'model_id' => $p['model_id'] ?? $dellModel?->id ?? 1,
                    'condition_status' => $p['condition_status'],
                    'description' => $p['description'],
                    'status' => 'available',
                    'acquired_at' => now()->subDays(rand(1, 15)),
                ]
            );

            Listing::updateOrCreate(
                ['user_id' => $seller->id, 'item_id' => $item->id],
                [
                    'location_id' => $location?->id,
                    'quantity' => $p['quantity'],
                    'price' => $p['price'],
                    'status' => 'active',
                    'warranty_period_days' => 14,
                    'warranty_terms' => '14 days testing guarantee.',
                    'description' => $p['description'],
                ]
            );
        }

        // C. SCRAP / SALVAGE UNITS (item_type: 'scrap') WITH COMPONENTS
        $scraps = [
            [
                'name' => 'Dell Latitude 5420 (Screen Fault / Salvage Unit)',
                'model_id' => $dellModel?->id,
                'condition_status' => 'scrap',
                'condition_notes' => 'Screen cracked during transit. Motherboard, 16GB RAM, battery and casing are in 100% working condition.',
                'description' => 'Ideal for parts harvesting: working Core i5 motherboard, pristine bottom/top case, functional keyboard.',
                'price' => 150000,
                'components' => [
                    ['name' => 'Motherboard (Core i5 11th Gen)', 'status' => 'working', 'condition_status' => 'tested_working'],
                    ['name' => 'RAM (16GB DDR4)', 'status' => 'working', 'condition_status' => 'tested_working'],
                    ['name' => 'FHD Screen Assembly', 'status' => 'damaged', 'condition_status' => 'damaged'],
                ],
            ],
            [
                'name' => 'Water Damaged MacBook Pro 14" (Donor Salvage Unit)',
                'model_id' => $macbookModel?->id,
                'condition_status' => 'scrap',
                'condition_notes' => 'Spilled tea on keyboard. Liquid damaged logic board. Retina display is flawless with zero scratches.',
                'description' => 'For component harvest: undamaged Mini-LED display, clean space gray aluminum chassis, trackpad intact.',
                'price' => 180000,
                'components' => [
                    ['name' => 'Mini-LED Liquid Retina Display', 'status' => 'working', 'condition_status' => 'tested_grade_a'],
                    ['name' => 'Aluminum Unibody Casing', 'status' => 'working', 'condition_status' => 'clean'],
                    ['name' => 'M1 Pro Logic Board', 'status' => 'damaged', 'condition_status' => 'water_damaged'],
                ],
            ],
            [
                'name' => 'iPhone 13 Broken Screen & Back (Salvage Unit)',
                'model_id' => $iphoneModel?->id,
                'condition_status' => 'scrap',
                'condition_notes' => 'Heavily cracked front glass and rear panel. Board boots, Face ID camera intact, battery at 91%.',
                'description' => 'Clean iCloud-free donor phone for harvesting cameras, motherboard, speaker, Taptic Engine.',
                'price' => 95000,
                'components' => [
                    ['name' => 'A15 Bionic Motherboard & FaceID', 'status' => 'working', 'condition_status' => 'tested_working'],
                    ['name' => 'Original Battery (91% Health)', 'status' => 'working', 'condition_status' => 'tested_working'],
                    ['name' => 'OLED Display & Front Glass', 'status' => 'damaged', 'condition_status' => 'cracked'],
                ],
            ],
        ];

        foreach ($scraps as $sc) {
            $parentItem = Item::updateOrCreate(
                ['user_id' => $seller->id, 'name' => $sc['name'], 'item_type' => 'scrap'],
                [
                    'model_id' => $sc['model_id'] ?? $dellModel?->id ?? 1,
                    'condition_status' => 'scrap',
                    'condition_notes' => $sc['condition_notes'],
                    'description' => $sc['description'],
                    'status' => 'available',
                    'acquired_at' => now()->subDays(rand(1, 10)),
                ]
            );

            // Create child components
            foreach ($sc['components'] as $comp) {
                Item::updateOrCreate(
                    ['parent_id' => $parentItem->id, 'name' => $comp['name']],
                    [
                        'user_id' => $seller->id,
                        'model_id' => $parentItem->model_id,
                        'item_type' => 'part',
                        'condition_status' => $comp['condition_status'],
                        'status' => $comp['status'],
                    ]
                );
            }

            Listing::updateOrCreate(
                ['user_id' => $seller->id, 'item_id' => $parentItem->id],
                [
                    'location_id' => $location?->id,
                    'quantity' => 1,
                    'price' => $sc['price'],
                    'status' => 'active',
                    'warranty_period_days' => 0,
                    'warranty_terms' => 'Sold as scrap/salvage for component harvest. As-is condition.',
                    'description' => $sc['description'],
                ]
            );
        }

        // D. COMMUNITY REQUESTS / DISCUSSIONS
        $discussions = [
            [
                'title' => 'Looking for HP EliteBook 840 G5 motherboard in Lagos',
                'category_id' => $laptopCat->id,
                'brand_id' => $hpModel?->brand_id,
                'model_id' => $hpModel?->id,
                'budget' => '70,000 – 90,000',
                'body' => 'Need a clean tested board without GPU issues. Willing to pick up at Computer Village today. Instant payment guaranteed.',
            ],
            [
                'title' => 'Dell Latitude 5420 screen replacement needed',
                'category_id' => $laptopCat->id,
                'brand_id' => $dellModel?->brand_id,
                'model_id' => $dellModel?->id,
                'budget' => '45,000',
                'body' => 'Cracked my LCD display. Looking for an original FHD matte replacement screen with installation in Abuja CBD.',
            ],
            [
                'title' => 'Urgent: Need clean iPhone 13 OEM Screen + Installation in Lekki/Ikeja',
                'category_id' => $phonesCat->id,
                'brand_id' => $iphoneModel?->brand_id,
                'model_id' => $iphoneModel?->id,
                'budget' => '100,000',
                'body' => 'My iPhone 13 fell and the display is cracked with green lines. I need an original OEM screen (no copy/incell please) plus someone who can professionally transfer True Tone and install it.',
            ],
            [
                'title' => 'Toyota Camry 2020 Transmission Gearbox Wanted in Ladipo',
                'category_id' => $vehiclesCat->id,
                'brand_id' => $camryModel?->brand_id,
                'model_id' => $camryModel?->id,
                'budget' => '250,000',
                'body' => 'Looking for clean Tokunbo 8-speed automatic transmission gearbox for 2020 Camry (2.5L). Must have testing warranty.',
            ],
        ];

        foreach ($discussions as $disc) {
            $d = Discussion::updateOrCreate(
                ['user_id' => $buyer->id, 'title' => $disc['title']],
                [
                    'category_id' => $disc['category_id'],
                    'brand_id' => $disc['brand_id'],
                    'model_id' => $disc['model_id'],
                    'location_id' => $location?->id,
                    'budget' => $disc['budget'],
                    'body' => $disc['body'],
                    'type' => 'item',
                    'status' => 'open',
                ]
            );

            // Add sample response
            Response::updateOrCreate(
                ['discussion_id' => $d->id, 'user_id' => $seller->id],
                [
                    'body' => "Hello, I have this exact component available in our stock at Ikeja/Ladipo. Let's connect.",
                    'status' => 'visible',
                ]
            );
        }
    }
}
