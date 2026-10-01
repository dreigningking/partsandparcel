<?php

namespace App\Services\Commercial;

use App\Models\Discussion;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\PromoCode;
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

        // 1. Daily community requests limit and usage
        $dailyRequestLimit = (int) ($plan?->request_limit ?: ($plan?->features['daily_request_limit'] ?? 1));
        $todayRequestsUsed = Discussion::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
        $dailyRequestsRemaining = max(0, $dailyRequestLimit - $todayRequestsUsed);
        $canCreateRequest = $todayRequestsUsed < $dailyRequestLimit;

        // 2. Daily community responses limit and usage
        $dailyResponseLimit = (int) ($plan?->response_limit ?: ($plan?->features['daily_response_limit'] ?? 1));
        $todayResponsesUsed = Response::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
        $dailyResponsesRemaining = max(0, $dailyResponseLimit - $todayResponsesUsed);
        $canRespond = $todayResponsesUsed < $dailyResponseLimit;

        // 3. Total listings altogether limit and usage
        $listingLimit = (int) ($plan?->listing_limit ?: ($plan?->features['listing_limit'] ?? 10));
        $totalListingsCount = Listing::where('user_id', $user->id)->count();
        $listingsRemaining = max(0, $listingLimit - $totalListingsCount);
        $canCreateListing = $totalListingsCount < $listingLimit;

        $daysLeft = null;
        if ($activeSub && $activeSub->ends_at) {
            $daysLeft = max(0, (int) ceil(now()->diffInDays($activeSub->ends_at, false)));
        }

        return [
            'plan_id' => $plan?->id,
            'plan_name' => $plan?->name ?? 'Starter Free',
            'is_paid' => $activeSub !== null && (float) ($plan?->price ?? 0) > 0,
            'is_active' => $activeSub !== null ? $activeSub->isActive() : true,
            'subscription' => $activeSub,
            
            // Community Requests
            'daily_request_limit' => $dailyRequestLimit,
            'daily_requests_used' => (int) $todayRequestsUsed,
            'daily_requests_remaining' => $dailyRequestsRemaining,
            'can_create_request' => $canCreateRequest,

            // Community Responses
            'daily_response_limit' => $dailyResponseLimit,
            'daily_responses_used' => (int) $todayResponsesUsed,
            'daily_responses_remaining' => $dailyResponsesRemaining,
            'can_respond' => $canRespond,

            // Total Listings Altogether
            'listing_limit' => $listingLimit,
            'listings_used' => (int) $totalListingsCount,
            'listings_remaining' => $listingsRemaining,
            'can_create_listing' => $canCreateListing,

            'starts_at' => $activeSub?->starts_at,
            'ends_at' => $activeSub?->ends_at,
            'days_left' => $daysLeft,
        ];
    }

    /**
     * Check if a user can create a community request today.
     */
    public function canCreateRequest(User $user): bool
    {
        return $this->getUsageStats($user)['can_create_request'];
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
     * Initialize subscription checkout using admin-configured default gateway or activate free tier directly.
     */
    public function initializeSubscriptionCheckout(
        User $user,
        int $planId,
        ?string $provider = null,
        int $months = 1,
        ?string $promoCode = null,
        ?float $customAmount = null,
        float $durationDiscount = 0.0,
        float $promoDiscount = 0.0
    ): array {
        $plan = SubscriptionPlan::findOrFail($planId);
        $provider = $provider ?: config('services.payment.default_gateway', 'paystack');

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

        // Calculate amount if not custom
        if ($customAmount !== null) {
            $payableAmount = max(0.0, $customAmount);
        } else {
            $baseTotal = (float) $plan->price * max(1, $months);
            $payableAmount = max(0.0, $baseTotal - $durationDiscount - $promoDiscount);
        }

        // If discounts make it completely free (e.g. 100% promo)
        if ($payableAmount <= 0.0) {
            $reference = 'FREE-' . strtoupper(Str::random(10));
            $payment = Payment::create([
                'user_id' => $user->id,
                'reference' => $reference,
                'provider' => $provider,
                'status' => 'successful',
                'amount' => 0.00,
                'currency' => 'NGN',
                'paid_at' => now(),
                'metadata' => [
                    'payment_type' => 'subscription',
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                    'months' => $months,
                    'promo_code' => $promoCode,
                    'duration_discount' => $durationDiscount,
                    'promo_discount' => $promoDiscount,
                ],
            ]);

            $subscription = $this->activateSubscription($payment);

            return [
                'status' => 'success',
                'is_free' => false,
                'subscription' => $subscription,
                'redirect_url' => route('dashboard'),
            ];
        }

        $reference = 'SUB-' . strtoupper(Str::random(10));

        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'provider' => $provider,
            'status' => 'pending',
            'amount' => $payableAmount,
            'currency' => 'NGN',
            'metadata' => [
                'payment_type' => 'subscription',
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'user_id' => $user->id,
                'months' => $months,
                'promo_code' => $promoCode,
                'duration_discount' => $durationDiscount,
                'promo_discount' => $promoDiscount,
            ],
        ]);

        $callbackUrl = route('payment.callback', ['reference' => $reference, 'provider' => $provider]);
        $authorizationUrl = null;

        $gatewayKey = config("services.{$provider}.secret");
        if (empty($gatewayKey) && app()->isLocal()) {
            $authorizationUrl = route('payment.callback', [
                'reference' => $reference,
                'provider' => $provider,
                'mock_success' => 1,
            ]);
        } else {
            if ($provider === 'flutterwave') {
                $response = $this->flutterwave->initialize($payment, $callbackUrl);
                $authorizationUrl = $response['link'] ?? $response['authorization_url'] ?? null;
            } else {
                $response = $this->paystack->initialize($payment, $callbackUrl);
                $authorizationUrl = $response['authorization_url'] ?? null;
            }
        }

        return [
            'status' => 'success',
            'is_free' => false,
            'payment' => $payment,
            'reference' => $reference,
            'authorization_url' => $authorizationUrl ?? $callbackUrl,
        ];
    }

    /**
     * Activate paid subscription upon successful payment confirmation.
     */
    public function activateSubscription(Payment $payment): Subscription
    {
        $planId = $payment->metadata['plan_id'] ?? null;
        $plan = SubscriptionPlan::findOrFail($planId);
        $months = max(1, (int) ($payment->metadata['months'] ?? 1));

        // Cancel previous active subscriptions
        Subscription::where('user_id', $payment->user_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $startsAt = now();
        $endsAt = now()->addMonths($months);

        $subscription = Subscription::create([
            'user_id' => $payment->user_id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'request_limit' => $plan->request_limit ?? ($plan->features['daily_request_limit'] ?? 1),
            'response_limit' => $plan->response_limit ?? ($plan->features['daily_response_limit'] ?? 1),
            'listing_limit' => $plan->listing_limit ?? ($plan->features['listing_limit'] ?? 10),
        ]);

        $payment->update([
            'subscription_id' => $subscription->id,
            'status' => 'successful',
            'paid_at' => now(),
        ]);

        // Record promo code usage if applied
        if (! empty($payment->metadata['promo_code'])) {
            PromoCode::where('code', $payment->metadata['promo_code'])->first()?->recordUsage();
        }

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
            'request_limit' => $plan->request_limit ?? 1,
            'response_limit' => $plan->response_limit ?? 1,
            'listing_limit' => $plan->listing_limit ?? 10,
        ]);
    }
}
