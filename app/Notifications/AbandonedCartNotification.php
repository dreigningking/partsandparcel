<?php

namespace App\Notifications;

use App\Models\Cart;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Cart $cart) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = $this->cart->items()->count();

        return (new MailMessage)
            ->subject('Items waiting in your cart — Parts & Parcel')
            ->greeting("Hello {$notifiable->name},")
            ->line("You left {$itemCount} item(s) in your shopping cart.")
            ->line("Popular items sell quickly on Parts & Parcel. Complete your checkout today with escrow-protected payments.")
            ->action('Return to My Cart', route('cart'))
            ->line('Thank you for choosing Parts & Parcel!');
    }

    public function toDatabase(object $notifiable): array
    {
        $itemCount = $this->cart->items()->count();

        app(FcmService::class)->sendToUser(
            $notifiable,
            'Your Cart is Waiting!',
            "You left {$itemCount} item(s) in your cart. Complete your purchase before items sell out!",
            ['cart_id' => (string) $this->cart->id]
        );

        return [
            'type' => 'abandoned_cart',
            'category' => 'transactions',
            'title' => 'Your Cart is Waiting',
            'message' => "You have {$itemCount} item(s) in your cart waiting for checkout.",
            'cart_id' => $this->cart->id,
            'action_url' => route('cart'),
            'icon' => 'fas fa-shopping-cart',
            'icon_color' => 'text-amber-600 bg-amber-100',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Your Cart is Waiting',
            'message' => 'Complete your order before items sell out.',
            'action_url' => route('cart'),
        ]);
    }
}
