<div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
  
  <!-- HERO HEADER & BILLING INTERVAL SWITCHER -->
  <div class="text-center max-w-3xl mx-auto space-y-3">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pp-50 border border-pp-200/80 text-pp-700 text-xs font-extrabold uppercase">
      <i class="fas fa-layer-group text-[11px]"></i>
      <span>Marketplace Subscriptions</span>
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight">
      Scale Your Parts &amp; Device Business
    </h1>

    <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
      Unlock higher daily community requests &amp; quote responses plus expanded active inventory listings to grow your sales.
    </p>

    <!-- COUNTRY INDICATOR BADGE -->
    <div class="pt-1 flex items-center justify-center gap-2 text-xs font-semibold text-slate-500">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-[11px] font-bold">
        <i class="fas fa-globe text-slate-400"></i>
        <span>Pricing shown in <strong>{{ $currency }}</strong> for <strong>{{ $targetCountry?->name ?? 'Nigeria' }}</strong></span>
      </span>
    </div>

    <!-- BILLING INTERVAL TOGGLE (MONTHLY VS ANNUAL) -->
    <div class="pt-4 flex items-center justify-center">
      <div class="p-1.5 rounded-2xl bg-slate-100 border border-slate-200 inline-flex items-center gap-1.5 shadow-2xs">
        <button
          type="button"
          wire:click="setBillingCycle('monthly')" 
          class="px-5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $billingCycle === 'monthly' ? 'bg-white text-slate-950 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}"
        >
          Monthly Billing
        </button>

        <button
          type="button"
          wire:click="setBillingCycle('annual')" 
          class="px-5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 {{ $billingCycle === 'annual' ? 'bg-white text-pp-700 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}"
        >
          <span>Annual Billing</span>
          <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
            Save ~17% (2 Mo. Free)
          </span>
        </button>
      </div>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('message'))
    <div class="max-w-xl mx-auto p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2 shadow-2xs">
      <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
      <span>{{ session('message') }}</span>
    </div>
  @endif

  @if (session()->has('warning'))
    <div class="max-w-xl mx-auto p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-2 shadow-2xs">
      <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
      <span>{{ session('warning') }}</span>
    </div>
  @endif

  <!-- DYNAMIC PLANS GRID -->
  <div class="grid grid-cols-1 md:grid-cols-2 {{ $plans->count() >= 3 ? 'lg:grid-cols-3' : '' }} gap-6 lg:gap-8 items-stretch">
    @forelse ($plans as $plan)
      @php
        $monthlyPrice = (float) $plan->getMonthlyPrice($currency, $countryCode, $countryId);
        $annualPrice = (float) $plan->getAnnualPrice($currency, $countryCode, $countryId);
        $isFree = ($monthlyPrice <= 0.0);
        $isActive = ($activePlanId === $plan->id);
        $features = $plan->features ?? [];
        $isPopular = !empty($features['priority_placement']) || str_contains(strtolower($plan->name), 'pro');
      @endphp

      <div class="rounded-3xl p-8 space-y-6 shadow-soft flex flex-col justify-between transition relative bg-white {{ $isPopular ? 'border-2 border-pp-600 ring-4 ring-pp-50' : 'border border-slate-200 hover:border-slate-300' }} {{ $isActive ? 'ring-2 ring-emerald-500 border-emerald-500' : '' }}">
        
        <!-- POPULAR BADGE -->
        @if ($isPopular)
          <span class="absolute -top-3 right-6 px-3.5 py-1 rounded-full bg-pp-600 text-white text-[10px] font-black uppercase tracking-wider shadow-xs">
            RECOMMENDED FOR PROS
          </span>
        @endif

        <div class="space-y-5">
          <!-- TOP ROW: TIER TAG & ACTIVE STATUS -->
          <div class="flex items-center justify-between">
            <span class="text-xs font-black uppercase tracking-wider {{ $isPopular ? 'text-pp-700' : 'text-slate-400' }}">
              {{ $plan->name }}
            </span>

            @if ($isActive)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-black uppercase flex items-center gap-1">
                <i class="fas fa-check-circle text-emerald-600"></i> Active Plan
              </span>
            @endif
          </div>

          <!-- PLAN HEADER & PRICING -->
          <div>
            <h3 class="text-2xl font-extrabold text-slate-950">{{ $plan->name }}</h3>
            
            <div class="mt-2">
              @if ($isFree)
                <div class="text-3xl sm:text-4xl font-black text-slate-950">
                  @money(0, $currency)
                  <span class="text-xs font-semibold text-slate-400">/ forever</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Free basic tier for occasional buyers and community requests.</p>
              @else
                @if ($billingCycle === 'annual')
                  <div class="text-3xl sm:text-4xl font-black text-slate-950">
                    @money($annualPrice, $currency)
                    <span class="text-xs font-semibold text-slate-400">/ year</span>
                  </div>
                  @php
                    $savings = max(0, ($monthlyPrice * 12) - $annualPrice);
                  @endphp
                  @if ($savings > 0)
                    <span class="text-[11px] text-emerald-700 font-extrabold block mt-1">
                      Save @money($savings, $currency) annually (2 months free)
                    </span>
                  @endif
                @else
                  <div class="text-3xl sm:text-4xl font-black text-slate-950">
                    @money($monthlyPrice, $currency)
                    <span class="text-xs font-semibold text-slate-400">/ month</span>
                  </div>
                  <span class="text-[11px] text-slate-400 font-medium block mt-1">
                    Billed monthly. Switch or cancel anytime.
                  </span>
                @endif
              @endif
            </div>
          </div>

          <!-- PLAN LIMITS & FEATURES LIST -->
          <ul class="space-y-3.5 text-xs text-slate-700 border-t border-slate-100 pt-5 font-medium">
            <li class="flex items-center gap-3">
              <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                <i class="fas fa-check"></i>
              </div>
              <span><strong>{{ $plan->daily_request_limit }}</strong> Community Requests per day</span>
            </li>

            <li class="flex items-center gap-3">
              <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                <i class="fas fa-check"></i>
              </div>
              <span><strong>{{ $plan->daily_response_limit }}</strong> Quote Responses per day</span>
            </li>

            <li class="flex items-center gap-3">
              <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                <i class="fas fa-check"></i>
              </div>
              <span>Up to <strong>{{ $plan->total_listing_limit }}</strong> Active Listings</span>
            </li>

            <li class="flex items-center gap-3">
              <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                <i class="fas fa-check"></i>
              </div>
              <span>
                <strong>{{ (float) $plan->escrow_percentage }}%</strong> Escrow Fee
                @if ($plan->escrow_cap)
                  (Capped at @money($plan->escrow_cap, $currency))
                @endif
              </span>
            </li>

            @if (!empty($features['priority_placement']))
              <li class="flex items-center gap-3 text-slate-900 font-bold">
                <div class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                  <i class="fas fa-arrow-trend-up"></i>
                </div>
                <span>Priority Search &amp; Category Placement</span>
              </li>
            @endif

            @if (!empty($features['verified_badge']))
              <li class="flex items-center gap-3 text-slate-900 font-bold">
                <div class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                  <i class="fas fa-id-badge"></i>
                </div>
                <span>Verified Business / Technician Badge</span>
              </li>
            @endif

            @if (!empty($features['dedicated_support']))
              <li class="flex items-center gap-3 text-slate-900 font-bold">
                <div class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                  <i class="fas fa-headset"></i>
                </div>
                <span>Dedicated Support &amp; Faster Responses</span>
              </li>
            @endif

            @if (!empty($features['dedicated_arbitration']))
              <li class="flex items-center gap-3 text-slate-900 font-bold">
                <div class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 flex items-center justify-center text-[10px] shrink-0 font-bold">
                  <i class="fas fa-scale-balanced"></i>
                </div>
                <span>Expedited Escrow Dispute Arbitration</span>
              </li>
            @endif
          </ul>
        </div>

        <!-- ACTION BUTTON -->
        <div class="pt-4 border-t border-slate-100">
          @if ($isActive)
            <button disabled class="w-full py-3.5 rounded-xl bg-slate-100 text-slate-500 font-extrabold text-xs cursor-default text-center">
              Current Active Plan
            </button>
          @elseif (auth()->check())
            @if ($isFree)
              <button
                type="button"
                wire:click="selectPlan({{ $plan->id }})"
                class="w-full py-3.5 rounded-xl border border-slate-300 text-slate-800 hover:bg-slate-50 font-extrabold text-xs transition cursor-pointer text-center"
              >
                Switch to Free Tier
              </button>
            @else
              <button
                type="button"
                wire:click="selectPlan({{ $plan->id }})"
                class="w-full py-3.5 rounded-xl {{ $isPopular ? 'bg-pp-600 hover:bg-pp-700 text-white shadow-xs' : 'bg-slate-950 hover:bg-slate-800 text-white' }} font-extrabold text-xs transition cursor-pointer text-center"
              >
                Upgrade to {{ $plan->name }} →
              </button>
            @endif
          @else
            <!-- GUEST CTA -->
            <button
              type="button"
              wire:click="selectPlan({{ $plan->id }})"
              class="w-full py-3.5 rounded-xl {{ $isPopular ? 'bg-pp-600 hover:bg-pp-700 text-white shadow-xs' : ($isFree ? 'border border-slate-300 text-slate-800 hover:bg-slate-50' : 'bg-slate-950 hover:bg-slate-800 text-white') }} font-extrabold text-xs transition cursor-pointer text-center"
            >
              {{ $isFree ? 'Get Started Free' : 'Choose ' . $plan->name . ' →' }}
            </button>
          @endif
        </div>

      </div>
    @empty
      <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-3">
        <i class="fas fa-box-open text-4xl text-slate-300"></i>
        <h4 class="font-extrabold text-slate-900 text-base">No Subscription Plans Available</h4>
        <p class="text-xs text-slate-500 max-w-md mx-auto">Plans are being configured by platform administrators. Please check back shortly.</p>
      </div>
    @endforelse
  </div>

  <!-- BOTTOM ASSURANCE FOOTER -->
  <div class="rounded-3xl bg-slate-50 border border-slate-200/80 p-6 sm:p-8">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center sm:text-left">
      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-2xl bg-pp-100 text-pp-700 grid place-items-center text-sm font-bold shrink-0">
          <i class="fas fa-shield-alt"></i>
        </div>
        <div>
          <h5 class="text-xs font-black text-slate-900">Escrow Security Included</h5>
          <p class="text-[11px] text-slate-500 mt-0.5">Every transaction is safeguarded by Parts &amp; Parcel inspection holding.</p>
        </div>
      </div>

      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 grid place-items-center text-sm font-bold shrink-0">
          <i class="fas fa-rotate"></i>
        </div>
        <div>
          <h5 class="text-xs font-black text-slate-900">Switch or Cancel Anytime</h5>
          <p class="text-[11px] text-slate-500 mt-0.5">No lock-in contracts. Upgrade or downgrade as your repair business grows.</p>
        </div>
      </div>

      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-900 grid place-items-center text-sm font-bold shrink-0">
          <i class="fas fa-file-invoice"></i>
        </div>
        <div>
          <h5 class="text-xs font-black text-slate-900">Official Tax Invoices</h5>
          <p class="text-[11px] text-slate-500 mt-0.5">Instant automated receipts and PDF invoice statements for all payments.</p>
        </div>
      </div>
    </div>
  </div>

</div>
