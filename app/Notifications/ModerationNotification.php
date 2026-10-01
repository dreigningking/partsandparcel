<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModerationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $moderationCount
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Moderation Alert: Pending Items Require Your Attention'))
            ->greeting(__('Hello :name!', ['name' => $notifiable->name]))
            ->line(__('You have :count pending moderation(s) that require your attention.', ['count' => $this->moderationCount]))
            ->action(__('Go to Dashboard'), url(route('admin.moderations', absolute: false)));
    }
}
