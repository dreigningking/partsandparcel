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
        // 1. Categories
        $categories = [
            'Smartphones & Tablets' => [
                'Screens & Displays',
                'Batteries & Charging',
                'Cameras & Sensors',
                'Motherboards & Logic Boards',
            ],
            'Laptops & Computers' => [
                'Keyboards & Trackpads',
                'RAM & Storage',
                'Cooling Fans & Heatsinks',
                'Displays & Hinges',
            ],
            'Engines & Drivetrain' => [
                'Complete Engines',
                'Transmissions & Gearboxes',
                'Alternators & Starters',
                'Fuel Pumps & Injectors',
            ],
            'Brakes & Suspension' => [
                'Brake Calipers & Rotors',
                'Shock Absorbers & Struts',
                'Control Arms & Ball Joints',
            ],
            'Salvage & Scrap' => [
                'Scrap Metal & Frames',
                'Damaged Donor Units',
            ],
        ];

        foreach ($categories as $parentName => $subCategories) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'is_active' => true]
            );

            foreach ($subCategories as $subName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($subName)],
                    ['name' => $subName, 'parent_id' => $parent->id, 'is_active' => true]
                );
            }
        }

        // 2. Brands & Models
        $brandsWithModels = [
            'Apple' => [
                'category' => 'Smartphones & Tablets',
                'models' => ['iPhone 12', 'iPhone 13', 'iPhone 14 Pro', 'iPad Air M1', 'MacBook Pro 14 (M1 Pro)'],
            ],
            'Samsung' => [
                'category' => 'Smartphones & Tablets',
                'models' => ['Galaxy S21 Ultra', 'Galaxy S22', 'Galaxy S23 Ultra', 'Galaxy A53'],
            ],
            'Toyota' => [
                'category' => 'Engines & Drivetrain',
                'models' => ['Corolla (2014-2019)', 'Camry (2018-2022)', 'RAV4 (2019+)', 'Hilux 2.8 GD-6'],
            ],
            'Honda' => [
                'category' => 'Engines & Drivetrain',
                'models' => ['Civic (2016-2021)', 'Accord (2018-2022)', 'CR-V (2017+)'],
            ],
            'Dell' => [
                'category' => 'Laptops & Computers',
                'models' => ['XPS 13 (9310)', 'XPS 15 (9520)', 'Latitude 5420'],
            ],
        ];

        foreach ($brandsWithModels as $brandName => $data) {
            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName, 'is_active' => true]
            );

            $category = Category::where('name', $data['category'])->first() ?? Category::first();

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
