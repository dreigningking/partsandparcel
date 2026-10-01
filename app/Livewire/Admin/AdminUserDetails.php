<?php

namespace App\Livewire\Admin;

use App\Models\Moderation;
use App\Models\User;
use Livewire\Component;

class AdminUserDetails extends Component
{
    public User $user;

    public string $rejectReason = '';

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function suspend(): void
    {
        $this->user->update(['suspended_at' => now()]);
        $this->user->refresh();
        session()->flash('status', __('User suspended.'));
    }

    public function unsuspend(): void
    {
        $this->user->update(['suspended_at' => null]);
        $this->user->refresh();
        session()->flash('status', __('User unsuspended.'));
    }

    public function approve(): void
    {
        $pendingModeration = Moderation::query()
            ->where('moderatable_type', User::class)
            ->where('moderatable_id', $this->user->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingModeration) {
            $pendingModeration->update([
                'status' => 'approved',
                'moderated_by' => auth()->id(),
            ]);
        }

        $this->user->update(['verified_at' => now()]);
        $this->user->refresh();
        session()->flash('status', __('User verified.'));
    }

    public function reject(): void
    {
        $pendingModeration = Moderation::query()
            ->where('moderatable_type', User::class)
            ->where('moderatable_id', $this->user->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingModeration) {
            $pendingModeration->update([
                'status' => 'rejected',
                'moderated_by' => auth()->id(),
                'reason' => $this->rejectReason,
            ]);
        }

        session()->flash('status', __('User rejected.'));
    }

    public function delete(): mixed
    {
        $this->user->delete();
        session()->flash('status', __('User deleted.'));

        return redirect()->route('admin.users');
    }

    public function render()
    {
        $this->user->loadCount(['properties', 'subscriptions']);

        return view('livewire.admin.admin-user-details');
    }
}
