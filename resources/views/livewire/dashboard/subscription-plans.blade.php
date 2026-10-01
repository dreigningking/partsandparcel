<main class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
  
  <div class="text-center max-w-2xl mx-auto space-y-3">
    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 text-xs font-extrabold uppercase">Marketplace Subscriptions</span>
    <h1 class="text-3xl font-extrabold text-slate-950">Scale Your Parts &amp; Device Business</h1>
    <p class="text-xs text-slate-500">Unlock higher daily community requests &amp; quote responses plus expanded active inventory listings.</p>

    <!-- BILLING INTERVAL TOGGLE (MONTHLY VS ANNUAL) -->
    <div class="pt-3 flex items-center justify-center">
      <div class="p-1 rounded-2xl bg-slate-100 border border-slate-200/80 inline-flex items-center gap-1 shadow-2xs">
        <button type="button" wire:click="setBillingCycle('monthly')" 
          class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $billingCycle === 'monthly' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}">
          Monthly Billing
        </button>
        <button type="button" wire:click="setBillingCycle('annual')" 
          class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 {{ $billingCycle === 'annual' ? 'bg-white text-pp-700 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}">
          <span>Annual Billing</span>
          <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Save ~17%</span>
        </button>
      </div>
    </div>
  </div>

  @php
    $starterPlan = $plans->first(fn($p) => str_contains(strtolower($p->name), 'starter') || (float)$p->price == 0);
    $proPlan = $plans->first(fn($p) => str_contains(strtolower($p->name), 'pro'));
    $enterprisePlan = $plans->first(fn($p) => str_contains(strtolower($p->name), 'enterprise'));
  @endphp

  <!-- PRICING TIERS GRID -->
  <div class="grid md:grid-cols-3 gap-6">
    
    <!-- FREE TIER -->
    @if($starterPlan)
    <div class="bg-white rounded-3xl border {{ $activePlanId === $starterPlan->id ? 'border-pp-600 ring-2 ring-pp-600/20' : 'border-slate-200' }} p-8 space-y-6 shadow-soft flex flex-col justify-between">
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">Starter</span>
          @if($activePlanId === $starterPlan->id)
            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">Active Plan</span>
          @endif
        </div>
        <h3 class="text-2xl font-extrabold text-slate-900">{{ $starterPlan->name }}</h3>
        <div class="text-3xl font-extrabold text-slate-950">₦0 <span class="text-xs font-normal text-slate-400">/ forever</span></div>
        <p class="text-xs text-slate-500">For individual buyers, technicians, and occasional sellers.</p>

        <ul class="space-y-3 text-xs text-slate-600 border-t border-slate-100 pt-4">
          <li class="flex items-center gap-2.5 font-bold text-slate-900">
            <i class="fas fa-check text-emerald-600"></i>
            <span><strong>{{ $starterPlan->daily_request_limit }}</strong> Community Request per day</span>
          </li>
          <li class="flex items-center gap-2.5 font-bold text-slate-900">
            <i class="fas fa-check text-emerald-600"></i>
            <span><strong>{{ $starterPlan->daily_response_limit }}</strong> Quote Response per day</span>
          </li>
          <li class="flex items-center gap-2.5 font-bold text-slate-900">
            <i class="fas fa-check text-emerald-600"></i>
            <span>Up to <strong>{{ $starterPlan->total_listing_limit }}</strong> Total Listings altogether</span>
          </li>
          <li class="flex items-center gap-2.5">
            <i class="fas fa-check text-emerald-600"></i>
            <span>Standard Buyer &amp; Seller Protection</span>
          </li>
        </ul>
      </div>

      @if($activePlanId === $starterPlan->id)
        <button disabled class="w-full py-3.5 rounded-xl bg-slate-100 text-slate-600 font-extrabold text-xs cursor-default">
          Current Default Plan
        </button>
      @else
        <button wire:click="selectPlan({{ $starterPlan->id }})" class="w-full py-3.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-extrabold text-xs transition cursor-pointer">
          Switch to Free Tier
        </button>
      @endif
    </div>
    @endif

    <!-- PRO TIER (POPULAR) -->
    @if($proPlan)
    <div class="bg-white rounded-3xl border-2 border-pp-600 p-8 space-y-6 shadow-soft relative flex flex-col justify-between">
      <span class="absolute -top-3 right-6 px-3 py-1 rounded-full bg-pp-600 text-white text-[10px] font-extrabold uppercase">
        POPULAR FOR TECHNICIANS
      </span>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-xs font-extrabold text-pp-600 uppercase tracking-wider block">Professional</span>
          @if($activePlanId === $proPlan->id)
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Active Plan</span>
          @endif
        </div>
        <h3 class="text-2xl font-extrabold text-slate-900">{{ $proPlan->name }}</h3>
        
        @if($billingCycle === 'annual')
          <div>
            <div class="text-3xl font-extrabold text-slate-950">@money($proPlan->annual_price) <span class="text-xs font-normal text-slate-400">/ year</span></div>
            <span class="text-[11px] text-emerald-700 font-bold block mt-0.5">Save ₦4,000/yr (2 months free!)</span>
          </div>
        @else
          <div>
            <div class="text-3xl font-extrabold text-slate-950">@money($proPlan->price) <span class="text-xs font-normal text-slate-400">/ month</span></div>
            <span class="text-[11px] text-slate-400 block mt-0.5">or @money($proPlan->annual_price) billed annually</span>
          </div>
        @endif

        <p class="text-xs text-slate-500">For active spare parts dealers, repair shops, and salvage vendors.</p>

        <ul class="space-y-3 text-xs text-slate-700 border-t border-slate-100 pt-4 font-medium">
          <li class="flex items-center gap-2.5 text-emerald-700 font-bold">
            <i class="fas fa-check text-emerald-600"></i>
            <span><strong>{{ $proPlan->daily_request_limit }}</strong> Community Requests per day</span>
          </li>
          <li class="flex items-center gap-2.5 text-emerald-700 font-bold">
            <i class="fas fa-check text-emerald-600"></i>
            <span><strong>{{ $proPlan->daily_response_limit }}</strong> Quote Responses per day</span>
          </li>
          <li class="flex items-center gap-2.5 text-emerald-700 font-bold">
            <i class="fas fa-check text-emerald-600"></i>
            <span>Up to <strong>{{ $proPlan->total_listing_limit }}</strong> Total Listings altogether</span>
          </li>
          <li class="flex items-center gap-2.5">
            <i class="fas fa-check text-emerald-600"></i>
            <span>Priority Search &amp; RFQ Visibility</span>
          </li>
          <li class="flex items-center gap-2.5">
            <i class="fas fa-check text-emerald-600"></i>
            <span>Direct Payouts to Bank Account</span>
          </li>
        </ul>
      </div>

      @if($activePlanId === $proPlan->id)
        <a href="{{ route('subscription.confirm', ['plan' => $proPlan->id, 'billing' => $billingCycle]) }}" 
          class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs text-center block shadow-xs transition">
          Renew or Extend Pro Plan →
        </a>
      @else
        <button wire:click="selectPlan({{ $proPlan->id }})" class="w-full py-3.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-sm transition cursor-pointer">
          Upgrade to Pro Plan →
        </button>
      @endif
    </div>
    @endif

    <!-- BUSINESS / ENTERPRISE TIER -->
    @if($enterprisePlan)
    <div class="bg-slate-900 text-white rounded-3xl p-8 space-y-6 shadow-soft flex flex-col justify-between">
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-xs font-extrabold text-pp-200 uppercase tracking-wider block">Enterprise</span>
          @if($activePlanId === $enterprisePlan->id)
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-400 text-slate-950 text-[10px] font-extrabold uppercase">Active Plan</span>
          @endif
        </div>
        <h3 class="text-2xl font-extrabold text-white">{{ $enterprisePlan->name }}</h3>
        
        @if($billingCycle === 'annual')
          <div>
            <div class="text-3xl font-extrabold text-white">@money($enterprisePlan->annual_price) <span class="text-xs font-normal text-slate-400">/ year</span></div>
            <span class="text-[11px] text-emerald-400 font-bold block mt-0.5">Save ₦10,000/yr (2 months free!)</span>
          </div>
        @else
          <div>
            <div class="text-3xl font-extrabold text-white">@money($enterprisePlan->price) <span class="text-xs font-normal text-slate-400">/ month</span></div>
            <span class="text-[11px] text-slate-400 block mt-0.5">or @money($enterprisePlan->annual_price) billed annually</span>
          </div>
        @endif

        <p class="text-xs text-slate-300">For major Computer Village importers, auto yards, and machinery hubs.</p>

        <ul class="space-y-3 text-xs text-slate-300 border-t border-white/10 pt-4">
          <li class="flex items-center gap-2.5 text-emerald-400 font-bold">
            <i class="fas fa-check text-emerald-400"></i>
            <span><strong>{{ $enterprisePlan->daily_request_limit }}</strong> Community Requests per day</span>
          </li>
          <li class="flex items-center gap-2.5 text-emerald-400 font-bold">
            <i class="fas fa-check text-emerald-400"></i>
            <span><strong>{{ $enterprisePlan->daily_response_limit }}</strong> Quote Responses per day</span>
          </li>
          <li class="flex items-center gap-2.5 text-emerald-400 font-bold">
            <i class="fas fa-check text-emerald-400"></i>
            <span>Up to <strong>{{ $enterprisePlan->total_listing_limit }}</strong> Total Listings altogether</span>
          </li>
          <li class="flex items-center gap-2.5">
            <i class="fas fa-check text-emerald-400"></i>
            <span>Dedicated Dispute Resolution Line</span>
          </li>
          <li class="flex items-center gap-2.5">
            <i class="fas fa-check text-emerald-400"></i>
            <span>Lowest Platform Commission Tier</span>
          </li>
        </ul>
      </div>

      @if($activePlanId === $enterprisePlan->id)
        <a href="{{ route('subscription.confirm', ['plan' => $enterprisePlan->id, 'billing' => $billingCycle]) }}" 
          class="w-full py-3.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-extrabold text-xs text-center block transition">
          Renew or Extend Enterprise Plan →
        </a>
      @else
        <button wire:click="selectPlan({{ $enterprisePlan->id }})" class="w-full py-3.5 rounded-xl bg-white text-slate-900 font-extrabold text-xs hover:bg-pp-50 transition cursor-pointer">
          Upgrade to Enterprise Plan →
        </button>
      @endif
    </div>
    @endif

  </div>

</main>