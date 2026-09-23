<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Admin staff roles with distinct administrative capabilities
        $adminRoles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super_admin',
                'description' => 'Full root access to all platform controls, finances, escrow, disputes, and users.',
                'permissions' => [
                    'manage_users' => true,
                    'manage_catalog' => true,
                    'manage_escrow' => true,
                    'resolve_disputes' => true,
                    'manage_subscriptions' => true,
                    'view_financial_reports' => true,
                    'manage_settings' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Dispute Arbitrator',
                'slug' => 'dispute_arbitrator',
                'description' => 'Staff reviewer handling post-sale issues, dispute evidence, returns, and escrow releases.',
                'permissions' => [
                    'resolve_disputes' => true,
                    'view_escrow' => true,
                    'issue_refunds' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Catalog Manager',
                'slug' => 'catalog_manager',
                'description' => 'Staff curator managing auto/tech categories, brands, and device models.',
                'permissions' => [
                    'manage_catalog' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Customer Support',
                'slug' => 'customer_support',
                'description' => 'Staff agent assisting buyers and sellers with tickets, inquiries, order tracking, and dispute triaging.',
                'permissions' => [
                    'manage_tickets' => true,
                    'view_orders' => true,
                    'view_users' => true,
                    'moderate_discussions' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($adminRoles as $roleData) {
            Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }
}
