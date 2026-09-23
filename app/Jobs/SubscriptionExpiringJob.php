<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionExpiringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays(3)])
            ->with(['user', 'plan'])
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            $user = $subscription->user;
            if (! $user) continue;

            $alreadyNotified = $user->notifications()
                ->where('type', SubscriptionExpiringNotification::class)
                ->where('created_at', '>=', now()->subDays(3))
                ->exists();

            if (! $alreadyNotified) {
                $daysLeft = max(1, (int) ceil(now()->diffInDays($subscription->ends_at)));
                $user->notify(new SubscriptionExpiringNotification($subscription, $daysLeft));
                Log::info("Sent expiring notice for subscription #{$subscription->id} to user #{$user->id}");
            }
        }
    }
}
