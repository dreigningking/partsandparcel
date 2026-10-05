<?php

namespace App\Notifications;

use App\Models\Discussion;
use App\Models\Response;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DiscussionResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Discussion $discussion,
        public Response $response
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $responderName = $this->response->user?->name ?? 'A community member';
        $discussionTitle = $this->discussion->title;

        try {
            if (class_exists(\App\Services\Notification\FcmService::class)) {
                app(\App\Services\Notification\FcmService::class)->sendToUser(
                    $notifiable,
                    'New Response on Discussion',
                    "{$responderName} replied to '{$discussionTitle}'",
                    ['discussion_id' => (string) $this->discussion->id, 'type' => 'discussion_response']
                );
            }
        } catch (\Throwable $e) {
            // FCM push failure should not block in-app / database notification
        }

        return [
            'type' => 'discussion_response',
            'category' => 'discussions',
            'title' => 'New Response on Discussion',
            'message' => "{$responderName} replied to: \"{$discussionTitle}\"",
            'discussion_id' => $this->discussion->id,
            'response_id' => $this->response->id,
            'action_url' => route('community.request', ['id' => $this->discussion->id]),
            'icon' => 'fas fa-comments',
            'icon_color' => 'text-pp-600 bg-pp-50',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $responderName = $this->response->user?->name ?? 'A community member';

        return new BroadcastMessage([
            'title' => 'New Response on Discussion',
            'message' => "{$responderName} replied to your watched discussion.",
            'discussion_id' => $this->discussion->id,
            'action_url' => route('community.request', ['id' => $this->discussion->id]),
        ]);
    }
}
