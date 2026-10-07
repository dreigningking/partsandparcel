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

class ListingLowStockNotification extends Notification implements ShouldQueue
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
        $title = 'Wishlist Item Low Stock';
        $message = "Hurry! Only {$this->availableStock} left of \"{$itemName}\" in your wishlist.";

        try {
            if (class_exists(FcmService::class)) {
                app(FcmService::class)->sendToUser(
                    $notifiable,
                    $title,
                    $message,
                    [
                        'listing_id' => (string) $this->listing->id,
                        'type' => 'listing_low_stock',
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Push notification failure should not block database notification
        }

        return [
            'type' => 'listing_low_stock',
            'category' => 'wishlist',
            'title' => $title,
            'message' => $message,
            'listing_id' => $this->listing->id,
            'available_stock' => $this->availableStock,
            'action_url' => route('listing-details', $this->listing->slug ?: $this->listing->id),
            'icon' => 'fas fa-hourglass-half',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");

        return new BroadcastMessage([
            'id' => (string) Str::uuid(),
            'title' => 'Wishlist Item Low Stock',
            'message' => "Only {$this->availableStock} left of \"{$itemName}\" in your wishlist.",
            'listing_id' => $this->listing->id,
            'action_url' => route('listing-details', $this->listing->slug ?: $this->listing->id),
            'icon' => 'fas fa-hourglass-half',
        ]);
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
