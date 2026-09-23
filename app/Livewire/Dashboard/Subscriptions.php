<?php

namespace App\Livewire\Dashboard;

use App\Models\Payment;
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
        if (! $planId) {
            $proPlan = SubscriptionPlan::where('name', 'like', '%Pro%')->first();
            $planId = $proPlan?->id ?? 1;
        }

        $result = $subscriptionService->initializeSubscriptionCheckout($user, $planId, 'paystack');

        if ($result['is_free']) {
            session()->flash('message', 'Starter Free plan active.');
            return redirect()->route('subscriptions');
        }

        return redirect()->away($result['authorization_url']);
    }

    public function render()
    {
        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);

        $usage = $user ? $subscriptionService->getUsageStats($user) : [
            'plan_name' => 'Starter Free',
            'is_paid' => false,
            'is_active' => true,
            'daily_response_limit' => 1,
            'daily_responses_used' => 0,
            'daily_responses_remaining' => 1,
            'listing_limit' => 10,
            'listings_used' => 0,
            'listings_remaining' => 10,
            'has_disassembly_tool' => false,
            'starts_at' => now(),
            'ends_at' => null,
            'days_left' => null,
        ];

        $billingHistory = $user
            ? Payment::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->whereNotNull('subscription_id')
                        ->orWhere('metadata->payment_type', 'subscription')
                        ->orWhere('metadata->payment_type', 'subscription_renewal');
                })
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
