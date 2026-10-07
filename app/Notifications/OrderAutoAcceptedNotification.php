<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderAutoAcceptedNotification extends Notification implements ShouldQueue
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
            ->subject("Order #{$ref} Successfully Auto-Accepted")
            ->greeting("Hello {$notifiable->name},")
            ->line("Order #{$ref} inspection window has elapsed without reported issues.")
            ->line('The transaction has been automatically marked as accepted, and seller settlement payout is scheduled.')
            ->action('View Order Summary', route('invoices.view', ['identifier' => $this->invoice->id]))
            ->line('Thank you for choosing Parts & Parcel!');
    }

    public function toDatabase(object $notifiable): array
    {
        $ref = $this->invoice->invoice_number;

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Order #{$ref} Auto-Accepted",
            "Inspection window completed. Order #{$ref} is finalized.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'order_auto_accepted',
            'category' => 'transactions',
            'title' => "Order #{$ref} Auto-Accepted",
            'message' => "Order #{$ref} completed inspection window and has been finalized.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
            'icon' => 'fas fa-handshake',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Order #{$this->invoice->invoice_number} Accepted",
            'message' => 'Order completed inspection window and is now accepted.',
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
        ]);
    }
}
