<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\Setting;
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
        $reactionHours = max(1, (int) Setting::getValue('abandoned_cart_reaction_hours', 24));
        $gapDays = max(1, (int) Setting::getValue('cart_reaction_gap_days', 30));

        $abandonedCarts = Cart::where('updated_at', '<=', now()->subHours($reactionHours))
            ->has('items')
            ->with(['buyer', 'items'])
            ->get();

        foreach ($abandonedCarts as $cart) {
            $buyer = $cart->buyer;
            if (! $buyer) {
                continue;
            }

            // Ensure buyer hasn't received an abandoned cart notification within the configured gap days
            $canSendEmail = is_null($buyer->last_abandoned_cart_email_at)
                || $buyer->last_abandoned_cart_email_at->lessThanOrEqualTo(now()->subDays($gapDays));

            if ($canSendEmail) {
                $buyer->notify(new AbandonedCartNotification($cart));
                $buyer->updateQuietly([
                    'last_abandoned_cart_email_at' => now(),
                ]);
                Log::info("Sent abandoned cart notification to buyer #{$buyer->id} for cart #{$cart->id}");
            }
        }
    }
}
