<div class="flex flex-col gap-6">

  <!-- FLASH MESSAGES -->
  @if(session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
      <span>{{ session('message') }}</span>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
    </div>
  @endif
  @if(session()->has('warning'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center justify-between">
      <span>{{ session('warning') }}</span>
      <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-900">&times;</button>
    </div>
  @endif

  <!-- PAGE HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Subscription &amp; Usage Overview</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your active seller plan, resource allowances, and billing history.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('subscription-plans') }}" class="px-5 py-2.5 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs border border-pp-200 transition flex items-center gap-1.5">
        <i class="fas fa-rocket text-pp-600"></i> View All Plans &amp; Upgrade
      </a>
    </div>
  </div>

  <!-- CURRENT SUBSCRIPTION CARD -->
  <div class="bg-white rounded-3xl border-2 border-pp-600 p-6 sm:p-8 space-y-6 shadow-soft relative">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-extrabold text-[10px] uppercase tracking-wider">CURRENT ACTIVE PLAN</span>
          <span class="px-2.5 py-0.5 rounded-full {{ $usage['is_paid'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }} font-bold text-[10px] uppercase">
            {{ $usage['is_paid'] ? 'Active Subscription' : 'Free Tier' }}
          </span>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900">{{ $usage['plan_name'] }}</h2>
        <p class="text-xs text-slate-500">
          @if($usage['is_paid'] && $usage['subscription']?->plan)
            @php
              $sub = $usage['subscription'];
              $isAnnual = $sub->starts_at && $sub->ends_at && $sub->starts_at->diffInDays($sub->ends_at) >= 300;
            @endphp
            {{ $isAnnual ? 'Billed annually' : 'Billed monthly' }} · 
            <strong>@money($isAnnual ? $sub->plan->annual_price : $sub->plan->price)</strong>
          @else
            No monthly subscription fees · <strong>₦0 / forever</strong>
          @endif
        </p>
      </div>

      <!-- DATES & RENEW / UPGRADE BUTTONS -->
      <div class="flex flex-col sm:items-end gap-2">
        <div class="text-xs text-slate-600 text-left sm:text-right space-y-0.5">
          <div>Start Date: <strong class="text-slate-900">{{ $usage['starts_at'] ? $usage['starts_at']->format('M d, Y') : now()->startOfMonth()->format('M d, Y') }}</strong></div>
          <div>
            @if($usage['ends_at'])
              Renewal Date: <strong class="text-slate-900">{{ $usage['ends_at']->format('M d, Y') }}</strong>
              <span class="text-pp-600 font-bold">({{ $usage['days_left'] }} days left)</span>
            @else
              Renewal Date: <strong class="text-slate-900">Permanent Free Tier</strong>
            @endif
          </div>
        </div>
        <div class="flex items-center gap-2 pt-1">
          <a href="{{ route('subscription-plans') }}" class="px-4 py-2 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition border border-pp-200">
            Upgrade Plan →
          </a>
          @if($usage['is_paid'])
            <button wire:click="renewSubscription" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
              Renew Subscription
            </button>
          @endif
        </div>
      </div>
    </div>

    <!-- THE 3 DYNAMICALLY DETERMINED SUBSCRIPTION RESOURCES -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-chart-pie text-pp-600"></i> Plan Resource Allowances (Real-Time Dynamic Quotas)
        </h3>
        <span class="text-[10px] text-slate-400 font-bold">Live Verification · Resets Daily</span>
      </div>

      <div class="grid md:grid-cols-3 gap-4">

        <!-- RESOURCE 1: COMMUNITY REQUESTS TODAY -->
        @php
          $reqLimit = max(1, $usage['daily_request_limit']);
          $reqLeft = $usage['daily_requests_remaining'];
          $reqPct = min(100, round(($reqLeft / $reqLimit) * 100));
        @endphp
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-bullhorn text-pp-600"></i> Community Requests Today
            </span>
            <span class="font-extrabold text-pp-700">{{ $reqLeft }} / {{ $reqLimit }} Left</span>
          </div>
          <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
            <div class="bg-pp-600 h-full rounded-full transition-all duration-300" style="width: {{ $reqPct }}%"></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-slate-400">
            <span>{{ $usage['daily_requests_used'] }} used today</span>
            <span>Resets at midnight</span>
          </div>
        </div>

        <!-- RESOURCE 2: COMMUNITY RESPONSES TODAY -->
        @php
          $respLimit = max(1, $usage['daily_response_limit']);
          $respLeft = $usage['daily_responses_remaining'];
          $respPct = min(100, round(($respLeft / $respLimit) * 100));
        @endphp
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-comment-dots text-emerald-600"></i> Quote Responses Today
            </span>
            <span class="font-extrabold text-emerald-700">{{ $respLeft }} / {{ $respLimit }} Left</span>
          </div>
          <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: {{ $respPct }}%"></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-slate-400">
            <span>{{ $usage['daily_responses_used'] }} used today</span>
            <span>Resets at midnight</span>
          </div>
        </div>

        <!-- RESOURCE 3: TOTAL LISTINGS ALLOWANCE ALTOGETHER -->
        @php
          $listLimit = max(1, $usage['listing_limit']);
          $listUsed = $usage['listings_used'];
          $listPct = min(100, round(($listUsed / $listLimit) * 100));
        @endphp
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-boxes text-indigo-600"></i> Total Listings Altogether
            </span>
            <span class="font-extrabold text-slate-900">{{ $listUsed }} / {{ $listLimit }} Used</span>
          </div>
          <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
            <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $listPct }}%"></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-slate-400">
            <span>{{ $usage['listings_remaining'] }} slots available</span>
            <span>All active inventory</span>
          </div>
        </div>

      </div>
    </div>

  </div>

  <!-- SUBSCRIPTION BILLING HISTORY TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-history text-pp-600"></i> Subscription Billing History
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">Past payments and subscription renewal receipts.</p>
      </div>
      <a href="{{ route('subscription-plans') }}" class="text-xs font-bold text-pp-600 hover:underline">View All Plans</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Reference</th>
            <th class="p-3.5">Payment Type</th>
            <th class="p-3.5">Gateway</th>
            <th class="p-3.5">Amount</th>
            <th class="p-3.5">Date</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Receipt</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          @forelse($billingHistory as $payment)
            <tr class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-900">#{{ $payment->reference }}</td>
              <td class="p-3.5 font-extrabold text-pp-700">{{ ucwords(str_replace('_', ' ', $payment->metadata['payment_type'] ?? 'Subscription')) }}</td>
              <td class="p-3.5 text-slate-600 uppercase">{{ $payment->provider ?? 'Gateway' }}</td>
              <td class="p-3.5 font-extrabold text-slate-900">@money($payment->amount, $payment->currency)</td>
              <td class="p-3.5 text-slate-600">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-0.5 rounded-full {{ $payment->status === 'successful' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }} text-[10px] font-extrabold uppercase">
                  {{ $payment->status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <span class="text-slate-400 font-bold"><i class="fas fa-check text-emerald-500 mr-1"></i> Recorded</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="p-6 text-center text-slate-400 text-xs">
                No past subscription payments found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>