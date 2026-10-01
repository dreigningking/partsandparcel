<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostSubscriptionWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly string $password
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Thanks for subscribing to our blog'))
            ->greeting(__('Hello :name!', ['name' => $this->user->name]))
            ->line(__('Thank you for subscribing to our blog updates.'))
            ->line(__('You will receive new post and comment updates as they become available.'))
            ->line(__('If you want to sign in to manage your subscriptions, use the credentials below.'))
            ->line(__('Email: :email', ['email' => $this->user->email]))
            ->line(__('Password: :password', ['password' => $this->password]))
            ->action(__('Login'), url(route('login', absolute: false)))
            ->line(__('Please change your password after first login.'));
    }
}
