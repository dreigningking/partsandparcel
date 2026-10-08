<?php

namespace App\Livewire\Components\Subscriptions;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionPlansView extends Component
{
    public string $billingCycle = 'monthly'; // 'monthly' | 'annual'
    public bool $isDashboard = false;
    public ?int $selectedCountryId = null;

    public function mount(?bool $isDashboard = null): void
    {
        if ($isDashboard !== null) {
            $this->isDashboard = $isDashboard;
        } else {
            $this->isDashboard = Auth::check();
        }

        $user = Auth::user();
        if ($user) {
            $this->selectedCountryId = $user->country_id ?? session('current_location.country_id');
        } else {
            $this->selectedCountryId = session('current_location.country_id') ?? session('country_id');
        }
    }

    public function setBillingCycle(string $cycle): void
    {
        $this->billingCycle = in_array($cycle, ['monthly', 'annual']) ? $cycle : 'monthly';
    }

    public function selectPlan(int $planId)
    {
        $user = Auth::user();

        // If guest, direct to login/registration
        if (! $user) {
            session()->put('url.intended', route('subscription.confirm', [
                'plan' => $planId,
                'billing' => $this->billingCycle,
            ]));
            session()->flash('warning', 'Please sign in or create an account to activate your subscription plan.');
            return redirect()->route('login');
        }

        $plan = SubscriptionPlan::findOrFail($planId);
        $targetCountry = $this->resolveCountry();
        $monthlyPrice = $plan->getMonthlyPrice(
            $targetCountry?->currency,
            $targetCountry?->code,
            $targetCountry?->id
        );

        // If Starter Free plan, activate directly
        if ($monthlyPrice <= 0.0) {
            app(SubscriptionService::class)->activateFreeSubscription($user, $plan);
            session()->flash('message', "Activated {$plan->name} successfully.");
            return redirect()->route('subscriptions');
        }

        // If Paid Plan, proceed to Payment Confirmation page
        return redirect()->route('subscription.confirm', [
            'plan' => $plan->id,
            'billing' => $this->billingCycle,
        ]);
    }

    protected function resolveCountry(): ?Country
    {
        $user = Auth::user();

        if ($user) {
            return $user->country
                ?? Country::find($user->country_id)
                ?? Country::find($this->selectedCountryId)
                ?? Country::where('code', $user->country_code ?? 'NG')->first()
                ?? Country::where('is_default', true)->first()
                ?? Country::first();
        }

        // Guest visitor: resolve from session
        $countryId = $this->selectedCountryId ?? session('current_location.country_id') ?? session('country_id');
        if ($countryId) {
            $country = Country::find($countryId);
            if ($country) {
                return $country;
            }
        }

        $code = session('current_location.country_code');
        if ($code) {
            $country = Country::where('code', strtoupper($code))->first();
            if ($country) {
                return $country;
            }
        }

        return Country::where('is_default', true)->first()
            ?? Country::where('code', 'NG')->first()
            ?? Country::first();
    }

    public function render()
    {
        $user = Auth::user();
        $targetCountry = $this->resolveCountry();
        $currency = $targetCountry?->currency ?: 'NGN';
        $countryCode = $targetCountry?->code ?: 'NG';
        $countryId = $targetCountry?->id;

        // Obtain plans dynamically arranged by sort_order
        $plans = SubscriptionPlan::with(['prices.country'])
            ->where('is_active', true)
            ->ordered()
            ->get();

        $activePlanId = null;
        if ($user) {
            $activeSub = $user->activeSubscription;
            if ($activeSub) {
                $activePlanId = $activeSub->subscription_plan_id;
            } else {
                $starter = $plans->first(fn ($p) => (float) $p->getMonthlyPrice($currency, $countryCode, $countryId) <= 0);
                $activePlanId = $starter?->id;
            }
        }

        return view('livewire.components.subscriptions.subscription-plans-view', [
            'plans' => $plans,
            'activePlanId' => $activePlanId,
            'billingCycle' => $this->billingCycle,
            'targetCountry' => $targetCountry,
            'currency' => $currency,
            'countryCode' => $countryCode,
            'countryId' => $countryId,
            'isDashboard' => $this->isDashboard,
        ]);
    }
}
