<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Offer;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OfferAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offer $offer, public ?Invoice $invoice = null) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $counterparty = $notifiable->id === $this->offer->sender_id
            ? ($this->offer->recipient->name ?? 'Buyer')
            : ($this->offer->sender->name ?? 'Seller');

        $invoiceRef = $this->invoice ? $this->invoice->reference : ('INV-' . $this->offer->id);

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Offer Accepted!',
            "{$counterparty} accepted your offer! Invoice #{$invoiceRef} has been generated.",
            ['offer_id' => (string) $this->offer->id, 'invoice_id' => (string) ($this->invoice?->id ?? '')]
        );

        return [
            'type' => 'offer_accepted',
            'category' => 'offers',
            'title' => 'Offer Accepted',
            'message' => "{$counterparty} accepted your offer! Invoice #{$invoiceRef} has been generated.",
            'offer_id' => $this->offer->id,
            'invoice_id' => $this->invoice?->id,
            'action_url' => route('invoices.view', ['id' => $this->invoice?->id ?? $this->offer->id]),
            'icon' => 'fas fa-check-circle',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Offer Accepted!',
            'message' => 'Your offer was accepted and an invoice generated.',
            'offer_id' => $this->offer->id,
            'action_url' => route('invoices.view', ['id' => $this->invoice?->id ?? $this->offer->id]),
        ]);
    }
}
