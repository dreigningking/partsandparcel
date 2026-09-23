<?php

namespace App\Notifications;

use App\Models\Cart;
use App\Services\Notification\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Cart $cart) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
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
