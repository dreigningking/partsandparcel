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
        public readonly int|string $moderationCount,
        public readonly array $breakdown = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('Moderation Alert: :count Pending Item(s) Require Review', ['count' => $this->moderationCount]))
            ->greeting(__('Hello :name!', ['name' => $notifiable->name]))
            ->line(__('There are currently :count item(s) awaiting moderation review across the platform.', ['count' => $this->moderationCount]));

        if (!empty($this->breakdown)) {
            $listings = $this->breakdown['listings'] ?? 0;
            $discussions = $this->breakdown['discussions'] ?? 0;
            $comments = $this->breakdown['comments'] ?? 0;

            if ($listings > 0) {
                $mail->line(__('• :count Listing(s) pending review', ['count' => $listings]));
            }
            if ($discussions > 0) {
                $mail->line(__('• :count Community Discussion(s) pending review', ['count' => $discussions]));
            }
            if ($comments > 0) {
                $mail->line(__('• :count Blog Post Comment(s) pending review', ['count' => $comments]));
            }
        }

        return $mail
            ->line(__('Please review and approve or reject these items in the admin control center.'))
            ->action(__('Open Moderation Queue'), url(route('admin.moderations', absolute: false)));
    }
}
