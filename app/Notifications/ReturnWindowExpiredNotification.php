<?php

namespace App\Notifications;

use App\Models\ReturnRecord;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnWindowExpiredNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("Return Window Expired: Order #{$invoiceRef}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The allocated timeframe to dispatch the return package for Order #{$invoiceRef} has elapsed.")
            ->line('As no return shipment was recorded, the return request has been closed and the transaction has been marked resolved.')
            ->action('View Order Details', route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]))
            ->line('Parts & Parcel Post-Sale Operations.');
    }

    public function toDatabase(object $notifiable): array
    {
        $invoiceRef = $this->returnRecord->invoice?->invoice_number ?? "INV-{$this->returnRecord->invoice_id}";

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Return Expired: Order #{$invoiceRef}",
            "Return shipment window expired. Issue marked resolved.",
            ['invoice_id' => (string) $this->returnRecord->invoice_id]
        );

        return [
            'type' => 'return_expired',
            'category' => 'returns',
            'title' => "Return Window Expired: Order #{$invoiceRef}",
            'message' => "The return package dispatch timeframe expired. Issue has been resolved.",
            'invoice_id' => $this->returnRecord->invoice_id,
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
            'icon' => 'fas fa-calendar-times',
            'icon_color' => 'text-slate-600 bg-slate-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Return Window Expired: Order #{$this->returnRecord->invoice?->invoice_number}",
            'message' => 'Return request expired without shipment. Issue resolved.',
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
        ]);
    }
}
