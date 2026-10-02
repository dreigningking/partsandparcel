<main class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

  <!-- BACK LINK -->
  <div>
    <a href="{{ route('subscription-plans') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-pp-700 transition">
      <i class="fas fa-arrow-left"></i> Back to Subscription Plans
    </a>
  </div>

  <!-- ERROR / FLASH MESSAGES -->
  @if(session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-800 text-xs font-bold flex items-start justify-between gap-3 shadow-sm">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-circle-exclamation text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">&times;</button>
    </div>
  @endif

  @if(session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
      <span>{{ session('message') }}</span>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
    </div>
  @endif

  <!-- PAGE TITLE -->
  <div class="space-y-1">
    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 text-xs font-extrabold uppercase">Step 2: Payment Confirmation</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950">Review Your Subscription &amp; Checkout</h1>
    <p class="text-xs text-slate-500">Configure your subscription duration, claim multi-month discounts, and complete payment.</p>
  </div>

  <div class="grid lg:grid-cols-12 gap-8 items-start">

    <!-- LEFT COLUMN: PLAN DETAILS & DURATION SELECTOR -->
    <div class="lg:col-span-7 space-y-6">

      <!-- PLAN CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-5 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
          <div>
            <span class="text-[10px] font-extrabold text-pp-600 uppercase tracking-wider block">Selected Plan</span>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $plan->name }}</h2>
          </div>
          <div class="text-right">
            <span class="text-2xl font-extrabold text-slate-950">@money($calc['monthly_price'], $calc['currency'])</span>
            <span class="text-[11px] text-slate-400 block font-normal">/ month base rate</span>
          </div>
        </div>

        <!-- THE 3 DETERMINED SUBSCRIPTION QUOTAS -->
        <div class="space-y-2.5">
          <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Plan Allowances &amp; Quotas</span>
          <div class="grid sm:grid-cols-3 gap-3 pt-1">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold text-slate-500 block uppercase">Daily Requests</span>
              <span class="text-lg font-extrabold text-slate-900 block">{{ $plan->daily_request_limit }}</span>
              <span class="text-[10px] text-slate-400 block">per day</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold text-slate-500 block uppercase">Daily Responses</span>
              <span class="text-lg font-extrabold text-slate-900 block">{{ $plan->daily_response_limit }}</span>
              <span class="text-[10px] text-slate-400 block">per day</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold text-slate-500 block uppercase">Total Listings</span>
              <span class="text-lg font-extrabold text-slate-900 block">{{ $plan->total_listing_limit }}</span>
              <span class="text-[10px] text-slate-400 block">altogether</span>
            </div>
          </div>
        </div>
      </div>

      <!-- DURATION SELECTION CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-5 shadow-soft">
        <div>
          <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-calendar-alt text-pp-600"></i> Select Subscription Duration
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">Pay for multiple months in advance to unlock special discounts.</p>
        </div>

        <!-- DURATION PILLS -->
        <div class="grid sm:grid-cols-2 gap-3">
          
          <!-- 1 MONTH -->
          <button type="button" wire:click="selectMonths(1)" 
            class="p-4 rounded-2xl border-2 text-left transition cursor-pointer flex flex-col justify-between {{ $months === 1 ? 'border-pp-600 bg-pp-50/50 ring-2 ring-pp-600/20' : 'border-slate-200 hover:border-slate-300' }}">
            <div class="flex justify-between items-center">
              <span class="text-sm font-extrabold text-slate-900">1 Month</span>
              <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $months === 1 ? 'border-pp-600 bg-pp-600' : 'border-slate-300' }}">
                @if($months === 1)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
              </span>
            </div>
            <span class="text-xs text-slate-500 mt-2">Billed monthly · Standard rate</span>
            <span class="text-sm font-bold text-slate-900 mt-1">@money($calc['monthly_price'], $calc['currency'])</span>
          </button>

          <!-- 3 MONTHS -->
          <button type="button" wire:click="selectMonths(3)" 
            class="p-4 rounded-2xl border-2 text-left transition cursor-pointer flex flex-col justify-between {{ $months === 3 ? 'border-pp-600 bg-pp-50/50 ring-2 ring-pp-600/20' : 'border-slate-200 hover:border-slate-300' }}">
            <div class="flex justify-between items-center">
              <span class="text-sm font-extrabold text-slate-900">3 Months</span>
              <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $months === 3 ? 'border-pp-600 bg-pp-600' : 'border-slate-300' }}">
                @if($months === 3)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
              </span>
            </div>
            <span class="text-xs text-slate-500 mt-2">Quarterly billing</span>
            <span class="text-sm font-bold text-slate-900 mt-1">@money($calc['monthly_price'] * 3, $calc['currency'])</span>
          </button>

          <!-- 6 MONTHS (5% DISCOUNT) -->
          <button type="button" wire:click="selectMonths(6)" 
            class="p-4 rounded-2xl border-2 text-left transition cursor-pointer flex flex-col justify-between relative {{ $months === 6 ? 'border-pp-600 bg-pp-50/50 ring-2 ring-pp-600/20' : 'border-slate-200 hover:border-slate-300' }}">
            <span class="absolute -top-2.5 right-3 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[9px] font-extrabold uppercase">Save 5%</span>
            <div class="flex justify-between items-center">
              <span class="text-sm font-extrabold text-slate-900">6 Months</span>
              <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $months === 6 ? 'border-pp-600 bg-pp-600' : 'border-slate-300' }}">
                @if($months === 6)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
              </span>
            </div>
            <span class="text-xs text-emerald-700 font-bold mt-2">Semi-Annual Savings</span>
            <span class="text-sm font-bold text-slate-900 mt-1">@money(($calc['monthly_price'] * 6) * 0.95, $calc['currency'])</span>
          </button>

          <!-- 12 MONTHS / ANNUAL (2 MONTHS FREE / BIG DISCOUNT) -->
          <button type="button" wire:click="selectMonths(12)" 
            class="p-4 rounded-2xl border-2 text-left transition cursor-pointer flex flex-col justify-between relative {{ $months === 12 ? 'border-pp-600 bg-pp-50/50 ring-2 ring-pp-600/20' : 'border-pp-200 bg-pp-50/20 hover:border-pp-400' }}">
            <span class="absolute -top-2.5 right-3 px-2 py-0.5 rounded-full bg-pp-600 text-white text-[9px] font-extrabold uppercase tracking-wide">
              BEST VALUE · 2 MONTHS FREE
            </span>
            <div class="flex justify-between items-center">
              <span class="text-sm font-extrabold text-pp-900">12 Months (1 Year)</span>
              <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $months === 12 ? 'border-pp-600 bg-pp-600' : 'border-slate-300' }}">
                @if($months === 12)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
              </span>
            </div>
            <span class="text-xs text-pp-700 font-bold mt-2">Annual Package</span>
            <div class="flex items-baseline gap-2 mt-1">
              <span class="text-sm font-extrabold text-slate-950">@money($calc['annual_price'], $calc['currency'])</span>
              <span class="text-[11px] text-slate-400 line-through">@money($calc['monthly_price'] * 12, $calc['currency'])</span>
            </div>
          </button>

        </div>

        <!-- CUSTOM MONTH STEPPER -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
          <span class="font-bold text-slate-700">Need custom duration?</span>
          <div class="flex items-center gap-2">
            <button type="button" wire:click="selectMonths({{ max(1, $months - 1) }})" 
              class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer">-</button>
            <span class="font-extrabold text-slate-900 min-w-16 text-center">{{ $months }} {{ Str::plural('Month', $months) }}</span>
            <button type="button" wire:click="selectMonths({{ min(36, $months + 1) }})" 
              class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer">+</button>
          </div>
        </div>

      </div>

    </div>

    <!-- RIGHT COLUMN: ORDER SUMMARY & coupon & PAY -->
    <div class="lg:col-span-5 space-y-6">

      <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-4">
          Order Summary
        </h3>

        <!-- BREAKDOWN -->
        <div class="space-y-3 text-xs">
          
          <div class="flex justify-between items-center text-slate-600">
            <span>Base Subscription ({{ $months }} × @money($calc['monthly_price'], $calc['currency']))</span>
            <span class="font-bold text-slate-900">@money($calc['gross_total'], $calc['currency'])</span>
          </div>

          <!-- DURATION DISCOUNT -->
          @if($calc['duration_discount'] > 0)
            <div class="flex justify-between items-center text-emerald-700 font-bold bg-emerald-50/80 p-2.5 rounded-xl border border-emerald-200">
              <span class="flex items-center gap-1.5">
                <i class="fas fa-tag text-emerald-600"></i> Multi-Month Discount
              </span>
              <span>-@money($calc['duration_discount'], $calc['currency'])</span>
            </div>
          @endif

          <!-- PROMO DISCOUNT -->
          @if($calc['promo_discount'] > 0)
            <div class="flex justify-between items-center text-emerald-700 font-bold bg-emerald-50/80 p-2.5 rounded-xl border border-emerald-200">
              <span class="flex items-center gap-1.5">
                <i class="fas fa-percent text-emerald-600"></i> coupon ({{ $appliedPromo->code }})
              </span>
              <span>-@money($calc['promo_discount'], $calc['currency'])</span>
            </div>
          @endif

          <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline">
            <div>
              <span class="text-sm font-extrabold text-slate-900 block">Total Payable Now</span>
              <span class="text-[10px] text-slate-400">Includes all valid discounts</span>
            </div>
            <div class="text-right">
              <span class="text-2xl font-extrabold text-pp-700">@money($calc['total_payable'], $calc['currency'])</span>
              @if($calc['total_savings'] > 0)
                <span class="text-[10px] text-emerald-700 font-bold block">You save @money($calc['total_savings'], $calc['currency'])</span>
              @endif
            </div>
          </div>

        </div>

        <!-- coupon INPUT -->
        <div class="space-y-2 border-t border-slate-100 pt-4">
          <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider block">Have a coupon?</label>
          
          @if($appliedPromo)
            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
              <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span class="font-extrabold text-emerald-900">{{ $appliedPromo->code }}</span>
                <span class="text-[11px] text-emerald-700">({{ $appliedPromo->type === 'percentage' ? $appliedPromo->value . '% off' : '₦' . number_format($appliedPromo->value, 0) . ' off' }})</span>
              </div>
              <button type="button" wire:click="removeCoupon" class="text-rose-600 hover:text-rose-800 font-bold text-xs cursor-pointer">
                Remove
              </button>
            </div>
          @else
            <div class="flex gap-2">
              <input type="text" wire:model="couponInput" placeholder="Enter code (e.g. WELCOME10)" 
                class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600/20 focus:border-pp-600">
              <button type="button" wire:click="applyCoupon" 
                class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition cursor-pointer">
                Apply
              </button>
            </div>
          @endif

          @if($promoError)
            <p class="text-[11px] text-rose-600 font-bold flex items-center gap-1 mt-1">
              <i class="fas fa-circle-exclamation"></i> {{ $promoError }}
            </p>
          @endif
          @if($promoMessage)
            <p class="text-[11px] text-emerald-700 font-bold flex items-center gap-1 mt-1">
              <i class="fas fa-circle-check"></i> {{ $promoMessage }}
            </p>
          @endif
        </div>

        <!-- PAYMENT METHOD INFO (ADMIN CONFIG DEFAULT) -->
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-pp-600 font-extrabold text-sm shadow-2xs">
              <i class="fas fa-shield-alt text-pp-600"></i>
            </div>
            <div>
              <span class="font-extrabold text-slate-900 block">Automated Secure Checkout</span>
              <span class="text-[10px] text-slate-500">Processed seamlessly via <strong>{{ ucfirst($defaultGateway) }}</strong></span>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-extrabold uppercase">Verified</span>
        </div>

        <!-- PAY NOW CTA -->
        <button type="button" wire:click="pay" wire:loading.attr="disabled"
          class="w-full py-4 rounded-2xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-sm shadow-md transition cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60">
          <span wire:loading.remove>
            Pay @money($calc['total_payable'], $calc['currency']) with {{ ucfirst($defaultGateway) }} →
          </span>
          <span wire:loading class="flex items-center gap-2">
            <i class="fas fa-spinner fa-spin"></i> Processing Secure Checkout...
          </span>
        </button>

        <p class="text-[10px] text-center text-slate-400">
          By proceeding, you agree to the Parts &amp; Parcel Marketplace Terms of Service. Payments are encrypted with bank-grade 256-bit SSL.
        </p>

      </div>

    </div>

  </div>

</main>
