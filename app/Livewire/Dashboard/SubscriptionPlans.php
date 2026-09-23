<?php

namespace App\Livewire\Dashboard;

use App\Models\SubscriptionPlan;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class SubscriptionPlans extends Component
{
    public function selectPlan($planId, $provider = 'paystack')
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please log in to manage your subscription.');
            return redirect()->route('login');
        }

        $subscriptionService = app(SubscriptionService::class);
        $result = $subscriptionService->initializeSubscriptionCheckout($user, (int) $planId, $provider);

        if ($result['is_free']) {
            session()->flash('message', 'Switched to Starter Free plan successfully.');
            return redirect()->route('subscriptions');
        }

        return redirect()->away($result['authorization_url']);
    }

    public function render()
    {
        $user = Auth::user();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        $activePlanId = null;
        if ($user) {
            $activeSub = $user->activeSubscription;
            if ($activeSub) {
                $activePlanId = $activeSub->subscription_plan_id;
            } else {
                $starter = $plans->firstWhere('price', 0);
                $activePlanId = $starter?->id;
            }
        }

        return view('livewire.dashboard.subscription-plans', [
            'plans' => $plans,
            'activePlanId' => $activePlanId,
        ]);
    }
}
