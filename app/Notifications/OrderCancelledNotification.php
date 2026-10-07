<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\User;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public ?User $canceller = null,
        public bool $isAutoCancelled = false,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    protected function getCancellationSourceText(): string
    {
        if ($this->isAutoCancelled) {
            return 'Automatically cancelled by system (fulfillment timeout)';
        }

        if ($this->canceller) {
            $role = ($this->canceller->id === $this->invoice->buyer_id) ? 'Buyer' : 'Seller';
            return "Cancelled by {$role} ({$this->canceller->name})";
        }

        return 'Order cancelled';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sourceText = $this->getCancellationSourceText();
        $invoiceRef = $this->invoice->invoice_number;

        $mail = (new MailMessage)
            ->subject("Order #{$invoiceRef} Has Been Cancelled")
            ->greeting("Hello {$notifiable->name},")
            ->line("Order #{$invoiceRef} has been cancelled.")
            ->line("Cancellation status: {$sourceText}.");

        if ($this->reason) {
            $mail->line("Reason: {$this->reason}");
        }

        if ($this->invoice->isPlatformEscrow() && $this->invoice->paid_at) {
            $mail->line('Escrow protection notice: A full refund has been initiated back to the buyer payment method.');
        }

        return $mail->action('View Invoice Details', route('invoices.view', ['identifier' => $this->invoice->id]))
            ->line('Thank you for using Parts & Parcel.');
    }

    public function toDatabase(object $notifiable): array
    {
        $sourceText = $this->getCancellationSourceText();
        $invoiceRef = $this->invoice->invoice_number;

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Order #{$invoiceRef} Cancelled",
            "Order #{$invoiceRef} was {$sourceText}.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'order_cancelled',
            'category' => 'transactions',
            'title' => "Order #{$invoiceRef} Cancelled",
            'message' => "Order #{$invoiceRef} was {$sourceText}." . ($this->reason ? " Reason: {$this->reason}" : ''),
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
            'icon' => 'fas fa-ban',
            'icon_color' => 'text-rose-600 bg-rose-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Order #{$this->invoice->invoice_number} Cancelled",
            'message' => $this->getCancellationSourceText(),
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
        ]);
    }
}
