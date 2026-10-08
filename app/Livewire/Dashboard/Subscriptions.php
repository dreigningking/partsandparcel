<?php

namespace App\Livewire\Dashboard;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class Subscriptions extends Component
{
    public function renewSubscription()
    {
        $user = Auth::user();
        if (! $user) return redirect()->route('login');

        $subscriptionService = app(SubscriptionService::class);
        $usage = $subscriptionService->getUsageStats($user);

        $planId = $usage['plan_id'];
        if (! $planId || ! $usage['is_paid']) {
            $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->first();
            $planId = $proPlan?->id ?? 2;
        }

        // Redirect to payment confirmation for full renewal customization
        return redirect()->route('subscription.confirm', ['plan' => $planId]);
    }

    public function render()
    {
        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);

        $usage = $user ? $subscriptionService->getUsageStats($user) : [
            'plan_name' => 'Starter Free',
            'is_paid' => false,
            'is_active' => true,
            'daily_request_limit' => 1,
            'daily_requests_used' => 0,
            'daily_requests_remaining' => 1,
            'can_create_request' => true,
            'daily_response_limit' => 1,
            'daily_responses_used' => 0,
            'daily_responses_remaining' => 1,
            'can_respond' => true,
            'listing_limit' => 10,
            'listings_used' => 0,
            'listings_remaining' => 10,
            'can_create_listing' => true,
            'starts_at' => now(),
            'ends_at' => null,
            'days_left' => null,
        ];

        $billingHistory = $user
            ? Payment::where('user_id', $user->id)
                ->where('paymentable_type',Subscription::class)
                ->latest()
                ->take(10)
                ->get()
            : collect();

        return view('livewire.dashboard.subscriptions', [
            'usage' => $usage,
            'billingHistory' => $billingHistory,
        ]);
    }
}
