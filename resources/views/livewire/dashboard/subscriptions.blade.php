<div class="flex flex-col gap-6">

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
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px] uppercase">Active</span>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900">Pro Seller Plan</h2>
        <p class="text-xs text-slate-500">Billed monthly · <strong>₦15,000 / month</strong></p>
      </div>

      <!-- DATES & RENEW / UPGRADE BUTTONS -->
      <div class="flex flex-col sm:items-end gap-2">
        <div class="text-xs text-slate-600 text-left sm:text-right space-y-0.5">
          <div>Start Date: <strong class="text-slate-900">Aug 01, 2026</strong></div>
          <div>Renewal Date: <strong class="text-slate-900">Aug 31, 2026</strong> <span class="text-pp-600 font-bold">(14 days left)</span></div>
        </div>
        <div class="flex items-center gap-2 pt-1">
          <a href="{{ route('subscription-plans') }}" class="px-4 py-2 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition border border-pp-200">
            Upgrade Plan →
          </a>
          <button class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
            Renew Subscription
          </button>
        </div>
      </div>
    </div>

    <!-- REMAINING SUBSCRIPTION RESOURCES -->
    <div class="space-y-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-chart-bar text-pp-600"></i> Remaining Subscription Resources
      </h3>

      <div class="grid md:grid-cols-3 gap-4">
        <!-- RESOURCE 1: COMMUNITY RESPONSES -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-1.5"><i class="fas fa-comment-dots text-pp-600"></i> Community Responses</span>
            <span class="font-extrabold text-pp-700">142 / 200 Left</span>
          </div>
          <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
            <div class="bg-pp-600 h-full rounded-full" style="width: 71%"></div>
          </div>
          <p class="text-[10px] text-slate-400">Resets on Aug 31, 2026</p>
        </div>

        <!-- RESOURCE 2: ACTIVE LISTINGS ALLOWANCE -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-1.5"><i class="fas fa-boxes text-pp-600"></i> Active Listings</span>
            <span class="font-extrabold text-slate-900">48 / 100 Used</span>
          </div>
          <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full" style="width: 48%"></div>
          </div>
          <p class="text-[10px] text-slate-400">52 slots remaining</p>
        </div>

        <!-- RESOURCE 3: DISASSEMBLY MATRIX TOOL -->
        <div class="p-4 rounded-2xl bg-pp-50/60 border border-pp-200/80 space-y-2">
          <div class="flex justify-between items-center text-xs">
            <span class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fas fa-microchip text-pp-600"></i> Item Disassembly Matrix</span>
            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">UNLOCKED</span>
          </div>
          <p class="text-[11px] text-slate-600 leading-snug">Full access to inventory disassembly &amp; component lifecycle tracking tools.</p>
        </div>
      </div>
    </div>

  </div>

  <!-- SUBSCRIPTION HISTORY TABLE -->
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
            <th class="p-3.5">Invoice Ref</th>
            <th class="p-3.5">Plan Name</th>
            <th class="p-3.5">Billing Cycle</th>
            <th class="p-3.5">Amount</th>
            <th class="p-3.5">Start Date</th>
            <th class="p-3.5">End Date</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Receipt</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">#SUB-10892</td>
            <td class="p-3.5 font-extrabold text-pp-700">Pro Seller Plan</td>
            <td class="p-3.5 text-slate-600">Monthly</td>
            <td class="p-3.5 font-extrabold text-slate-900">₦15,000</td>
            <td class="p-3.5 text-slate-600">Aug 01, 2026</td>
            <td class="p-3.5 text-slate-600">Aug 31, 2026</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">ACTIVE</span></td>
            <td class="p-3.5 text-right">
              <button class="text-pp-600 font-bold hover:underline cursor-pointer"><i class="fas fa-download mr-1"></i> Download</button>
            </td>
          </tr>
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">#SUB-10421</td>
            <td class="p-3.5 font-extrabold text-slate-700">Pro Seller Plan</td>
            <td class="p-3.5 text-slate-600">Monthly</td>
            <td class="p-3.5 font-extrabold text-slate-900">₦15,000</td>
            <td class="p-3.5 text-slate-600">Jul 01, 2026</td>
            <td class="p-3.5 text-slate-600">Jul 31, 2026</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">COMPLETED</span></td>
            <td class="p-3.5 text-right">
              <button class="text-pp-600 font-bold hover:underline cursor-pointer"><i class="fas fa-download mr-1"></i> Download</button>
            </td>
          </tr>
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">#SUB-10012</td>
            <td class="p-3.5 font-extrabold text-slate-700">Free Tier (Starter)</td>
            <td class="p-3.5 text-slate-600">Monthly</td>
            <td class="p-3.5 font-extrabold text-slate-900">₦0</td>
            <td class="p-3.5 text-slate-600">Jun 01, 2026</td>
            <td class="p-3.5 text-slate-600">Jun 30, 2026</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">COMPLETED</span></td>
            <td class="p-3.5 text-right">
              <button class="text-pp-600 font-bold hover:underline cursor-pointer"><i class="fas fa-download mr-1"></i> Download</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>