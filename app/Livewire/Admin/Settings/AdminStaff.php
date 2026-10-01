<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Notifications\StaffInviteNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Staff Management — Admin Control Center')]
class AdminStaff extends Component
{
    public bool $showModal = false;

    public ?int $editingUserId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public ?int $role_id = null;

    public string $search = '';

    public ?int $roleFilter = null;

    public function openCreate(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = Role::where('is_active', true)->orderBy('name', 'asc')->value('id');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $userId): void
    {
        $user = User::query()->whereKey($userId)->whereNotNull('role_id')->firstOrFail();

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role_id = $user->role_id;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingUserId = null;
        $this->resetErrorBag();
    }

    public function saveStaff(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];

        if ($this->editingUserId) {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->editingUserId);
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $this->validate($rules);

        $role = Role::query()->whereKey($this->role_id)->firstOrFail();

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $role->id,
        ];

        if ($this->editingUserId) {
            if ($this->password !== '') {
                $payload['password'] = Hash::make($this->password);
            }

            User::query()->whereKey($this->editingUserId)->update($payload);
            session()->flash('status', __('Staff account ":name" updated successfully.', ['name' => $this->name]));
        } else {
            $plainPassword = $this->password;
            $payload['password'] = Hash::make($plainPassword);
            $payload['email_verified_at'] = now();
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'country_code')) {
                $payload['country_code'] = auth()->user()?->country_code ?? 'NG';
            }

            $user = User::query()->create($payload);

            try {
                $user->notify(new StaffInviteNotification($user, $plainPassword));
            } catch (\Throwable $e) {
                report($e);
            }

            session()->flash('status', __('Staff member ":name" created and invitation email queued.', ['name' => $this->name]));
        }

        $this->closeModal();
    }

    public function deleteStaff(int $userId): void
    {
        $user = User::query()->whereKey($userId)->whereNotNull('role_id')->firstOrFail();

        if (auth()->id() === $user->id) {
            session()->flash('error', __('You cannot remove your own administrative privileges.'));

            return;
        }

        $userName = $user->name;
        // Revoke admin access by nullifying role_id
        $user->update(['role_id' => null]);

        session()->flash('status', __('Admin privileges revoked for ":name".', ['name' => $userName]));
    }

    public function render(): View
    {
        $staffQuery = User::query()
            ->with('role')
            ->whereNotNull('role_id');

        if (trim($this->search) !== '') {
            $term = '%' . trim($this->search) . '%';
            $staffQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        if ($this->roleFilter) {
            $staffQuery->where('role_id', $this->roleFilter);
        }

        $staff = $staffQuery->orderBy('name', 'asc')->get();
        $adminRoles = Role::query()->where('is_active', true)->orderBy('name', 'asc')->get();

        return view('livewire.admin.settings.admin-staff', [
            'staff' => $staff,
            'adminRoles' => $adminRoles,
            'totalStaffCount' => User::whereNotNull('role_id')->count(),
            'totalRolesCount' => Role::where('is_active', true)->count(),
            'search' => $this->search,
            'roleFilter' => $this->roleFilter,
            'showModal' => $this->showModal,
            'editingUserId' => $this->editingUserId,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $this->role_id,
        ]);
    }
}
