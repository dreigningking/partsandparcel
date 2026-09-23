<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class Notifications extends Component
{
    use WithPagination;

    public string $activeTab = 'all';

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function markAsRead(string $notificationId)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            $notification?->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            session()->flash('message', 'All notifications marked as read.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        if (! $user) {
            return view('livewire.dashboard.notifications', [
                'notifications' => collect(),
                'counts' => ['all' => 0, 'transactions' => 0, 'offers' => 0, 'community' => 0],
            ]);
        }

        $allQuery = $user->notifications();

        $counts = [
            'all' => (clone $allQuery)->count(),
            'transactions' => (clone $allQuery)->where('data->category', 'transactions')->count(),
            'offers' => (clone $allQuery)->where('data->category', 'offers')->count(),
            'community' => (clone $allQuery)->where('data->category', 'community')->count(),
        ];

        $notificationsQuery = $user->notifications()->latest();

        if ($this->activeTab !== 'all') {
            $notificationsQuery->where('data->category', $this->activeTab);
        }

        $notifications = $notificationsQuery->paginate(15);

        return view('livewire.dashboard.notifications', [
            'notifications' => $notifications,
            'counts' => $counts,
        ]);
    }
}
