<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\User;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ListingRestockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public int $availableStock
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if ($notifiable instanceof User && $notifiable->notificationPreference('email')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");
        $title = 'Wishlist Item Restocked!';
        $message = "\"{$itemName}\" from your wishlist is back in stock ({$this->availableStock} available)!";

        try {
            if (class_exists(FcmService::class)) {
                app(FcmService::class)->sendToUser(
                    $notifiable,
                    $title,
                    $message,
                    [
                        'listing_id' => (string) $this->listing->id,
                        'type' => 'listing_restocked',
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Push notification failure should not block database notification
        }

        return [
            'type' => 'listing_restocked',
            'category' => 'wishlist',
            'title' => $title,
            'message' => $message,
            'listing_id' => $this->listing->id,
            'available_stock' => $this->availableStock,
            'action_url' => route('listing-details', $this->listing->slug ?: $this->listing->id),
            'icon' => 'fas fa-box-open',
            'icon_color' => 'text-emerald-600 bg-emerald-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");

        return new BroadcastMessage([
            'id' => (string) Str::uuid(),
            'title' => 'Wishlist Item Restocked!',
            'message' => "\"{$itemName}\" from your wishlist is back in stock!",
            'listing_id' => $this->listing->id,
            'action_url' => route('listing-details', $this->listing->slug ?: $this->listing->id),
            'icon' => 'fas fa-box-open',
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");

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
