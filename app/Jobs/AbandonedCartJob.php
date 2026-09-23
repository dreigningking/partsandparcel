<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Notifications\AbandonedCartNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AbandonedCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $abandonedCarts = Cart::where('updated_at', '<=', now()->subHours(24))
            ->has('items')
            ->with(['buyer', 'items'])
            ->get();

        foreach ($abandonedCarts as $cart) {
            $buyer = $cart->buyer;
            if (! $buyer) continue;

            // Ensure buyer hasn't received an abandoned cart notification in the last 48 hours
            $alreadyNotified = $buyer->notifications()
                ->where('type', AbandonedCartNotification::class)
                ->where('created_at', '>=', now()->subHours(48))
                ->exists();

            if (! $alreadyNotified) {
                $buyer->notify(new AbandonedCartNotification($cart));
                Log::info("Sent abandoned cart notification to buyer #{$buyer->id} for cart #{$cart->id}");
            }
        }
    }
}
