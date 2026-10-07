<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageReadyForPickupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = $this->invoice->invoice_number;
        $allowanceHours = (int) \App\Models\Setting::getValue('order_pickup_allowance_hours', 48);

        return (new MailMessage)
            ->subject("Your Order #{$ref} is Ready for Pickup!")
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! The seller has marked your order #{$ref} as ready for pickup.")
            ->line("Please collect your package within the next {$allowanceHours} hours.")
            ->action('View Pickup Details', route('invoices.view', ['identifier' => $this->invoice->id]))
            ->line('Once collected, remember to inspect your items and confirm reception.');
    }

    public function toDatabase(object $notifiable): array
    {
        $ref = $this->invoice->invoice_number;

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Package Ready for Pickup: Order #{$ref}",
            "Your package is ready for collection from the seller.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'pickup_ready',
            'category' => 'orders',
            'title' => "Order #{$ref} is Ready for Pickup",
            'message' => "The seller has prepared your package. Please collect it within the allowance window.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
            'icon' => 'fas fa-box-open',
            'icon_color' => 'text-blue-600 bg-blue-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Order #{$this->invoice->invoice_number} Ready for Pickup",
            'message' => 'Your package is ready for collection.',
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
        ]);
    }
}
