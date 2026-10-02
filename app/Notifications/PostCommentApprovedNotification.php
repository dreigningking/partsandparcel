<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PostCommentApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Post $post,
        public readonly PostComment $comment
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof User && $notifiable->notificationPreference('email')) {
            $channels[] = 'mail';
        }

        // Also trigger push notification if user prefers push
        if ($notifiable instanceof User && $notifiable->notificationPreference('push')) {
            try {
                if ($notifiable->deviceTokens()->exists()) {
                    app(FcmService::class)->sendToUser(
                        $notifiable,
                        "New Comment on " . Str::limit($this->post->title, 35),
                        "{$this->comment->name}: " . Str::limit($this->comment->comment, 60),
                        [
                            'url' => route('blog.show', $this->post->slug),
                            'type' => 'post_comment',
                        ]
                    );
                }
            } catch (\Throwable $e) {
                // Ignore push exception to avoid blocking database/mail delivery
            }
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $notifiable->name ?? 'Valued Member';
        $postUrl = route('blog.show', $this->post->slug);

        return (new MailMessage)
            ->subject("New comment on: {$this->post->title} — Parts & Parcel")
            ->greeting("Hello {$userName}!")
            ->line("A new comment has been approved on a post you are watching:")
            ->line("**{$this->post->title}**")
            ->line("**{$this->comment->name}** wrote:")
            ->line('"' . Str::limit($this->comment->comment, 250) . '"')
            ->action('View Post & Conversation', $postUrl)
            ->line('You are receiving this update because you are subscribed to notifications for this article.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fas fa-comment-dots',
            'title' => 'New Comment on ' . Str::limit($this->post->title, 40),
            'message' => "{$this->comment->name}: " . Str::limit($this->comment->comment, 80),
            'action_url' => route('blog.show', $this->post->slug),
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
        ];
    }
}
