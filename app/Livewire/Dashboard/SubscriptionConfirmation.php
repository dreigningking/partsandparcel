<?php

namespace App\Livewire\Dashboard;

use App\Models\PromoCode;
use App\Models\SubscriptionPlan;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class SubscriptionConfirmation extends Component
{
    public int $planId;
    public ?SubscriptionPlan $plan = null;
    public int $months = 1;
    public string $promoCodeInput = '';
    public ?PromoCode $appliedPromo = null;
    public ?string $promoMessage = null;
    public ?string $promoError = null;
    public string $defaultGateway = 'paystack';
    public bool $isProcessing = false;

    public function mount($plan)
    {
        $this->planId = is_numeric($plan) ? (int) $plan : (int) ($plan->id ?? 1);
        $this->plan = SubscriptionPlan::with('prices')->findOrFail($this->planId);

        // If Starter Free plan, redirect directly to subscriptions overview
        if ((float) $this->plan->price <= 0) {
            $user = Auth::user();
            if ($user) {
                app(SubscriptionService::class)->activateFreeSubscription($user, $this->plan);
            }
            return redirect()->route('subscriptions')->with('message', 'Starter Free plan active.');
        }

        // Set duration based on query parameter
        if (request()->query('billing') === 'annual') {
            $this->months = 12;
        } else {
            $this->months = (int) request()->query('months', 1);
            if ($this->months < 1) $this->months = 1;
        }

        $this->defaultGateway = config('services.payment.default_gateway', 'paystack');
    }

    public function selectMonths(int $months)
    {
        $this->months = max(1, min(36, $months));
        $this->revalidatePromo();
    }

    public function setCustomMonths($value)
    {
        $this->months = max(1, min(36, (int) $value));
        $this->revalidatePromo();
    }

    public function applyPromoCode()
    {
        $this->promoError = null;
        $this->promoMessage = null;

        $code = strtoupper(trim($this->promoCodeInput));
        if (empty($code)) {
            $this->promoError = 'Please enter a promo code.';
            return;
        }

        $promo = PromoCode::where('code', $code)->first();

        if (! $promo) {
            $this->promoError = "Promo code '{$code}' is invalid or does not exist.";
            $this->appliedPromo = null;
            return;
        }

        $subtotal = $this->calculations['subtotal_after_duration'];
        $eligibility = $promo->validateEligibility($subtotal);

        if (! $eligibility['valid']) {
            $this->promoError = $eligibility['message'];
            $this->appliedPromo = null;
            return;
        }

        $this->appliedPromo = $promo;
        $this->promoMessage = "Promo code '{$promo->code}' applied successfully!";
        $this->promoError = null;
    }

    public function removePromoCode()
    {
        $this->appliedPromo = null;
        $this->promoCodeInput = '';
        $this->promoMessage = null;
        $this->promoError = null;
    }

    protected function revalidatePromo()
    {
        if ($this->appliedPromo) {
            $subtotal = $this->calculations['subtotal_after_duration'];
            $check = $this->appliedPromo->validateEligibility($subtotal);
            if (! $check['valid']) {
                $this->promoError = "Promo code removed: {$check['message']}";
                $this->appliedPromo = null;
                $this->promoMessage = null;
            }
        }
    }

    public function getCalculationsProperty(): array
    {
        $user = Auth::user();
        $currency = $user->currency ?? 'NGN';
        $country = $user->country_code ?? 'NG';

        $monthlyPrice = $this->plan->getMonthlyPrice($currency, $country);
        $annualPrice = $this->plan->getAnnualPrice($currency, $country);
        $months = max(1, $this->months);

        $grossTotal = $monthlyPrice * $months;

        // Duration / Annual discount
        $durationDiscount = 0.0;
        if ($months >= 12 && $annualPrice > 0) {
            $years = intdiv($months, 12);
            $remMonths = $months % 12;
            $discountedTotal = ($years * $annualPrice) + ($remMonths * $monthlyPrice);
            $durationDiscount = max(0.0, (float) ($grossTotal - $discountedTotal));
        } elseif ($months >= 6) {
            // 5% multi-month discount
            $durationDiscount = round($grossTotal * 0.05, 2);
        }

        $subtotalAfterDuration = max(0.0, (float) ($grossTotal - $durationDiscount));

        // Promo code discount
        $promoDiscount = 0.0;
        if ($this->appliedPromo) {
            $promoDiscount = (float) $this->appliedPromo->calculateDiscount($subtotalAfterDuration);
        }

        $totalPayable = max(0.0, (float) ($subtotalAfterDuration - $promoDiscount));

        return [
            'currency' => $currency,
            'monthly_price' => $monthlyPrice,
            'annual_price' => $annualPrice,
            'months' => $months,
            'gross_total' => $grossTotal,
            'duration_discount' => $durationDiscount,
            'subtotal_after_duration' => $subtotalAfterDuration,
            'promo_discount' => $promoDiscount,
            'total_payable' => $totalPayable,
            'total_savings' => $durationDiscount + $promoDiscount,
        ];
    }

    public function pay()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('error', 'Please log in to complete payment.');
            return redirect()->route('login');
        }

        $this->isProcessing = true;
        $calc = $this->calculations;

        $subscriptionService = app(SubscriptionService::class);
        $result = $subscriptionService->initializeSubscriptionCheckout(
            user: $user,
            planId: $this->plan->id,
            provider: $this->defaultGateway,
            months: $this->months,
            promoCode: $this->appliedPromo?->code,
            customAmount: $calc['total_payable'],
            durationDiscount: $calc['duration_discount'],
            promoDiscount: $calc['promo_discount']
        );

        if ($result['status'] === 'success') {
            if (! empty($result['redirect_url'])) {
                return redirect()->to($result['redirect_url']);
            }
            if (! empty($result['authorization_url'])) {
                return redirect()->away($result['authorization_url']);
            }
        }

        $this->isProcessing = false;
        session()->flash('error', $result['message'] ?? 'Unable to initialize checkout. Please try again.');
    }

    public function render()
    {
        return view('livewire.dashboard.subscription-confirmation', [
            'calc' => $this->calculations,
        ]);
    }
}
