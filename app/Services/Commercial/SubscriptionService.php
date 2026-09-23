<?php

namespace App\Services\Commercial;

use App\Models\Listing;
use App\Models\Payment;
use App\Models\Response;
use App\Models\Revenue;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Support\Str;

class SubscriptionService
{
    public function __construct(
        protected PaystackService $paystack,
        protected FlutterwaveService $flutterwave
    ) {}

    /**
     * Dynamically compute current resource usage and plan limits on the fly.
     * No mutable decrement counters are stored in the database.
     */
    public function getUsageStats(User $user): array
    {
        /** @var Subscription|null $activeSub */
        $activeSub = $user->activeSubscription()->with('plan')->first();

        /** @var SubscriptionPlan|null $plan */
        $plan = $activeSub?->plan ?? SubscriptionPlan::where('name', 'like', '%Starter%')->first();

        $dailyLimit = $plan?->features['daily_response_limit'] ?? ($plan?->response_limit ?? 1);
        $listingLimit = $plan?->features['listing_limit'] ?? 10;
        $hasDisassemblyTool = (bool) ($plan?->features['disassembly_tool'] ?? false);

        // Calculate responses used today dynamically on the fly
        $todayResponsesUsed = Response::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();

        // Calculate active listings dynamically on the fly
        $activeListingsCount = Listing::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        $daysLeft = null;
        if ($activeSub && $activeSub->ends_at) {
            $daysLeft = max(0, (int) ceil(now()->diffInDays($activeSub->ends_at, false)));
        }

        return [
            'plan_id' => $plan?->id,
            'plan_name' => $plan?->name ?? 'Starter Free',
            'is_paid' => $activeSub !== null,
            'is_active' => $activeSub !== null ? $activeSub->isActive() : true,
            'subscription' => $activeSub,
            'daily_response_limit' => (int) $dailyLimit,
            'daily_responses_used' => (int) $todayResponsesUsed,
            'daily_responses_remaining' => max(0, (int) $dailyLimit - $todayResponsesUsed),
            'can_respond' => $todayResponsesUsed < $dailyLimit,
            'listing_limit' => (int) $listingLimit,
            'listings_used' => (int) $activeListingsCount,
            'listings_remaining' => max(0, (int) $listingLimit - $activeListingsCount),
            'can_create_listing' => $activeListingsCount < $listingLimit,
            'has_disassembly_tool' => $hasDisassemblyTool,
            'starts_at' => $activeSub?->starts_at,
            'ends_at' => $activeSub?->ends_at,
            'days_left' => $daysLeft,
        ];
    }

    /**
     * Check if a user can submit a community response today based on dynamic quota.
     */
    public function canSubmitResponse(User $user): bool
    {
        return $this->getUsageStats($user)['can_respond'];
    }

    /**
     * Check if a user can create another listing based on dynamic quota.
     */
    public function canCreateListing(User $user): bool
    {
        return $this->getUsageStats($user)['can_create_listing'];
    }

    /**
     * Initialize subscription checkout (Paystack or Flutterwave) or activate free tier directly.
     */
    public function initializeSubscriptionCheckout(User $user, int $planId, string $provider = 'paystack'): array
    {
        $plan = SubscriptionPlan::findOrFail($planId);

        // If plan is Free, activate immediately without checkout gateway
        if ((float) $plan->price <= 0.0) {
            $subscription = $this->activateFreeSubscription($user, $plan);
            return [
                'status' => 'success',
                'is_free' => true,
                'subscription' => $subscription,
                'redirect_url' => route('subscriptions'),
            ];
        }

        $reference = 'SUB-' . strtoupper(Str::random(10));

        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'provider' => $provider,
            'status' => 'pending',
            'amount' => $plan->price,
            'currency' => 'NGN',
            'metadata' => [
                'payment_type' => 'subscription',
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ],
        ]);

        $callbackUrl = route('payment.callback', ['reference' => $reference]);

        if ($provider === 'flutterwave') {
            $response = $this->flutterwave->initialize($payment, $callbackUrl);
            $authorizationUrl = $response['link'] ?? $response['authorization_url'] ?? $callbackUrl;
        } else {
            $response = $this->paystack->initialize($payment, $callbackUrl);
            $authorizationUrl = $response['authorization_url'] ?? $callbackUrl;
        }

        return [
            'status' => 'success',
            'is_free' => false,
            'payment' => $payment,
            'reference' => $reference,
            'authorization_url' => $authorizationUrl,
        ];
    }

    /**
     * Activate paid subscription upon successful payment confirmation.
     */
    public function activateSubscription(Payment $payment): Subscription
    {
        $planId = $payment->metadata['plan_id'] ?? null;
        $plan = SubscriptionPlan::findOrFail($planId);

        // Cancel previous active subscriptions
        Subscription::where('user_id', $payment->user_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $startsAt = now();
        $endsAt = now()->addMonth();

        $subscription = Subscription::create([
            'user_id' => $payment->user_id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'response_limit' => $plan->response_limit,
        ]);

        $payment->update([
            'subscription_id' => $subscription->id,
            'status' => 'successful',
            'paid_at' => now(),
        ]);

        // Record platform revenue
        Revenue::create([
            'payment_id' => $payment->id,
            'type' => 'subscription',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ]);

        return $subscription;
    }

    /**
     * Activate or revert user to Starter Free plan.
     */
    public function activateFreeSubscription(User $user, SubscriptionPlan $plan): Subscription
    {
        // Cancel active subscriptions
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYears(10), // Permanent free tier
            'response_limit' => $plan->response_limit,
        ]);
    }
}
