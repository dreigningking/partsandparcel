<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingRestockedNotification extends Notification implements ShouldQueue
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
            ->subject(__('Back in Stock: ":item" in your wishlist is now available!', ['item' => $itemName]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Good news! An item in your wishlist has just been restocked by the seller.'))
            ->line(__('":item" is back with :count unit(s) available.', ['item' => $itemName, 'count' => $this->availableStock]))
            ->line(__('Listed Price: ₦:price', ['price' => number_format((float) $this->listing->price, 2)]))
            ->action(__('View Listing Details'), route('listing-details', $this->listing->slug ?: $this->listing->id))
            ->line(__('Thank you for using Parts & Parcel.'));
    }
}
