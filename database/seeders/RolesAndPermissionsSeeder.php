<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Tier 1: Platform Overview + Content & Blog
        $tier1Permissions = [
            'view_dashboard' => true,
            'view_analytics' => true,
            'manage_moderation' => true,
            'manage_blog' => true,
            'manage_blog_comments' => true,
        ];

        // Tier 2: Tier 1 + Trust & Support
        $tier2Permissions = array_merge($tier1Permissions, [
            'manage_support' => true,
            'resolve_disputes' => true,
            'view_invoices' => true,
            'moderate_discussions' => true,
        ]);

        // Tier 3: Tier 2 + Marketplace Operations
        $tier3Permissions = array_merge($tier2Permissions, [
            'manage_users' => true,
            'manage_subscriptions' => true,
            'manage_listings' => true,
            'manage_promotions' => true,
            'manage_coupons' => true,
        ]);

        // Tier 4: Tier 3 + Finance & Escrow (Can do everything EXCEPT System Settings)
        $tier4Permissions = array_merge($tier3Permissions, [
            'manage_payments' => true,
            'view_revenue' => true,
            'manage_payouts' => true,
        ]);

        // Tier 5: Tier 4 + System Administration (Full root access)
        $tier5Permissions = array_merge($tier4Permissions, [
            'manage_settings' => true,
            '*' => true,
        ]);

        // Admin staff roles with distinct, additive administrative tiers
        $adminRoles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super_admin',
                'description' => 'Tier 5 Root Administrator: Full access across all platform operations and exclusive control over system settings, roles, and staff.',
                'permissions' => $tier5Permissions,
                'is_active' => true,
            ],
            [
                'name' => 'General Operations Manager',
                'slug' => 'general_manager',
                'description' => 'Tier 4 Operations Executive: Full management over marketplace operations, trust, customer support, and financial payouts (excludes system settings).',
                'permissions' => $tier4Permissions,
                'is_active' => true,
            ],
            [
                'name' => 'Marketplace Operations Lead',
                'slug' => 'operations_lead',
                'description' => 'Tier 3 Operations Specialist: Manages users, listings, subscriptions, promotions, disputes, support desk, and content.',
                'permissions' => $tier3Permissions,
                'is_active' => true,
            ],
            [
                'name' => 'Customer Support & Trust Officer',
                'slug' => 'customer_support',
                'description' => 'Tier 2 Support Specialist: Handles user support conversations, helpdesk tickets, dispute claims, order tracking, and content moderation.',
                'permissions' => $tier2Permissions,
                'is_active' => true,
            ],
            [
                'name' => 'Content & Community Specialist',
                'slug' => 'content_specialist',
                'description' => 'Tier 1 Content Specialist: Manages blog articles, reader comments, and general platform overview & moderation.',
                'permissions' => $tier1Permissions,
                'is_active' => true,
            ],

            // Legacy / specialized staff roles for compatibility with existing demo seeds
            [
                'name' => 'Dispute Arbitrator',
                'slug' => 'dispute_arbitrator',
                'description' => 'Specialized reviewer handling post-sale issues, dispute evidence, order inspection, and moderation.',
                'permissions' => [
                    'view_dashboard' => true,
                    'resolve_disputes' => true,
                    'manage_moderation' => true,
                    'view_invoices' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Catalog Manager',
                'slug' => 'catalog_manager',
                'description' => 'Specialized staff curator managing marketplace listings, categories, and moderation.',
                'permissions' => [
                    'view_dashboard' => true,
                    'manage_listings' => true,
                    'manage_moderation' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($adminRoles as $roleData) {
            Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }
}
