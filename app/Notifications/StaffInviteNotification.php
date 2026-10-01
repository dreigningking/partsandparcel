<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffInviteNotification extends Notification implements ShouldQueue
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
            ->subject(__('You have been invited as staff'))
            ->greeting(__('Hello :name!', ['name' => $this->user->name]))
            ->line(__('You have been invited to join the admin staff.'))
            ->line(__('Email: :email', ['email' => $this->user->email]))
            ->line(__('Password: :password', ['password' => $this->password]))
            ->action(__('Login'), url(route('login', absolute: false)))
            ->line(__('Please change your password after first login.'));
    }
}
