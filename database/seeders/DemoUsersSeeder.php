<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Component;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super_admin')->first();

        // 1. Admin user (Admin access with elevated permissions via role_id)
        User::updateOrCreate(
            ['email' => 'admin@partsandparcel.com'],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole?->id,
                'phone' => '+2348011112222',
                'is_verified' => true,
                'theme_preference' => 'system',
                'country_code' => 'NG',
                'currency' => 'NGN',
            ]
        );

        // 2. User Emeka: Both buyer & seller (Auto & Tech salvage merchant)
        $emeka = User::updateOrCreate(
            ['email' => 'emeka@partsandparcel.com'],
            [
                'name' => 'Emeka Okafor',
                'password' => Hash::make('password'),
                'role_id' => null, // Standard user
                'phone' => '+2348033334444',
                'business_name' => 'Apex Auto & Tech Salvage',
                'bio' => 'Certified dismantler and original OEM parts supplier located in Ladipo Market, Lagos.',
                'is_verified' => true,
                'theme_preference' => 'dark',
                'country_code' => 'NG',
                'currency' => 'NGN',
            ]
        );

        $emekaLocation = Location::updateOrCreate(
            ['user_id' => $emeka->id, 'label' => 'Warehouse & Yard'],
            [
                'contact_name' => 'Emeka Okafor',
                'phone' => '+2348033334444',
                'address_line_1' => 'Plot 14 Ladipo Auto Spares Market, Mushin',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'country' => 'Nigeria',
                'is_default' => true,
            ]
        );

        BankAccount::updateOrCreate(
            ['user_id' => $emeka->id, 'account_number' => '0123456789'],
            [
                'bank_name' => 'Zenith Bank',
                'bank_code' => '057',
                'account_name' => 'Apex Auto & Tech Salvage Ltd',
                'recipient_code' => 'RCP_demo_seller_1',
                'currency' => 'NGN',
                'is_default' => true,
                'verified_at' => now(),
            ]
        );

        // 3. User Tunde: Both buyer & seller (Repair technician with subscription)
        $tunde = User::updateOrCreate(
            ['email' => 'tunde@partsandparcel.com'],
            [
                'name' => 'Tunde Bakare',
                'password' => Hash::make('password'),
                'role_id' => null,
                'phone' => '+2348055556666',
                'business_name' => 'FixLogic Diagnostics & Repairs',
                'bio' => 'Certified electronic device technician and automobile ECU diagnostic specialist.',
                'is_verified' => true,
                'theme_preference' => 'system',
                'country_code' => 'NG',
                'currency' => 'NGN',
            ]
        );

        Location::updateOrCreate(
            ['user_id' => $tunde->id, 'label' => 'Workshop'],
            [
                'contact_name' => 'Tunde Bakare',
                'phone' => '+2348055556666',
                'address_line_1' => '12 Computer Village Road, Ikeja',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'country' => 'Nigeria',
                'is_default' => true,
            ]
        );

        $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->first();
        if ($proPlan) {
            Subscription::updateOrCreate(
                ['user_id' => $tunde->id, 'subscription_plan_id' => $proPlan->id],
                [
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'response_limit' => $proPlan->response_limit,
                ]
            );
        }

        // 4. User Chioma: Regular customer who buys, creates requests, and can sell
        $chioma = User::updateOrCreate(
            ['email' => 'chioma@partsandparcel.com'],
            [
                'name' => 'Chioma Adeyemi',
                'password' => Hash::make('password'),
                'role_id' => null,
                'phone' => '+2348077778888',
                'theme_preference' => 'light',
                'country_code' => 'NG',
                'currency' => 'NGN',
            ]
        );

        Location::updateOrCreate(
            ['user_id' => $chioma->id, 'label' => 'Home Residence'],
            [
                'contact_name' => 'Chioma Adeyemi',
                'phone' => '+2348077778888',
                'address_line_1' => 'Flat 4B, Admiralty Way, Lekki Phase 1',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'country' => 'Nigeria',
                'is_default' => true,
            ]
        );

        // 5. Sample Listings created by Emeka
        $camryModel = DeviceModel::where('name', 'like', '%Camry%')->first() ?? DeviceModel::first();
        $iphoneModel = DeviceModel::where('name', 'like', '%iPhone 13%')->first() ?? DeviceModel::first();

        if ($camryModel) {
            $camryItem = Item::updateOrCreate(
                ['user_id' => $emeka->id, 'serial_number' => '2AR-FE-984321'],
                [
                    'model_id' => $camryModel->id,
                    'condition_status' => 'tested_used',
                    'status' => 'available',
                    'acquired_at' => now()->subDays(10),
                ]
            );

            Listing::updateOrCreate(
                ['user_id' => $emeka->id, 'assetable_type' => Item::class, 'assetable_id' => $camryItem->id],
                [
                    'location_id' => $emekaLocation->id,
                    'quantity' => 1,
                    'price' => 450000.00,
                    'status' => 'active',
                    'warranty_period_days' => 14,
                    'warranty_terms' => '14 days testing warranty. Replacement or full refund if compression fails.',
                    'description' => 'Clean Belgium-used 2.5L 2AR-FE complete engine with wiring harness and throttle body.',
                ]
            );
        }

        if ($iphoneModel) {
            $iphoneItem = Item::updateOrCreate(
                ['user_id' => $emeka->id, 'serial_number' => 'A2633-SN-498112'],
                [
                    'model_id' => $iphoneModel->id,
                    'condition_status' => 'donor_unit',
                    'status' => 'available',
                    'acquired_at' => now()->subDays(5),
                ]
            );

            $screenComponent = Component::updateOrCreate(
                ['item_id' => $iphoneItem->id, 'name' => 'OEM Super Retina XDR OLED Display'],
                [
                    'condition_status' => 'tested_grade_a',
                    'serial_number' => 'SCR-IP13-8891',
                    'status' => 'available',
                ]
            );

            $screenListing = Listing::updateOrCreate(
                ['user_id' => $emeka->id, 'assetable_type' => Component::class, 'assetable_id' => $screenComponent->id],
                [
                    'location_id' => $emekaLocation->id,
                    'quantity' => 1,
                    'price' => 85000.00,
                    'status' => 'active',
                    'warranty_period_days' => 7,
                    'warranty_terms' => '7 days warranty against touch freeze or dead pixels. Adhesive film must remain intact.',
                    'description' => '100% original OLED screen pulled from clean iCloud-locked donor iPhone 13. True Tone transferable.',
                ]
            );

            // 6. Request created by Chioma (buyer side of a user)
            Discussion::updateOrCreate(
                ['user_id' => $chioma->id, 'title' => 'Urgent: Need clean iPhone 13 OEM Screen + Installation in Lekki/Ikeja'],
                [
                    'type' => 'item',
                    'category_id' => $iphoneModel->category_id,
                    'model_id' => $iphoneModel->id,
                    'body' => "My iPhone 13 fell and the display is cracked with green lines. I need an original OEM screen (no copy/incell please) plus someone who can professionally transfer True Tone and install it.\nTotal budget: 100,000 NGN.",
                    'status' => 'open',
                ]
            );

            // 7. Cart created by Chioma with items from seller Emeka
            $cart = Cart::updateOrCreate(
                ['buyer_id' => $chioma->id, 'seller_id' => $emeka->id],
                ['status' => 'active', 'expires_at' => now()->addDays(7)]
            );

            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'listing_id' => $screenListing->id],
                ['quantity' => 1, 'unit_price' => $screenListing->price]
            );
        }
    }
}
