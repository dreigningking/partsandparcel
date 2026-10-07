<?php

namespace App\Notifications;

use App\Models\Replacement;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplacementAutoRefundedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Replacement $replacement
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoiceRef = $this->replacement->invoice?->invoice_number ?? "INV-{$this->replacement->invoice_id}";

        return (new MailMessage)
            ->subject("Replacement Dispatched Timeout — Auto-Refunded: Order #{$invoiceRef}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The replacement unit for Order #{$invoiceRef} was not dispatched within the required timeframe.")
            ->line('As a result, the replacement request was cancelled and a full platform escrow refund has been processed back to the buyer.')
            ->action('View Refund Details', route('invoices.view', ['identifier' => $this->replacement->invoice_id]))
            ->line('Parts & Parcel Escrow Protection Guarantee.');
    }

    public function toDatabase(object $notifiable): array
    {
        $invoiceRef = $this->replacement->invoice?->invoice_number ?? "INV-{$this->replacement->invoice_id}";

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Replacement Auto-Refunded: Order #{$invoiceRef}",
            "Replacement dispatch timeout elapsed. Full refund queued.",
            ['invoice_id' => (string) $this->replacement->invoice_id]
        );

        return [
            'type' => 'replacement_auto_refunded',
            'category' => 'refunds',
            'title' => "Replacement Dispatched Timeout: Order #{$invoiceRef}",
            'message' => "Replacement unit was not dispatched in time. Automatic buyer refund queued.",
            'invoice_id' => $this->replacement->invoice_id,
            'action_url' => route('invoices.view', ['identifier' => $this->replacement->invoice_id]),
            'icon' => 'fas fa-undo-alt',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Replacement Timeout: Order #{$this->replacement->invoice?->invoice_number}",
            'message' => 'Replacement cancelled and full refund processed.',
            'action_url' => route('invoices.view', ['identifier' => $this->replacement->invoice_id]),
        ]);
    }
}
