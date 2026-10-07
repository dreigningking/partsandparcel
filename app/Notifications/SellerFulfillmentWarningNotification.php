<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerFulfillmentWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public int $remainingHours = 36
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = $this->invoice->invoice_number;

        return (new MailMessage)
            ->subject("Urgent: Action Required on Paid Order #{$ref}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Order #{$ref} was paid by the buyer but has not been marked as shipped or ready for pickup.")
            ->line("Under platform fulfillment rules, if you do not fulfill this order within the next {$this->remainingHours} hours, the order will be automatically cancelled and refunded to the buyer.")
            ->action('Fulfill Order Now', route('invoices.view', ['identifier' => $this->invoice->id]))
            ->line('Please dispatch the parcel or mark ready for pickup as soon as possible.');
    }

    public function toDatabase(object $notifiable): array
    {
        $ref = $this->invoice->invoice_number;

        app(FcmService::class)->sendToUser(
            $notifiable,
            "Urgent: Fulfill Order #{$ref}",
            "Please dispatch order #{$ref} to prevent automatic cancellation.",
            ['invoice_id' => (string) $this->invoice->id]
        );

        return [
            'type' => 'fulfillment_warning',
            'category' => 'orders',
            'title' => "Fulfillment Deadline Warning: Order #{$ref}",
            'message' => "Order #{$ref} needs dispatch within {$this->remainingHours} hours to prevent automatic cancellation.",
            'invoice_id' => $this->invoice->id,
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
            'icon' => 'fas fa-exclamation-triangle',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => "Fulfillment Warning: Order #{$this->invoice->invoice_number}",
            'message' => "Action required on Order #{$this->invoice->invoice_number} within {$this->remainingHours} hours.",
            'action_url' => route('invoices.view', ['identifier' => $this->invoice->id]),
        ]);
    }
}
