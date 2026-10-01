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
    public string $billingCycle = 'monthly'; // 'monthly' | 'annual'

    public function setBillingCycle(string $cycle)
    {
        $this->billingCycle = in_array($cycle, ['monthly', 'annual']) ? $cycle : 'monthly';
    }

    public function selectPlan($planId)
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please log in to manage your subscription.');
            return redirect()->route('login');
        }

        $plan = SubscriptionPlan::findOrFail($planId);

        // If Starter Free plan, activate directly
        if ((float) $plan->price <= 0.0) {
            app(SubscriptionService::class)->activateFreeSubscription($user, $plan);
            session()->flash('message', 'Switched to Starter Free plan successfully.');
            return redirect()->route('subscriptions');
        }

        // If Paid Plan, proceed to Payment Confirmation page
        return redirect()->route('subscription.confirm', [
            'plan' => $plan->id,
            'billing' => $this->billingCycle,
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $plans = SubscriptionPlan::with('prices')->where('is_active', true)->get();

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
            'billingCycle' => $this->billingCycle,
        ]);
    }
}
