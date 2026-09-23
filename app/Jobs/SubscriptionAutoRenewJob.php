<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Revenue;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionAutoRenewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $dueSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('ends_at', [now()->subHours(6), now()->addHours(6)])
            ->with(['user', 'plan'])
            ->get();

        foreach ($dueSubscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;

            if (! $user || ! $plan) continue;

            // In production, charge recurring token from gateway (e.g. Paystack authorization code).
            // For now, record recurring renewal payment and extend validity
            $reference = 'RENEW-' . strtoupper(Str::random(10));

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'reference' => $reference,
                'provider' => 'paystack',
                'status' => 'successful',
                'amount' => $plan->price,
                'currency' => 'NGN',
                'paid_at' => now(),
                'metadata' => [
                    'payment_type' => 'subscription_renewal',
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                ],
            ]);

            // Extend subscription by 1 month
            $subscription->update([
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'status' => 'active',
            ]);

            // Record platform revenue
            Revenue::create([
                'payment_id' => $payment->id,
                'type' => 'subscription',
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ]);

            Log::info("Auto-renewed subscription #{$subscription->id} for user #{$user->id}. Ref: {$reference}");
        }
    }
}
