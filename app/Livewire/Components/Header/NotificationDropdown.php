<?php

namespace App\Livewire\Components\Header;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public int $unreadCount = 0;
    public array $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function getListeners(): array
    {
        $userId = Auth::id();
        $listeners = [
            'notifications-updated' => 'loadNotifications',
            'notification-received' => 'handleNewNotification',
        ];

        if ($userId) {
            $listeners["echo-private:user.{$userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated"] = 'handleNewNotification';
        }

        return $listeners;
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->unreadCount = 0;
            $this->notifications = [];
            return;
        }

        $this->unreadCount = $user->unreadNotifications()->count();
        $this->notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'icon' => $n->data['icon'] ?? 'fas fa-bell',
                'action_url' => $n->data['action_url'] ?? route('notifications'),
                'is_unread' => $n->unread(),
                'time_ago' => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function handleNewNotification($payload = null): void
    {
        $this->unreadCount++;

        if (is_array($payload)) {
            $newRow = [
                'id' => $payload['id'] ?? (string) time(),
                'title' => $payload['title'] ?? 'New Notification',
                'message' => $payload['message'] ?? '',
                'icon' => $payload['icon'] ?? 'fas fa-bell',
                'action_url' => $payload['action_url'] ?? route('notifications'),
                'is_unread' => true,
                'time_ago' => 'Just now',
            ];
            array_unshift($this->notifications, $newRow);
            $this->notifications = array_slice($this->notifications, 0, 5);
        } else {
            $this->loadNotifications();
        }
    }

    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.components.header.notification-dropdown');
    }
}
