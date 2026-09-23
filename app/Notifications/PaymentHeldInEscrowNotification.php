<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PaymentHeldInEscrowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = number_format((float) $this->invoice->total_amount);

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Escrow Payment Confirmed',
            "Invoice #{$this->invoice->reference} (₦{$amount}) paid. Funds are secured in Escrow. Please prepare the parcel.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'escrow_payment',
            'category' => 'transactions',
            'title' => 'Escrow Payment Confirmed',
            'message' => "Invoice #{$this->invoice->reference} (₦{$amount}) was paid by buyer. Funds are held in Escrow pending delivery.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['id' => $this->invoice->id]),
            'icon' => 'fas fa-shield-alt',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Escrow Payment Confirmed',
            'message' => "Payment of ₦" . number_format((float) $this->invoice->total_amount) . " confirmed in Escrow.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['id' => $this->invoice->id]),
        ]);
    }
}
