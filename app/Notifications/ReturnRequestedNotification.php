<?php

namespace App\Notifications;

use App\Models\ReturnRecord;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ReturnRecord $returnRecord
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoiceRef = $this->returnRecord->invoice?->invoice_number ?? "INV-{$this->returnRecord->invoice_id}";
        $hours = (int) \App\Models\Setting::getValue('order_rejected_to_returned_hours', 72);

        return (new MailMessage)
            ->subject("Return Requested for Order #{$invoiceRef}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The seller has accepted your reported issue and requested the return of the item.")
            ->line("Please dispatch the return package and update the tracking details within {$hours} hours.")
            ->line("Important: If the return package is not marked shipped within {$hours} hours, the return request will expire and the transaction issue will be resolved in seller favor.")
            ->action('View Return Instructions', route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]))
            ->line('Thank you for your cooperation.');
    }

    public function toDatabase(object $notifiable): array
    {
        $invoiceRef = $this->returnRecord->invoice?->invoice_number ?? "INV-{$this->returnRecord->invoice_id}";
        $hours = (int) \App\Models\Setting::getValue('order_rejected_to_returned_hours', 72);

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Return Requested for #{$invoiceRef}",
            "Please dispatch the return package within {$hours} hours.",
            ['invoice_id' => (string) $this->returnRecord->invoice_id]
        );

        return [
            'type' => 'return_requested',
            'category' => 'returns',
            'title' => "Return Requested: Order #{$invoiceRef}",
            'message' => "The seller requested return of the item. Please dispatch within {$hours} hours.",
            'invoice_id' => $this->returnRecord->invoice_id,
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
            'icon' => 'fas fa-undo-alt',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Return Requested: Order #{$this->returnRecord->invoice?->invoice_number}",
            'message' => 'Please dispatch return package within allowed timeframe.',
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
        ]);
    }
}
