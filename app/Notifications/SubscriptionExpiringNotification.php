<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription, public int $daysLeft = 3) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $planName = $this->subscription->plan->name ?? 'Paid Plan';

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Subscription Expiring Soon',
            "Your {$planName} expires in {$this->daysLeft} days. Renew to maintain your daily responses and seller tools.",
            ['subscription_id' => (string) $this->subscription->id]
        );

        return [
            'type' => 'subscription_expiring',
            'category' => 'subscriptions',
            'title' => 'Subscription Expiring Soon',
            'message' => "Your {$planName} will expire in {$this->daysLeft} days. Renew now to retain priority responses and active listing slots.",
            'subscription_id' => $this->subscription->id,
            'action_url' => route('subscriptions'),
            'icon' => 'fas fa-clock',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Subscription Expiring',
            'message' => "Your subscription expires in {$this->daysLeft} days.",
            'action_url' => route('subscriptions'),
        ]);
    }
}
