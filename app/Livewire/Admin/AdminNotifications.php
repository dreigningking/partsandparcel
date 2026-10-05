<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Notifications Center — Admin Control Center')]
class AdminNotifications extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, unread

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function markAsRead(string $id): void
    {
        $user = Auth::user();
        $notif = $user?->notifications()->where('id', $id)->first();
        if ($notif) {
            $notif->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
        session()->flash('status', __('All notifications marked as read.'));
    }

    public function deleteNotification(string $id): void
    {
        Auth::user()?->notifications()->where('id', $id)->delete();
        session()->flash('status', __('Notification deleted.'));
    }

    public function render()
    {
        $user = Auth::user();

        $query = $user ? $user->notifications() : collect()->toQuery();

        if ($this->filter === 'unread' && $user) {
            $query = $user->unreadNotifications();
        }

        $notifications = $user ? $query->paginate(15) : collect();
        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $totalCount = $user ? $user->notifications()->count() : 0;

        return view('livewire.admin.admin-notifications', [
            'notifications' => $notifications,
            'filter' => $this->filter,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
        ]);
    }
}
