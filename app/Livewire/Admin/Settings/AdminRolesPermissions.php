<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Role;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminRolesPermissions extends Component
{
    public bool $showRoleModal = false;

    public ?int $editingRoleId = null;

    public string $name = '';

    public string $roleSlug = '';

    /** @var array<string, array<string>> */
    public array $permissionGroups = [];

    public function openCreateRole(): void
    {
        $this->editingRoleId = null;
        $this->name = '';
        $this->roleSlug = '';
        $this->permissionGroups = array_fill_keys(array_keys(Role::PERMISSION_GROUPS), []);
        $this->resetErrorBag();
        $this->showRoleModal = true;
    }

    public function openEditRole(int $id): void
    {
        $role = Role::query()->findOrFail($id);
        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->roleSlug = (string) ($role->slug ?? '');
        $this->permissionGroups = [];
        foreach (array_keys(Role::PERMISSION_GROUPS) as $group) {
            $this->permissionGroups[$group] = is_array($role->{$group}) ? $role->{$group} : [];
        }
        $this->resetErrorBag();
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->editingRoleId = null;
    }

    public function saveRole(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'roleSlug' => ['nullable', 'string', 'max:255'],
            'permissionGroups' => ['array'],
        ]);

        $known = [];
        foreach (Role::PERMISSION_GROUPS as $group => $actions) {
            $known[$group] = $actions;
        }

        $filtered = [];
        foreach (array_keys(Role::PERMISSION_GROUPS) as $group) {
            $capabilities = $this->permissionGroups[$group] ?? [];
            $filtered[$group] = array_values(array_intersect($capabilities, $known[$group]));
        }

        $slug = $this->roleSlug !== '' ? Str::slug($this->roleSlug) : Str::slug($this->name);

        $slugQuery = Role::query()->where('slug', $slug);
        if ($this->editingRoleId) {
            $slugQuery->where('id', '!=', $this->editingRoleId);
        }
        if ($slugQuery->exists()) {
            $this->addError('roleSlug', __('This slug is already in use.'));

            return;
        }

        $data = [
            'name' => $this->name,
            'slug' => $slug,
        ];
        foreach ($filtered as $group => $capabilities) {
            $data[$group] = $capabilities;
        }

        if ($this->editingRoleId) {
            Role::query()->whereKey($this->editingRoleId)->update($data);
        } else {
            Role::query()->create($data);
        }

        session()->flash('status', __('Role saved.'));
        $this->closeRoleModal();
    }

    public function deleteRole(int $id): void
    {
        $role = Role::query()->findOrFail($id);
        if ($role->users()->exists()) {
            session()->flash('error', __('Cannot delete a role assigned to users.'));

            return;
        }
        $role->delete();
        session()->flash('status', __('Role deleted.'));
    }

    public function render()
    {
        $roles = Role::query()->orderBy('name','asc')->get();

        return view('livewire.admin.settings.admin-roles-permissions', [
            'roles' => $roles,
        ]);
    }
}
