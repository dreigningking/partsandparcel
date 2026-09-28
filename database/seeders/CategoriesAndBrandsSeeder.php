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

        // Collect all active primary and child category slugs
        $allValidSlugs = [];
        foreach ($categoriesStructure as $parentData) {
            $allValidSlugs[] = $parentData['slug'];
            foreach ($parentData['children'] as $child) {
                $allValidSlugs[] = $child['slug'];
            }
        }

        // Clean up obsolete categories from previous test seeders (only if not linked to existing models/discussions)
        Category::whereNotIn('slug', $allValidSlugs)
            ->whereDoesntHave('deviceModels')
            ->whereDoesntHave('discussions')
            ->delete();

        foreach ($categoriesStructure as $parentName => $data) {
            $parent = Category::where('slug', $data['slug'])
                ->orWhere('name', $parentName)
                ->first();

            if ($parent) {
                $parent->update([
                    'name' => $parentName,
                    'slug' => $data['slug'],
                    'parent_id' => null,
                    'is_active' => true,
                ]);
            } else {
                $parent = Category::create([
                    'name' => $parentName,
                    'slug' => $data['slug'],
                    'parent_id' => null,
                    'is_active' => true,
                ]);
            }

            foreach ($data['children'] as $child) {
                $existingChild = Category::where('slug', $child['slug'])
                    ->orWhere('name', $child['name'])
                    ->first();

                if ($existingChild) {
                    $existingChild->update([
                        'name' => $child['name'],
                        'slug' => $child['slug'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]);
                } else {
                    Category::create([
                        'name' => $child['name'],
                        'slug' => $child['slug'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 2. Brands & Models
        $brandsWithModels = [
            'Apple' => [
                'category' => 'phones',
                'models' => [
                    'iPhone 11',
                    'iPhone 12',
                    'iPhone 13',
                    'iPhone 14 Pro',
                    'iPhone 15 Pro Max',
                    'iPad Air M1',
                    'iPad Pro 12.9 M2',
                    'MacBook Air M1',
                    'MacBook Pro 14 (M1 Pro)',
                    'MacBook Pro 16 M2 Max',
                ],
            ],
            'Samsung' => [
                'category' => 'phones',
                'models' => [
                    'Galaxy S21 Ultra',
                    'Galaxy S22',
                    'Galaxy S23 Ultra',
                    'Galaxy S24 Ultra',
                    'Galaxy A53 5G',
                    'Galaxy A54',
                    'Galaxy Note 20 Ultra',
                    'EcoBubble 8kg Washer',
                    'Double Door 320L Refrigerator',
                ],
            ],
            'Google' => [
                'category' => 'phones',
                'models' => [
                    'Pixel 6 Pro',
                    'Pixel 7',
                    'Pixel 7 Pro',
                    'Pixel 8 Pro',
                ],
            ],
            'Tecno' => [
                'category' => 'phones',
                'models' => [
                    'Camon 20 Pro',
                    'Camon 30 Premier',
                    'Spark 10 Pro',
                    'Phantom V Fold',
                ],
            ],
            'Infinix' => [
                'category' => 'phones',
                'models' => [
                    'Note 30 Pro',
                    'Hot 30 Play',
                    'Zero 30 5G',
                    'GT 10 Pro',
                ],
            ],
            'HP' => [
                'category' => 'laptops',
                'models' => [
                    'EliteBook 840 G5',
                    'EliteBook 840 G7',
                    'EliteBook 850 G6',
                    'ProBook 450 G8',
                    'Envy x360 15',
                    'Spectre x360 14',
                    'Omen 16 Gaming',
                    'LaserJet Pro M404n',
                ],
            ],
            'Dell' => [
                'category' => 'laptops',
                'models' => [
                    'XPS 13 (9310)',
                    'XPS 15 (9520)',
                    'Latitude 5420',
                    'Latitude 7490',
                    'Latitude 5510',
                    'Inspiron 15 (3511)',
                    'Precision 5560 Workstation',
                ],
            ],
            'Lenovo' => [
                'category' => 'laptops',
                'models' => [
                    'ThinkPad X1 Carbon Gen 9',
                    'ThinkPad T14 Gen 2',
                    'ThinkPad E14',
                    'ThinkPad X280',
                    'Legion 5 Pro',
                    'IdeaPad Slim 3',
                ],
            ],
            'Toyota' => [
                'category' => 'cars',
                'models' => [
                    'Corolla (2014-2019)',
                    'Corolla (2020+)',
                    'Camry (2018-2022)',
                    'Camry (2012-2017)',
                    'RAV4 (2019+)',
                    'Hilux 2.8 GD-6',
                    'Land Cruiser Prado (2020)',
                    'Highlander (2017-2021)',
                    'HiAce Bus (Commuter)',
                ],
            ],
            'Honda' => [
                'category' => 'cars',
                'models' => [
                    'Civic (2016-2021)',
                    'Accord (2018-2022)',
                    'Accord (2013-2017)',
                    'CR-V (2017+)',
                    'Pilot (2016-2022)',
                ],
            ],
            'Lexus' => [
                'category' => 'cars',
                'models' => [
                    'RX350 (2010-2015)',
                    'RX350 (2016-2022)',
                    'ES350 (2013-2018)',
                    'ES350 (2019+)',
                    'GX460 (2014-2021)',
                    'IS250 (2014-2019)',
                ],
            ],
            'Mercedes-Benz' => [
                'category' => 'cars',
                'models' => [
                    'C300 (W204)',
                    'C300 (W205)',
                    'E350 (W212)',
                    'E300 (W213)',
                    'GLE 350 (2016-2019)',
                    'ML 350 (W166)',
                    'Actros 3340 Prime Mover',
                ],
            ],
            'Sinotruk' => [
                'category' => 'trucks',
                'models' => [
                    'HOWO 371 6x4 Tipper',
                    'HOWO A7 420 Tractor Head',
                    'HOWO 336 10-Wheeler Dump',
                ],
            ],
            'Mack' => [
                'category' => 'trucks',
                'models' => [
                    'Granite 6x4 Dump Truck',
                    'Anthem Tractor Head',
                    'CH613 Vision Truck',
                ],
            ],
            'Bajaj' => [
                'category' => 'motorcycles',
                'models' => [
                    'Boxer BM150 Motorcycle',
                    'RE Compact 4S Tricycle (Keke)',
                    'Maxima Cargo 3-Wheeler',
                    'Pulsar 200NS',
                ],
            ],
            'LG' => [
                'category' => 'washing-machines',
                'models' => [
                    'Vivace 9kg Front Load Washer',
                    'Smart Inverter Top Load 11kg',
                    'InstaView Door-in-Door Refrigerator 601L',
                    'Dual Inverter 1.5HP Split AC',
                    'NeoChef 42L Microwave Oven',
                ],
            ],
            'Haier Thermocool' => [
                'category' => 'refrigerators',
                'models' => [
                    'Turbo Chest Freezer 250L',
                    'GenPAL 1.5HP Inverter AC',
                    'Luxury Top Load 8kg Washer',
                    'Double Door Frost Free Fridge 200L',
                ],
            ],
            'Hisense' => [
                'category' => 'air-conditioners',
                'models' => [
                    '1.5HP Copper Split AC',
                    '205L Double Door Top Mount Fridge',
                    'PureJet 8kg Front Load Washer',
                ],
            ],
            'Caterpillar' => [
                'category' => 'excavators',
                'models' => [
                    '320D Hydraulic Excavator',
                    '330D L Excavator',
                    '336D Heavy Excavator',
                    'D6R Track-Type Tractor (Bulldozer)',
                    'D8R Heavy Crawler Bulldozer',
                    '966H Wheel Loader',
                    '428F Backhoe Loader',
                    '3512B 1500kVA Diesel Generator',
                    'C18 700kVA Soundproof Generator',
                ],
            ],
            'Komatsu' => [
                'category' => 'excavators',
                'models' => [
                    'PC200-8 Hydraulic Excavator',
                    'PC300-8 Heavy Excavator',
                    'D65EX-16 Crawler Bulldozer',
                    'WA380-6 Wheel Loader',
                    'D155A Heavy Dozer',
                ],
            ],
            'Volvo' => [
                'category' => 'excavators',
                'models' => [
                    'EC210B Prime Excavator',
                    'EC380D Heavy Excavator',
                    'L120F Wheel Loader',
                    'L150H Wheel Loader',
                    'FMX 400 Tipper Truck',
                ],
            ],
            'Hitachi' => [
                'category' => 'excavators',
                'models' => [
                    'ZX200-5G Hydraulic Excavator',
                    'ZX330-5G Heavy Excavator',
                    'ZX350LCH Quarry Excavator',
                ],
            ],
            'JCB' => [
                'category' => 'wheel-loaders',
                'models' => [
                    '3CX Backhoe Loader',
                    '4CX 4WS Backhoe Loader',
                    '531-70 Telescopic Handler',
                    'JS205 Tracked Excavator',
                ],
            ],
            'Tadano' => [
                'category' => 'cranes',
                'models' => [
                    'GT-550E 55-Ton Truck Crane',
                    'GR-300EX 30-Ton Rough Terrain Crane',
                    'TG-500E Hydraulic Mobile Crane',
                ],
            ],
            'Liebherr' => [
                'category' => 'cranes',
                'models' => [
                    'LTM 1050 50-Ton All Terrain Crane',
                    'LTM 1100 Mobile Crane',
                    'R 920 Compact Crawler Excavator',
                ],
            ],
            'Cummins' => [
                'category' => 'generators',
                'models' => [
                    '6BT5.9G2 100kVA Diesel Generator',
                    'QSL9 250kVA Generator Set',
                    'KTA50-G3 1500kVA Power Plant',
                    'NT855 Heavy Diesel Engine',
                ],
            ],
            'Perkins' => [
                'category' => 'generators',
                'models' => [
                    '1103A-33G 30kVA Generator',
                    '1104A-44TG2 60kVA Generator',
                    '2506C-E15TAG2 500kVA Generator',
                    '4006-23TAG2A 800kVA Generator',
                ],
            ],
            'Mikano' => [
                'category' => 'generators',
                'models' => [
                    '20kVA Perkins Powered Soundproof Gen',
                    '50kVA Perkins Diesel Generator',
                    '100kVA Prime Power Generator',
                    '250kVA Heavy Mikano Generator',
                ],
            ],
            'Elepaq' => [
                'category' => 'generators',
                'models' => [
                    'SV6800E 3.5kVA Key-Starter Generator',
                    'SV20000E2 10kVA Petrol Generator',
                    'Constant 4.5kVA Manual Generator',
                ],
            ],
            'Tiger' => [
                'category' => 'generators',
                'models' => [
                    'TG950 Small 650W Petrol Generator',
                    'TGR2900 2.5kVA Generator',
                ],
            ],
            'Felicity Solar' => [
                'category' => 'solar-inverters',
                'models' => [
                    'FL-IVPS 5000W 48V Hybrid Solar Inverter',
                    '10kWh 48V LiFePO4 Lithium Battery',
                    '550W Monocrystalline Solar Panel',
                ],
            ],
            'Ingersoll Rand' => [
                'category' => 'compressors',
                'models' => [
                    'UP6 15 Rotary Screw Air Compressor',
                    'SSR M22 30HP Industrial Compressor',
                    '2475 Two-Stage Piston Compressor Pump',
                ],
            ],
            'Atlas Copco' => [
                'category' => 'compressors',
                'models' => [
                    'GA 15 VSD+ Variable Speed Screw Compressor',
                    'GA 37 Stationary Rotary Compressor',
                    'XAS 88 Towable Diesel Air Compressor',
                ],
            ],
            'ABB' => [
                'category' => 'motors',
                'models' => [
                    'M2BAX 15kW 3-Phase Induction Motor',
                    'M3AA 7.5kW Aluminum Motor',
                    'ACS880 Industrial Frequency Inverter',
                    'Emax 2 High Voltage Air Circuit Breaker',
                ],
            ],
            'Siemens' => [
                'category' => 'switchgear',
                'models' => [
                    'SIMOTICS GP 11kW Low Voltage Motor',
                    'SIMATIC S7-1200 PLC Processor',
                    'SENTRON 3VA Molded Case Circuit Breaker',
                    'SIRIUS 3RW Soft Starter',
                ],
            ],
            'Massey Ferguson' => [
                'category' => 'tractors',
                'models' => [
                    'MF 375 75HP 2WD Farm Tractor',
                    'MF 385 85HP 4WD Heavy Tractor',
                    'MF 240 50HP Compact Tractor',
                ],
            ],
            'John Deere' => [
                'category' => 'tractors',
                'models' => [
                    '5075E 75HP Utility Tractor',
                    '6120M Heavy Duty Ag Tractor',
                    'W70 Self-Propelled Combine Harvester',
                ],
            ],
            'Mahindra' => [
                'category' => 'tractors',
                'models' => [
                    '575 DI Sarpanch 45HP Tractor',
                    '475 DI Bhoomiputra 42HP Tractor',
                    'Arjun Novo 605 DI 57HP Tractor',
                ],
            ],
            'Bosch' => [
                'category' => 'construction-tools',
                'models' => [
                    'GWS 750-115 Professional Angle Grinder',
                    'GBH 2-26 DRE Rotary Hammer with SDS Plus',
                    'GSB 18V-50 Brushless Impact Drill',
                    'GCO 14-24 J Metal Cut-Off Saw',
                ],
            ],
            'Makita' => [
                'category' => 'construction-tools',
                'models' => [
                    'GA4530 4-1/2 Inch Angle Grinder',
                    'HR2470 24mm Rotary Hammer Drill',
                    'DHP482 18V LXT Combi Drill',
                ],
            ],
            'DeWalt' => [
                'category' => 'construction-tools',
                'models' => [
                    'DWE402 4-1/2 Small Angle Grinder',
                    'DCD771C2 20V MAX Cordless Drill Kit',
                    'D25133K SDS Plus 1-Inch Pistol Grip Hammer',
                ],
            ],
        ];

        foreach ($brandsWithModels as $brandName => $data) {
            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName, 'is_active' => true]
            );

            $category = Category::where('slug', $data['category'])
                ->orWhere('name', $data['category'])
                ->first() ?? Category::first();

            foreach ($data['models'] as $modelName) {
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
