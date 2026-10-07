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

class ListingSoldOutNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public string $reason = 'sold_out' // 'sold_out', 'unavailable', 'deleted'
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
        $isSoldOut = ($this->reason === 'sold_out');

        $title = $isSoldOut ? 'Wishlist Item Sold Out' : 'Wishlist Item Unavailable';
        $message = $isSoldOut
            ? "\"{$itemName}\" from your wishlist is now sold out."
            : "\"{$itemName}\" from your wishlist is currently unavailable.";

        try {
            if (class_exists(FcmService::class)) {
                app(FcmService::class)->sendToUser(
                    $notifiable,
                    $title,
                    $message,
                    [
                        'listing_id' => (string) $this->listing->id,
                        'type' => 'listing_sold_out',
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Push notification failure should not block database notification
        }

        return [
            'type' => 'listing_sold_out',
            'category' => 'wishlist',
            'title' => $title,
            'message' => $message,
            'listing_id' => $this->listing->id,
            'action_url' => route('wishlists'),
            'icon' => 'fas fa-exclamation-circle',
            'icon_color' => 'text-rose-600 bg-rose-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");
        $isSoldOut = ($this->reason === 'sold_out');

        return new BroadcastMessage([
            'id' => (string) Str::uuid(),
            'title' => $isSoldOut ? 'Wishlist Item Sold Out' : 'Wishlist Item Unavailable',
            'message' => "\"{$itemName}\" in your wishlist is now sold out.",
            'listing_id' => $this->listing->id,
            'action_url' => route('wishlists'),
            'icon' => 'fas fa-exclamation-circle',
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemName = $this->listing->item?->name ?? ($this->listing->title ?? "Listing #{$this->listing->id}");
        $isSoldOut = ($this->reason === 'sold_out');

        $subject = $isSoldOut
            ? __('Wishlist Notice: ":item" is now sold out', ['item' => $itemName])
            : __('Wishlist Notice: ":item" is no longer available', ['item' => $itemName]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('An item in your wishlist has run out of available stock or is no longer listed:'))
            ->line(__('":item" (Listed Price: ₦:price)', [
                'item' => $itemName,
                'price' => number_format((float) $this->listing->price, 2),
            ]))
            ->line(__('We will notify you automatically when this item is restocked by the seller.'))
            ->action(__('View Wishlist'), route('wishlists'))
            ->line(__('Thank you for using Parts & Parcel.'));
    }
}
