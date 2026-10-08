<?php

namespace App\Notifications;

use App\Models\Discussion;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DiscussionEditedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Discussion $discussion
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $discussionTitle = $this->discussion->title;
        $url = route('community.request', ['id' => $this->discussion->id]);
        $recipientName = $notifiable->name ?? 'Community Member';

        return (new MailMessage)
            ->subject("[Parts & Parcel] Update: Request \"{$discussionTitle}\" has been modified")
            ->greeting("Hello {$recipientName},")
            ->line("The requester has updated the specifications or details for the community request you responded to or are watching:")
            ->line("**{$discussionTitle}**")
            ->action('View Updated Request', $url)
            ->line('Review the updated details to see if your proposal or response needs any adjustment.')
            ->salutation('Best regards, The Parts & Parcel Team');
    }

    public function toDatabase(object $notifiable): array
    {
        $discussionTitle = $this->discussion->title;

        try {
            if (class_exists(FcmService::class)) {
                app(FcmService::class)->sendToUser(
                    $notifiable,
                    'Request Specifications Updated',
                    "The request '{$discussionTitle}' was updated by the requester.",
                    ['discussion_id' => (string) $this->discussion->id, 'type' => 'discussion_edited']
                );
            }
        } catch (\Throwable $e) {
            // FCM push failure should not block database notification
        }

        return [
            'type' => 'discussion_edited',
            'category' => 'community',
            'title' => 'Request Specifications Updated',
            'message' => "The request \"{$discussionTitle}\" was updated by the requester.",
            'discussion_id' => $this->discussion->id,
            'action_url' => route('community.request', ['id' => $this->discussion->id]),
            'icon' => 'fas fa-pen-to-square',
            'icon_color' => 'text-amber-600 bg-amber-50',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $discussionTitle = $this->discussion->title;

        return new BroadcastMessage([
            'title' => 'Request Specifications Updated',
            'message' => "The request \"{$discussionTitle}\" was updated by the requester.",
            'discussion_id' => $this->discussion->id,
            'action_url' => route('community.request', ['id' => $this->discussion->id]),
        ]);
    }
}

