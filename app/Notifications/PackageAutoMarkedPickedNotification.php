<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageAutoMarkedPickedNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("Order #{$ref} Marked as Picked Up")
            ->greeting("Hello {$notifiable->name},")
            ->line("Order #{$ref} has been automatically marked as picked up based on the local pickup allowance window.")
            ->line('The inspection and acceptance window is now active.')
            ->action('View Order Status', route('invoices.view', ['identifier' => $this->invoice->id]))
            ->line('If there is any issue with your order, you can report it via the invoice view.');
    }

    public function toDatabase(object $notifiable): array
    {
        $ref = $this->invoice->invoice_number;

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Order #{$ref} Marked as Picked Up",
            "The pickup allowance has elapsed. Package is marked as picked up.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'pickup_auto_marked',
            'category' => 'orders',
            'title' => "Order #{$ref} Marked as Picked Up",
            'message' => "Order #{$ref} was automatically marked as picked up.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
            'icon' => 'fas fa-check-circle',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Order #{$this->invoice->invoice_number} Marked as Picked Up",
            'message' => 'Package has been automatically recorded as picked up.',
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
        ]);
    }
}
