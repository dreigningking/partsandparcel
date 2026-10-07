<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDelayedShipmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = $this->invoice->invoice_number;
        $shippedAt = $this->invoice->shipped_at?->toDayDateTimeString() ?? 'N/A';
        $sellerName = $this->invoice->seller?->name ?? 'Seller';
        $buyerName = $this->invoice->buyer?->name ?? 'Buyer';

        return (new MailMessage)
            ->subject("[Admin Alert] Shipment Delay Follow-Up: Order #{$ref}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Order #{$ref} was dispatched on {$shippedAt} but has exceeded the expected delivery window without delivery confirmation.")
            ->line("Seller: {$sellerName} | Buyer: {$buyerName}")
            ->line('Escrow status has been flagged for administrative review. Please follow up with the logistics provider.')
            ->action('View Invoice in Admin', route('admin.invoices.show', ['invoice' => $this->invoice->id]))
            ->line('Parts & Parcel Escrow Monitoring.');
    }

    public function toDatabase(object $notifiable): array
    {
        $ref = $this->invoice->invoice_number;

        return [
            'type' => 'admin_shipment_delay',
            'category' => 'logistics',
            'title' => "Shipment Delay: Order #{$ref}",
            'message' => "Order #{$ref} dispatched on {$this->invoice->shipped_at?->format('M d')} has exceeded expected transit timeframe.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('admin.invoices.show', ['invoice' => $this->invoice->id]),
            'icon' => 'fas fa-shipping-fast',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }
}
