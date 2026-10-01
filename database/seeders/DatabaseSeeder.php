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
        \App\Observers\ListingObserver::$seeding = true;

        $this->call([
            CountriesSeeder::class,
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            CategoriesAndBrandsSeeder::class,
            SubscriptionPlansSeeder::class,
            DemoUsersSeeder::class,
            DemoItemsAndListingsSeeder::class,
            DemoPromoCodesSeeder::class,
            DemoRequestAndResponseSeeder::class,
            PostsSeeder::class,
            EngagementsSeeder::class,
            PromotionsSeeder::class,
            CartsAndOffersSeeder::class,
            MediaSeeder::class,
        ]);
    }
}
