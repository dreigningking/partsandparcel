<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Notifications\SubscriptionExpiredNotification;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionExpiredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SubscriptionService $subscriptionService): void
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->with(['user', 'plan'])
            ->get();

        $starterPlan = SubscriptionPlan::where('name', 'like', '%Starter%')->first();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);

            $user = $subscription->user;
            if ($user) {
                if ($starterPlan) {
                    $subscriptionService->activateFreeSubscription($user, $starterPlan);
                }
                $user->notify(new SubscriptionExpiredNotification($subscription));
                Log::info("Expired subscription #{$subscription->id} for user #{$user->id} and reverted to free starter plan.");
            }
        }
    }
}
