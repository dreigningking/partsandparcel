<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountriesSeeder::class,
            RolesAndPermissionsSeeder::class,
            CategoriesAndBrandsSeeder::class,
            SubscriptionPlansSeeder::class,
            DemoUsersSeeder::class,
        ]);
    }
}
