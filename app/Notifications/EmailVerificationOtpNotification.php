<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $otp,
        public readonly int $expiryMinutes = 10
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Verify Your Email Address - Parts & Parcel'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? 'there']))
            ->line(__('Thank you for registering on Parts & Parcel. Please use the following 6-digit verification code to confirm your email address:'))
            ->line(new \Illuminate\Support\HtmlString(
                '<div style="text-align:center; margin: 25px 0;">' .
                '<span style="display:inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #5140c8; background: #f5f3ff; border: 2px dashed #d9d2ff; padding: 12px 24px; border-radius: 12px; font-family: monospace;">' .
                $this->otp .
                '</span></div>'
            ))
            ->line(__('This verification code will expire in :minutes minutes.', ['minutes' => $this->expiryMinutes]))
            ->line(__('If you did not create an account or request this verification code, no further action is required.'))
            ->salutation(__('Best regards,<br>The Parts & Parcel Team'));
    }
}
