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
        'manage_users' => [
            'label' => 'Manage Users',
            'description' => 'Create, edit, suspend, assign roles, and manage all platform user accounts.',
            'group' => 'User Management',
            'icon' => 'fas fa-users-cog',
        ],
        'view_users' => [
            'label' => 'View Users',
            'description' => 'Browse customer, merchant, and staff profiles, contact info, and activity histories.',
            'group' => 'User Management',
            'icon' => 'fas fa-user-check',
        ],
        'manage_catalog' => [
            'label' => 'Manage Catalog',
            'description' => 'Manage marketplace categories, vehicle & equipment brands, and hardware device models.',
            'group' => 'Catalog & Inventory',
            'icon' => 'fas fa-boxes',
        ],
        'manage_escrow' => [
            'label' => 'Manage Escrow',
            'description' => 'Full control over platform escrow releases, security holds, and payment disputes.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-shield-alt',
        ],
        'view_escrow' => [
            'label' => 'View Escrow',
            'description' => 'Inspect platform escrow balances, transaction hold statuses, and payout schedules.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-money-check-alt',
        ],
        'issue_refunds' => [
            'label' => 'Issue Refunds',
            'description' => 'Authorize and execute buyer refunds for cancelled orders or verified dispute returns.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-hand-holding-usd',
        ],
        'view_financial_reports' => [
            'label' => 'View Financial Reports',
            'description' => 'Access revenue analytics, commission ledgers, subscription earnings, and payout logs.',
            'group' => 'Finances & Escrow',
            'icon' => 'fas fa-chart-line',
        ],
        'resolve_disputes' => [
            'label' => 'Resolve Disputes',
            'description' => 'Review dispute claims, examine buyer/seller evidence, order returns, and issue rulings.',
            'group' => 'Disputes & Support',
            'icon' => 'fas fa-gavel',
        ],
        'manage_tickets' => [
            'label' => 'Manage Tickets',
            'description' => 'Handle customer support tickets, answer helpdesk inquiries, and triage issues.',
            'group' => 'Disputes & Support',
            'icon' => 'fas fa-headset',
        ],
        'view_orders' => [
            'label' => 'View Orders',
            'description' => 'Track orders, invoices, shipment tracking details, delivery logs, and receipts.',
            'group' => 'Disputes & Support',
            'icon' => 'fas fa-shopping-bag',
        ],
        'moderate_discussions' => [
            'label' => 'Moderate Discussions',
            'description' => 'Moderate community discussion threads, comments, and reported user contributions.',
            'group' => 'Community & Content',
            'icon' => 'fas fa-comments',
        ],
        'manage_subscriptions' => [
            'label' => 'Manage Subscriptions',
            'description' => 'Configure vendor tier plans, monthly pricing, commission rates, and feature limits.',
            'group' => 'Platform Settings',
            'icon' => 'fas fa-award',
        ],
        'manage_settings' => [
            'label' => 'Manage Settings',
            'description' => 'Configure global marketplace settings, media upload limits, promotions, and timelines.',
            'group' => 'Platform Settings',
            'icon' => 'fas fa-cogs',
        ],
    ];

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
