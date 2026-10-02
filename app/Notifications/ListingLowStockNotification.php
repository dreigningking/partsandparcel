<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingLowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public int $availableStock
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemName = $this->listing->item?->name ?? "Listing #{$this->listing->id}";

        return (new MailMessage)
            ->subject(__('Hurry! Only :count left of ":item" in your wishlist', ['count' => $this->availableStock, 'item' => $itemName]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('An item you saved to your wishlist is running low on stock!'))
            ->line(__('":item" has only :count unit(s) remaining.', ['item' => $itemName, 'count' => $this->availableStock]))
            ->line(__('Listed Price: ₦:price', ['price' => number_format((float) $this->listing->price, 2)]))
            ->action(__('Buy or Make Offer Now'), route('listing-details', $this->listing->slug ?: $this->listing->id))
            ->line(__('Act fast before it sells out!'));
    }
}
