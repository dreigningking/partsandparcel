<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Role;
use App\Models\User;
use App\Notifications\StaffInviteNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminStaff extends Component
{
    public bool $showModal = false;

    public ?int $editingUserId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public ?int $role_id = null;

    public function openCreate(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = Role::orderBy('name', 'asc')->value('id');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $userId): void
    {
        if ($userId === auth()->id()) {
            return;
        }
        $user = User::query()->whereKey($userId)->whereHas('role')->firstOrFail();
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
            'active_role' => 'admin',
        ];

        if ($this->password !== '') {
            $payload['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            User::query()->whereKey($this->editingUserId)->update(
                $this->password !== '' ? $payload : [
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'role_id' => $payload['role_id'],
                    'active_role' => $payload['active_role'],
                ]
            );
        } else {
            $payload['password'] = Hash::make($this->password);
            $payload['email_verified_at'] = now();
            $payload['country_id'] = auth()->user()?->country_id;
            $user = User::query()->create($payload);
            $user->notify(new StaffInviteNotification($user, $this->password));
        }

        session()->flash('status', __('Staff member saved.'));
        $this->closeModal();
    }

    public function deleteStaff(int $userId): void
    {
        $user = User::query()->whereKey($userId)->whereHas('role')->firstOrFail();
        if (auth()->id() === $user->id) {
            session()->flash('error', __('You cannot remove yourself from admin.'));

            return;
        }
        $user->update(['role_id' => null,'active_role' => 'buyer']);
        session()->flash('status', __('Staff member removed from admin.'));
    }

    public function render()
    {
        $staff = User::query()
            ->with('role')
            ->whereHas('role')
            ->orderBy('name','asc')
            ->get();

        $adminRoles = Role::orderBy('name', 'asc')->get();

        return view('livewire.admin.settings.admin-staff', [
            'staff' => $staff,
            'adminRoles' => $adminRoles,
        ]);
    }
}
