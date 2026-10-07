<?php

namespace App\Notifications;

use App\Models\ReturnRecord;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnAutoAcceptedNotification extends Notification implements ShouldQueue
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
            ->subject("Returned Package Auto-Accepted: Order #{$invoiceRef}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The returned package for Order #{$invoiceRef} has completed the vendor inspection window without contest.")
            ->line('The return has been automatically accepted. Resolution processing (refund/replacement) is proceeding immediately.')
            ->action('View Return Status', route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]))
            ->line('Parts & Parcel Post-Sale Operations.');
    }

    public function toDatabase(object $notifiable): array
    {
        $invoiceRef = $this->returnRecord->invoice?->invoice_number ?? "INV-{$this->returnRecord->invoice_id}";

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Return Auto-Accepted: Order #{$invoiceRef}",
            "Return inspection window elapsed. Return accepted.",
            ['invoice_id' => (string) $this->returnRecord->invoice_id]
        );

        return [
            'type' => 'return_auto_accepted',
            'category' => 'returns',
            'title' => "Return Auto-Accepted: Order #{$invoiceRef}",
            'message' => "Returned goods inspection window elapsed. Return accepted and resolution advancing.",
            'invoice_id' => $this->returnRecord->invoice_id,
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
            'icon' => 'fas fa-clipboard-check',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Return Auto-Accepted: Order #{$this->returnRecord->invoice?->invoice_number}",
            'message' => 'Return accepted. Resolution in progress.',
            'action_url' => route('invoices.view', ['identifier' => $this->returnRecord->invoice_id]),
        ]);
    }
}
