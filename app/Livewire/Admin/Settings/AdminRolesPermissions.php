<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Roles & Permissions — Admin Control Center')]
class AdminRolesPermissions extends Component
{
    public bool $showRoleModal = false;

    public ?int $editingRoleId = null;

    public string $name = '';

    public string $roleSlug = '';

    public string $description = '';

    public bool $is_active = true;

    public string $search = '';

    /**
     * Selected permission slugs for the role being created/edited.
     * Admin can check and uncheck in the view.
     *
     * @var array<string>
     */
    public array $selectedPermissions = [];

    /**
     * All permissions defined in our roles and permissions system.
     * Moved from RolesAndPermissionsSeeder.
     *
     * @var array<string, array{label: string, description: string, group: string, icon: string}>
     */
    public array $availablePermissions = [
        // 1. Platform Overview
        'view_dashboard' => [
            'label' => 'View Dashboard',
            'description' => 'Access administrative control center and inspect core platform KPIs.',
            'group' => 'Platform Overview',
            'icon' => 'fas fa-tachometer-alt',
        ],
        'view_analytics' => [
            'label' => 'View Analytics & Reports',
            'description' => 'Access revenue analytics, customer traffic metrics, and activity charts.',
            'group' => 'Platform Overview',
            'icon' => 'fas fa-chart-line',
        ],
        'manage_moderation' => [
            'label' => 'Manage Moderation',
            'description' => 'Review and approve/reject listings, profile identity verifications, and user updates.',
            'group' => 'Platform Overview',
            'icon' => 'fas fa-flag',
        ],

        // 2. Marketplace Operations
        'manage_users' => [
            'label' => 'Manage Users',
            'description' => 'Browse customer/merchant profiles, suspend accounts, and manage payout freeze controls.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-users-cog',
        ],
        'manage_subscriptions' => [
            'label' => 'Manage Subscriptions',
            'description' => 'Inspect vendor subscriptions, assign merchant tiers, and monitor subscription histories.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-award',
        ],
        'manage_listings' => [
            'label' => 'Manage Listings',
            'description' => 'Inspect, activate, pause, or remove items and marketplace listings.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-boxes',
        ],
        'manage_promotions' => [
            'label' => 'Manage Promotions',
            'description' => 'Review, approve, and manage promotional listing boosts and sponsorships.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-rocket',
        ],
        'manage_coupons' => [
            'label' => 'Manage Coupons & Discounts',
            'description' => 'Create, edit, and deactivate discount promo codes and marketing vouchers.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-ticket-alt',
        ],
        'view_invoices' => [
            'label' => 'View Orders & Invoices',
            'description' => 'Track orders, invoices, shipment tracking logs, delivery receipts, and fulfillment status.',
            'group' => 'Marketplace Operations',
            'icon' => 'fas fa-file-invoice',
        ],

        // 3. Trust & Customer Support
        'manage_support' => [
            'label' => 'Customer Support Desk',
            'description' => 'Access the helpdesk, triage customer support conversations, and reply to user tickets.',
            'group' => 'Trust & Support',
            'icon' => 'fas fa-headset',
        ],
        'resolve_disputes' => [
            'label' => 'Resolve Disputes',
            'description' => 'Review dispute claims, examine evidence, order returns, and issue arbitration rulings.',
            'group' => 'Trust & Support',
            'icon' => 'fas fa-gavel',
        ],
        'moderate_discussions' => [
            'label' => 'Moderate Discussions',
            'description' => 'Moderate community discussion threads, questions, comments, and reported user contributions.',
            'group' => 'Trust & Support',
            'icon' => 'fas fa-comments',
        ],

        // 4. Content & Blog
        'manage_blog' => [
            'label' => 'Manage Blog Posts',
            'description' => 'Draft, edit, publish, and delete blog articles, tutorials, and announcements.',
            'group' => 'Content & Blog',
            'icon' => 'fas fa-newspaper',
        ],
        'manage_blog_comments' => [
            'label' => 'Manage Blog Comments',
            'description' => 'Review, approve, and moderate reader comments on published blog articles.',
            'group' => 'Content & Blog',
            'icon' => 'fas fa-comment-dots',
        ],

        // 5. Finances & Escrow
        'manage_payments' => [
            'label' => 'Manage Payments & Escrow',
            'description' => 'Monitor payment gateway transactions, verify webhook charges, and view escrow balances.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-shield-alt',
        ],
        'view_revenue' => [
            'label' => 'View Revenue',
            'description' => 'Inspect platform fee earnings, escrow fee margins, and commission ledgers.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-coins',
        ],
        'manage_payouts' => [
            'label' => 'Manage Payouts',
            'description' => 'Authorize and release seller payouts, inspect seller bank details, and process settlements.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-hand-holding-usd',
        ],

        // 6. System Administration (Super Admin Only)
        'manage_settings' => [
            'label' => 'Manage System Settings',
            'description' => 'Exclusive root control over global settings, geography, categories, roles, and staff.',
            'group' => 'System Administration',
            'icon' => 'fas fa-cogs',
        ],
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Unauthorized. Only Super Administrators can manage roles and permissions.');
    }

    public function openCreateRole(): void
    {
        $this->editingRoleId = null;
        $this->name = '';
        $this->roleSlug = '';
        $this->description = '';
        $this->is_active = true;
        $this->selectedPermissions = [];
        $this->resetErrorBag();
        $this->showRoleModal = true;
    }

    public function openEditRole(int $id): void
    {
        $role = Role::query()->findOrFail($id);
        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->roleSlug = $role->slug;
        $this->description = $role->description ?? '';
        $this->is_active = (bool) $role->is_active;

        $perms = (array) ($role->permissions ?? []);
        if (array_is_list($perms)) {
            $this->selectedPermissions = $perms;
        } else {
            $this->selectedPermissions = array_keys(array_filter($perms));
        }

        $this->resetErrorBag();
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->editingRoleId = null;
        $this->resetErrorBag();
    }

    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = array_keys($this->availablePermissions);
    }

    public function deselectAllPermissions(): void
    {
        $this->selectedPermissions = [];
    }

    public function toggleGroupPermissions(string $group): void
    {
        $groupKeys = [];
        foreach ($this->availablePermissions as $key => $perm) {
            if ($perm['group'] === $group) {
                $groupKeys[] = $key;
            }
        }

        // If all group permissions are already selected, deselect them. Otherwise, select all in this group.
        $allSelected = count(array_intersect($groupKeys, $this->selectedPermissions)) === count($groupKeys);

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $groupKeys));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $groupKeys)));
        }
    }

    public function saveRole(): void
    {
        $slug = $this->roleSlug !== '' ? Str::slug($this->roleSlug, '_') : Str::slug($this->name, '_');

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'roleSlug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($this->editingRoleId),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'selectedPermissions' => ['array'],
        ]);

        // Build associative permissions array matching RolesAndPermissionsSeeder ['slug' => true]
        $perms = [];
        foreach ($this->selectedPermissions as $permKey) {
            if (array_key_exists($permKey, $this->availablePermissions)) {
                $perms[$permKey] = true;
            }
        }

        $data = [
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description ?: null,
            'permissions' => $perms,
            'is_active' => $this->is_active,
        ];

        if ($this->editingRoleId) {
            $role = Role::query()->findOrFail($this->editingRoleId);
            $role->update($data);
            session()->flash('status', __('Role ":name" updated successfully.', ['name' => $this->name]));
        } else {
            Role::query()->create($data);
            session()->flash('status', __('Role ":name" created successfully.', ['name' => $this->name]));
        }

        $this->closeRoleModal();
    }

    public function toggleRoleStatus(int $id): void
    {
        $role = Role::query()->findOrFail($id);
        $role->update(['is_active' => ! $role->is_active]);

        session()->flash('status', __('Role status updated.'));
    }

    public function deleteRole(int $id): void
    {
        $role = Role::query()->withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            session()->flash('error', __('Cannot delete role ":name" because it is assigned to :count staff user(s).', [
                'name' => $role->name,
                'count' => $role->users_count,
            ]));

            return;
        }

        $roleName = $role->name;
        $role->delete();

        session()->flash('status', __('Role ":name" deleted successfully.', ['name' => $roleName]));
    }

    #[Computed]
    public function groupedPermissions(): array
    {
        $grouped = [];
        foreach ($this->availablePermissions as $key => $details) {
            $grouped[$details['group']][$key] = $details;
        }

        return $grouped;
    }

    public function render(): View
    {
        $rolesQuery = Role::query()->withCount('users');

        if (trim($this->search) !== '') {
            $term = '%' . trim($this->search) . '%';
            $rolesQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('slug', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        $roles = $rolesQuery->orderBy('id', 'asc')->get();

        return view('livewire.admin.settings.admin-roles-permissions', [
            'roles' => $roles,
            'groupedPermissions' => $this->groupedPermissions(),
            'availablePermissions' => $this->availablePermissions,
            'selectedPermissions' => $this->selectedPermissions,
            'search' => $this->search,
            'showRoleModal' => $this->showRoleModal,
            'editingRoleId' => $this->editingRoleId,
            'name' => $this->name,
            'roleSlug' => $this->roleSlug,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
    }
}
