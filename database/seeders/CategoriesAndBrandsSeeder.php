<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesAndBrandsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Categories & Children
        $categoriesStructure = [
            'Electronics' => [
                'slug' => 'electronics',
                'children' => [
                    ['name' => 'Smartphones & Tablets', 'slug' => 'phones'],
                    ['name' => 'Laptops & Computers', 'slug' => 'laptops'],
                    ['name' => 'Screens & Displays', 'slug' => 'screens-displays'],
                    ['name' => 'Batteries & Charging Adapters', 'slug' => 'batteries-chargers'],
                    ['name' => 'Motherboards & Logic Boards', 'slug' => 'motherboards-logic-boards'],
                    ['name' => 'RAM, Storage & SSDs', 'slug' => 'storage-memory'],
                    ['name' => 'Keyboards & Input Devices', 'slug' => 'keyboards-input'],
                    ['name' => 'Audio, Headphones & Speakers', 'slug' => 'audio-sound'],
                    ['name' => 'Cameras & Image Sensors', 'slug' => 'cameras-sensors'],
                    ['name' => 'Networking & Telecom Hardware', 'slug' => 'networking-telecom'],
                ],
            ],
            'Appliances' => [
                'slug' => 'appliances',
                'children' => [
                    ['name' => 'Washing Machines & Dryers', 'slug' => 'washing-machines'],
                    ['name' => 'Refrigerators & Deep Freezers', 'slug' => 'refrigerators'],
                    ['name' => 'Air Conditioners & Inverters', 'slug' => 'air-conditioners'],
                    ['name' => 'Microwaves & Kitchen Appliances', 'slug' => 'kitchen-appliances'],
                    ['name' => 'Compressors & Gas Tanks', 'slug' => 'appliance-compressors'],
                    ['name' => 'Washing Machine Motors & Pumps', 'slug' => 'appliance-motors-pumps'],
                    ['name' => 'Appliance Control Boards & Capacitors', 'slug' => 'appliance-control-boards'],
                    ['name' => 'Water Dispensers & Heaters', 'slug' => 'water-heaters-dispensers'],
                ],
            ],
            'Vehicles' => [
                'slug' => 'vehicles',
                'children' => [
                    ['name' => 'Passenger Cars & Sedans', 'slug' => 'cars'],
                    ['name' => 'Haulage Trucks & Trailers', 'slug' => 'trucks'],
                    ['name' => 'Buses & Commercial Vans', 'slug' => 'buses'],
                    ['name' => 'Motorcycles & Tricycles', 'slug' => 'motorcycles'],
                    ['name' => 'Complete Tokunbo Engines', 'slug' => 'complete-engines'],
                    ['name' => 'Gearboxes & Transmissions', 'slug' => 'gearboxes-transmissions'],
                    ['name' => 'ECUs, Sensors & Brain Boxes', 'slug' => 'ecus-electrical'],
                    ['name' => 'Suspension, Brakes & Steering', 'slug' => 'suspension-brakes'],
                    ['name' => 'Auto Body Panels & Bumpers', 'slug' => 'vehicle-body-parts'],
                    ['name' => 'Alternators & Starter Motors', 'slug' => 'alternators-starters'],
                ],
            ],
            'Heavy Equipment' => [
                'slug' => 'heavy-equipment',
                'children' => [
                    ['name' => 'Excavators & Diggers', 'slug' => 'excavators'],
                    ['name' => 'Bulldozers & Crawlers', 'slug' => 'bulldozers'],
                    ['name' => 'Wheel Loaders & Backhoes', 'slug' => 'wheel-loaders'],
                    ['name' => 'Heavy Cranes & Lifting Equipment', 'slug' => 'cranes'],
                    ['name' => 'Forklifts & Material Handling', 'slug' => 'forklifts-material-handling'],
                    ['name' => 'Road Rollers & Compactors', 'slug' => 'road-rollers-compactors'],
                    ['name' => 'Undercarriage, Tracks & Rollers', 'slug' => 'undercarriage-tracks'],
                    ['name' => 'Heavy Hydraulic Pumps & Rams', 'slug' => 'hydraulic-pumps-cylinders'],
                ],
            ],
            'Construction' => [
                'slug' => 'construction',
                'children' => [
                    ['name' => 'Concrete Mixers & Batch Plants', 'slug' => 'concrete-mixers-pumps'],
                    ['name' => 'Scaffolding & Formwork Systems', 'slug' => 'scaffolding-formwork'],
                    ['name' => 'Tamping Rammers & Plate Compactors', 'slug' => 'tamping-rammers-compactors'],
                    ['name' => 'Demolition Breakers & Rock Drills', 'slug' => 'demolition-breakers-drills'],
                    ['name' => 'Surveying & Laser Levels', 'slug' => 'surveying-instruments'],
                    ['name' => 'Tower Cranes & Hoists', 'slug' => 'tower-gantry-cranes'],
                    ['name' => 'Asphalt Pavers & Screeds', 'slug' => 'asphalt-paving-machines'],
                    ['name' => 'Construction Power & Hand Tools', 'slug' => 'construction-tools'],
                ],
            ],
            'Industrial' => [
                'slug' => 'industrial',
                'children' => [
                    ['name' => 'Industrial Screw Compressors', 'slug' => 'compressors'],
                    ['name' => '3-Phase Electric Motors & Drives', 'slug' => 'motors'],
                    ['name' => 'Industrial Switchgear & Transformers', 'slug' => 'switchgear'],
                    ['name' => 'Industrial Water & Chemical Pumps', 'slug' => 'industrial-pumps'],
                    ['name' => 'CNC Lathes & Metalworking Machinery', 'slug' => 'cnc-metalworking'],
                    ['name' => 'Industrial Automation & PLCs', 'slug' => 'industrial-automation'],
                    ['name' => 'Packaging & Bottling Machinery', 'slug' => 'packaging-equipment'],
                    ['name' => 'Industrial Valves & Mechanical Seals', 'slug' => 'valves-piping-seals'],
                ],
            ],
            'Agricultural' => [
                'slug' => 'agricultural',
                'children' => [
                    ['name' => 'Farm Tractors & Tillage Machinery', 'slug' => 'tractors'],
                    ['name' => 'Harvesters & Combine Heads', 'slug' => 'harvesters-combines'],
                    ['name' => 'Ploughs, Harrows & Rotavators', 'slug' => 'ploughs-harrows-tillers'],
                    ['name' => 'Irrigation Pumps & Sprinkler Systems', 'slug' => 'irrigation-systems'],
                    ['name' => 'Planters, Seeders & Sprayers', 'slug' => 'planters-spreaders'],
                    ['name' => 'Grain Milling & Processing Machines', 'slug' => 'grain-milling-processing'],
                    ['name' => 'Poultry & Livestock Farm Equipment', 'slug' => 'poultry-livestock-equipment'],
                    ['name' => 'Tractor Gearboxes & Implement Spares', 'slug' => 'tractor-spare-parts'],
                ],
            ],
            'Power & Energy' => [
                'slug' => 'power-energy',
                'children' => [
                    ['name' => 'Diesel & Heavy Industrial Generators', 'slug' => 'generators'],
                    ['name' => 'Solar Panels & Inverters', 'slug' => 'solar-inverters'],
                    ['name' => 'Deep Cycle & Lithium Batteries', 'slug' => 'energy-storage-batteries'],
                    ['name' => 'Generator Alternators & AVRs', 'slug' => 'alternators-avr'],
                    ['name' => 'Industrial Power Plants & Turbines', 'slug' => 'power-plants'],
                    ['name' => 'Distribution Transformers & HT Panels', 'slug' => 'electrical-transformers'],
                    ['name' => 'Diesel Fuel Injectors & Injection Pumps', 'slug' => 'generator-injectors-pumps'],
                    ['name' => 'Generator Enclosures & Engine Parts', 'slug' => 'generator-accessories-parts'],
                ],
            ],
            'Scrap & Salvage' => [
                'slug' => 'scrap-salvage',
                'children' => [
                    ['name' => 'Salvage Electronics & Dead Boards', 'slug' => 'salvage-electronics'],
                    ['name' => 'Accident & Salvage Vehicles', 'slug' => 'salvage-vehicles'],
                    ['name' => 'Decommissioned Heavy Equipment Units', 'slug' => 'salvage-heavy-equipment'],
                    ['name' => 'Faulty Generators & Salvage Motors', 'slug' => 'salvage-generators'],
                    ['name' => 'Salvage Home Appliances & Compressors', 'slug' => 'salvage-appliances'],
                    ['name' => 'Damaged Laptops & Screen Broken Units', 'slug' => 'scrap-laptops-motherboards'],
                    ['name' => 'Non-Running Engine Blocks & Castings', 'slug' => 'scrap-engine-blocks'],
                    ['name' => 'Bulk Scrap Metal & Copper Radiators', 'slug' => 'scrap-metal-bulk'],
                ],
            ],
        ];

        // Seed Categories and Subcategories
        foreach ($categoriesStructure as $parentName => $data) {
            $parent = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );

            foreach ($data['children'] as $child) {
                Category::updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 2. Arrangement: Brand -> Category -> Models
        $catalog = [
            'Apple' => [
                'phones' => [
                    'iPhone 11',
                    'iPhone 12',
                    'iPhone 13',
                    'iPhone 13 Pro Max',
                    'iPhone 14 Pro',
                    'iPhone 15',
                    'iPhone 15 Pro Max',
                    'iPhone 16 Pro',
                    'iPad Air M1',
                    'iPad Pro 12.9 M2',
                ],
                'laptops' => [
                    'MacBook Air M1 13-inch',
                    'MacBook Air M2 13-inch',
                    'MacBook Pro 14 (M1 Pro)',
                    'MacBook Pro 16 M2 Max',
                    'MacBook Pro 16 M3 Max',
                ],
                'screens-displays' => [
                    'Apple Studio Display 27-inch 5K',
                    'Pro Display XDR 32-inch 6K',
                ],
                'batteries-chargers' => [
                    'MagSafe 85W Power Adapter',
                    'Apple 140W USB-C Power Adapter',
                ],
            ],

            'Samsung' => [
                'phones' => [
                    'Galaxy S21 Ultra',
                    'Galaxy S22 5G',
                    'Galaxy S23 Ultra',
                    'Galaxy S24 Ultra',
                    'Galaxy A53 5G',
                    'Galaxy A54 5G',
                    'Galaxy Z Fold 5',
                    'Galaxy Note 20 Ultra',
                ],
                'screens-displays' => [
                    'Odyssey G9 49-inch Curved Gaming OLED',
                    'Smart Monitor M8 32-inch 4K',
                ],
                'washing-machines' => [
                    'EcoBubble 8kg Front Load Washer',
                    'QuickDrive 10.5kg Smart Washer',
                    'Top Load Wobble 9kg Washer',
                ],
                'refrigerators' => [
                    'French Door 650L Refrigerator',
                    'Double Door 320L Frost-Free Fridge',
                    'Bespoke 4-Door Flex Fridge',
                ],
                'air-conditioners' => [
                    'WindFree 1.5HP Inverter Split AC',
                    'Digital Inverter 2.0HP Fast Cooling AC',
                ],
            ],

            'HP' => [
                'laptops' => [
                    'EliteBook 840 G5',
                    'EliteBook 840 G6',
                    'EliteBook 840 G7',
                    'EliteBook 840 G8',
                    'ProBook 450 G8',
                    'ProBook 450 G9',
                    'ZBook Power G8 Mobile Workstation',
                    'Omen 16 Gaming Laptop',
                    'Spectre x360 14',
                ],
                'storage-memory' => [
                    'HP EX900 M.2 NVMe PCIe SSD 512GB',
                    'HP EX950 PCIe NVMe SSD 1TB',
                ],
            ],

            'Dell' => [
                'laptops' => [
                    'Latitude 5420',
                    'Latitude 7490',
                    'Latitude 7420 Carbon',
                    'XPS 13 9310',
                    'XPS 15 9520',
                    'Precision 7560 Workstation',
                    'Inspiron 15 3511',
                    'Alienware m15 R7 Gaming',
                ],
                'screens-displays' => [
                    'UltraSharp U2723QE 27-inch 4K Hub Monitor',
                    'Dell P2419H 24-inch FHD IPS Monitor',
                ],
            ],

            'Lenovo' => [
                'laptops' => [
                    'ThinkPad T480',
                    'ThinkPad T490',
                    'ThinkPad X1 Carbon Gen 10',
                    'Legion 5 Pro 16-inch Gaming',
                    'IdeaPad 3 15ALC6',
                    'ThinkPad E15 Gen 4',
                ],
            ],

            'Google' => [
                'phones' => [
                    'Pixel 6 Pro',
                    'Pixel 7',
                    'Pixel 7 Pro',
                    'Pixel 8 Pro',
                    'Pixel 9 Pro XL',
                ],
            ],

            'Tecno' => [
                'phones' => [
                    'Camon 20 Pro 5G',
                    'Camon 30 Premier',
                    'Spark 10 Pro',
                    'Phantom V Fold',
                    'Pova 5 Pro 5G',
                ],
            ],

            'Infinix' => [
                'phones' => [
                    'Note 30 Pro',
                    'Note 40 Pro 5G',
                    'Hot 40 Pro',
                    'Zero 30 5G',
                    'GT 10 Pro Gaming',
                ],
            ],

            'Xiaomi' => [
                'phones' => [
                    'Redmi Note 12 Pro 5G',
                    'Redmi Note 13 Pro+ 5G',
                    'Poco F5 Pro',
                    'Xiaomi 13 Pro Leica',
                ],
            ],

            'Sony' => [
                'audio-sound' => [
                    'WH-1000XM4 Noise-Cancelling Headphones',
                    'WH-1000XM5 Wireless Headphones',
                    'SRS-XG500 Portable Wireless Boombox',
                ],
                'cameras-sensors' => [
                    'Alpha A7 III Full Frame Mirrorless',
                    'Alpha A7 IV Mirrorless Camera',
                    'FX3 Cinema Line Full Frame Camera',
                ],
            ],

            'LG' => [
                'washing-machines' => [
                    'Vivace 9kg AI DD Front Loader Washer',
                    'TurboWash 12kg Smart Inverter Washer',
                    'Top Loader Smart Inverter 10kg Washer',
                ],
                'refrigerators' => [
                    'InstaView Door-in-Door 601L Fridge',
                    'Smart Inverter 260L Double Door Fridge',
                    'Chest Freezer 350L Linear Compressor',
                ],
                'air-conditioners' => [
                    'Dual Inverter 1.5HP GenCool Split AC',
                    'Artcool 2.0HP Mirror Finish Inverter AC',
                ],
                'kitchen-appliances' => [
                    'NeoChef 42L Smart Inverter Microwave Oven',
                ],
            ],

            'Haier Thermocool' => [
                'refrigerators' => [
                    'HRF-350 Double Door Refrigerator',
                    'HTF-319 Silver Deep Freezer',
                    'HTF-519 Turbo Deep Freezer',
                ],
                'washing-machines' => [
                    'Top Load Semi-Automatic 10kg Washer',
                    'Front Load 8kg Inverter Washer',
                ],
                'air-conditioners' => [
                    'SuperCool 1.5HP Split Unit AC',
                    'GenPAL 1.0HP Inverter AC',
                ],
            ],

            'Panasonic' => [
                'air-conditioners' => [
                    'Nanoe-X Inverter 1.5HP Split AC',
                    'Deluxe Non-Inverter 2.0HP AC',
                ],
                'kitchen-appliances' => [
                    'Inverter Convection 34L Microwave Oven',
                ],
            ],

            'Toyota' => [
                'cars' => [
                    'Corolla 1.8L (2008-2013)',
                    'Corolla 1.8L (2014-2019)',
                    'Camry 2.4L "Muscle" (2007-2011)',
                    'Camry 2.5L XLE (2012-2017)',
                    'Camry 3.5L V6 (2018-2022)',
                    'RAV4 2.5L AWD (2013-2018)',
                    'Highlander 3.5L V6 (2010-2016)',
                    'Sienna 3.5L V6 (2011-2018)',
                    'Hilux 2.7L Petrol / 2.8L Diesel',
                    'Land Cruiser Prado TX-L 3.0L',
                ],
                'complete-engines' => [
                    '2AZ-FE 2.4L 4-Cylinder Tokunbo Engine',
                    '2GR-FE 3.5L V6 Complete Tokunbo Engine',
                    '1ZZ-FE 1.8L Engine Assembly',
                    '1TR-FE 2.0L Petrol Hilux Engine',
                    '1GD-FTV 2.8L Turbo Diesel Engine',
                ],
                'gearboxes-transmissions' => [
                    'U241E 4-Speed Automatic Transmission',
                    'U660E 6-Speed Automatic Transmission',
                    'K114 CVT Transmission Box',
                ],
                'ecus-electrical' => [
                    'Denso ECM / ECU Engine Control Unit',
                    'Bosch ABS Pump & Modulator Unit',
                ],
            ],

            'Honda' => [
                'cars' => [
                    'Civic 1.8L EX (2012-2015)',
                    'Accord 2.4L "Evil Spirit" (2008-2012)',
                    'Accord 2.4L (2013-2017)',
                    'CR-V 2.4L AWD (2012-2016)',
                    'Pilot 3.5L Touring (2011-2015)',
                ],
                'complete-engines' => [
                    'K24A 2.4L i-VTEC Complete Engine',
                    'R18A 1.8L VTEC Engine Assembly',
                    'J35A 3.5L V6 Engine Assembly',
                ],
                'gearboxes-transmissions' => [
                    'BAYA 5-Speed Automatic Transmission',
                    'Honda Earth Dreams CVT Transmission',
                ],
            ],

            'Mercedes-Benz' => [
                'cars' => [
                    'C-Class C300 (W204)',
                    'C-Class C300 (W205)',
                    'E-Class E350 (W212)',
                    'GLE 350 4MATIC (W166)',
                    'G-Wagon G63 AMG',
                ],
                'complete-engines' => [
                    'M272 3.5L V6 Complete Engine',
                    'M274 2.0L Turbocharged Engine',
                    'OM642 3.0L V6 CDI Diesel Engine',
                ],
                'gearboxes-transmissions' => [
                    '722.9 7G-Tronic Automatic Transmission',
                    '9G-Tronic Automatic Transmission',
                ],
            ],

            'Lexus' => [
                'cars' => [
                    'ES 350 (2007-2012)',
                    'ES 350 (2013-2018)',
                    'RX 350 (2010-2015)',
                    'GX 460 V8 (2010-2020)',
                ],
                'complete-engines' => [
                    '2GR-FE 3.5L V6 Lexus Engine Assembly',
                    '1UR-FE 4.6L V8 Engine Assembly',
                ],
            ],

            'Ford' => [
                'cars' => [
                    'Edge 3.5L V6 AWD (2011-2014)',
                    'Explorer 3.5L Limited (2012-2017)',
                    'F-150 SuperCrew 3.5L EcoBoost',
                ],
                'trucks' => [
                    'F-650 Super Duty Commercial Truck',
                    'Cargo 1830 Heavy Rigid Hauler',
                ],
            ],

            'Mack' => [
                'trucks' => [
                    'Vision CXN613 Conventional Tractor',
                    'Granite GU713 Dump Truck Chassis',
                    'CH613 Heavy Duty Haulage Tractor',
                ],
            ],

            'DAF' => [
                'trucks' => [
                    'XF 105.460 Super Space Cab 6x2',
                    'CF 85.410 Tipper Haulage Chassis',
                    'LF 55.220 Distribution Truck',
                ],
            ],

            'Bajaj' => [
                'motorcycles' => [
                    'Boxer BM150 Heavy Commercial Motorcycle',
                    'RE Compact 4-Stroke Commercial Tricycle (Keke)',
                    'Maxima Cargo 3-Wheeler Delivery Box',
                ],
            ],

            'TVS' => [
                'motorcycles' => [
                    'King Deluxe Plus Commercial Tricycle (Keke)',
                    'HLX 125cc Commercial Motorcycle',
                ],
            ],

            'Caterpillar' => [
                'excavators' => [
                    'CAT 320D Hydraulic Tracked Excavator',
                    'CAT 330D2L Heavy Excavator',
                ],
                'bulldozers' => [
                    'CAT D6R XL Crawler Dozer',
                    'CAT D8R Heavy Duty Track-Type Tractor',
                ],
                'wheel-loaders' => [
                    'CAT 950H Wheel Loader',
                    'CAT 966H Heavy Wheel Loader',
                ],
                'generators' => [
                    'CAT 3406 350kVA Standby Diesel Generator',
                    'CAT C15 500kVA Heavy Industrial Generator',
                ],
            ],

            'Komatsu' => [
                'excavators' => [
                    'PC200-8 Hydraulic Crawler Excavator',
                    'PC300-8 Heavy Mining Excavator',
                ],
                'bulldozers' => [
                    'D155A-6 Heavy Crawler Dozer',
                ],
                'wheel-loaders' => [
                    'WA380-6 Heavy Wheel Loader',
                ],
            ],

            'JCB' => [
                'wheel-loaders' => [
                    '3CX Eco Backhoe Loader',
                    '4DX Heavy Duty Backhoe Loader',
                ],
                'excavators' => [
                    'JS205 Heavy Tracked Excavator',
                ],
            ],

            'Perkins' => [
                'generators' => [
                    '1103A-33G 30kVA Prime Diesel Generator',
                    '1104A-44TG2 60kVA Silent Soundproof Generator',
                    '1106A-70TAG2 150kVA Heavy Power Generator',
                    '2506A-E15TAG2 500kVA Industrial Generator',
                ],
                'generator-accessories-parts' => [
                    'Perkins 1104 Fuel Injection Pump Assembly',
                    'Perkins Complete Turbocharger Unit',
                ],
            ],

            'Cummins' => [
                'generators' => [
                    'C33D5 33kVA Diesel Generator',
                    '6BTA5.9-G2 125kVA Standby Generator',
                    'QSL9-G5 300kVA Industrial Heavy Generator',
                ],
            ],

            'Mikano' => [
                'generators' => [
                    'Mikano YorPower 20kVA Soundproof Generator',
                    'Mikano 50kVA Soundproof Generator (Perkins Engine)',
                    'Mikano 100kVA Industrial Power Unit',
                ],
            ],

            'Atlas Copco' => [
                'compressors' => [
                    'GA 30 Rotary Screw Air Compressor (30kW)',
                    'GA 75 VSD Variable Speed Compressor',
                    'XAS 97 Portable Towable Diesel Compressor',
                ],
            ],

            'John Deere' => [
                'tractors' => [
                    '5075E 75HP 4WD Utility Tractor',
                    '6120M Heavy Duty Ag Tractor',
                ],
                'harvesters-combines' => [
                    'W70 Self-Propelled Combine Harvester',
                ],
            ],

            'Mahindra' => [
                'tractors' => [
                    '575 DI Sarpanch 45HP Farm Tractor',
                    'Arjun Novo 605 DI 57HP Heavy Tractor',
                ],
            ],

            'Bosch' => [
                'construction-tools' => [
                    'GWS 750-115 Professional Angle Grinder',
                    'GBH 2-26 DRE Rotary Hammer with SDS Plus',
                    'GSB 18V-50 Brushless Cordless Impact Drill',
                    'GCO 14-24 J Metal Cut-Off Saw',
                ],
                'ecus-electrical' => [
                    'Bosch Common Rail Diesel High Pressure Pump CP4',
                    'Bosch 12V 150A Heavy Vehicle Alternator',
                ],
            ],

            'Makita' => [
                'construction-tools' => [
                    'GA4530 4-1/2 Inch Angle Grinder',
                    'HR2470 24mm SDS-Plus Rotary Hammer',
                    'DHP482 18V LXT Cordless Combi Drill',
                ],
            ],

            'DeWalt' => [
                'construction-tools' => [
                    'DWE402 4-1/2 Inch Small Angle Grinder',
                    'DCD771C2 20V MAX Cordless Drill Kit',
                    'D25133K SDS Plus 1-Inch Pistol Grip Hammer',
                ],
            ],
        ];

        // Seed Brands and Models using Brand -> Category -> Models arrangement
        foreach ($catalog as $brandName => $categories) {
            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'is_active' => true,
                ]
            );

            foreach ($categories as $categorySlug => $models) {
                $category = Category::where('slug', $categorySlug)->first()
                    ?? Category::where('name', $categorySlug)->first()
                    ?? Category::first();

                foreach ($models as $modelName) {
                    DeviceModel::updateOrCreate(
                        [
                            'brand_id' => $brand->id,
                            'slug' => Str::slug($brandName . ' ' . $modelName),
                        ],
                        [
                            'category_id' => $category->id,
                            'name' => $modelName,
                        ]
                    );
                }
            }
        }
    }
}
