<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $planName = $this->subscription->plan->name ?? 'Paid Plan';

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Subscription Expired',
            "Your {$planName} subscription has expired. Your account has been reverted to the Starter Free tier.",
            ['subscription_id' => (string) $this->subscription->id]
        );

        return [
            'type' => 'subscription_expired',
            'category' => 'subscriptions',
            'title' => 'Subscription Expired',
            'message' => "Your {$planName} has lapsed. Your account has been switched to Starter Free (1 response/day, 10 listings).",
            'subscription_id' => $this->subscription->id,
            'action_url' => route('subscription-plans'),
            'icon' => 'fas fa-exclamation-triangle',
            'icon_color' => 'text-rose-600 bg-rose-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Subscription Expired',
            'message' => 'Your subscription has expired and was reverted to Starter Free.',
            'action_url' => route('subscription-plans'),
        ]);
    }
}
