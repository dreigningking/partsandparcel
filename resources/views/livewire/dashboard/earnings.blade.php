<div class="flex flex-col gap-6">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Payouts &amp; Earnings Wallet</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your settlement earnings, payout history, and connected bank accounts.</p>
    </div>

    <button class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-2">
      <i class="fas fa-wallet"></i>
      <span>Request Instant Withdrawal (₦850,000)</span>
    </button>
  </div>

  <!-- WALLET METRICS -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    
    <div class="bg-white rounded-3xl border-2 border-pp-500 p-6 shadow-soft space-y-1">
      <span class="text-xs font-bold text-slate-400 uppercase block">Available Balance</span>
      <span class="text-3xl font-black text-pp-600 mt-1 block">₦850,000</span>
      <span class="text-[11px] text-emerald-600 font-bold block mt-2">Ready for withdrawal to GTBank</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-1">
      <span class="text-xs font-bold text-slate-400 uppercase block">Pending Escrow Settlement</span>
      <span class="text-3xl font-black text-amber-700 mt-1 block">₦280,000</span>
      <span class="text-[11px] text-slate-500 block mt-2">Releases upon delivery confirmation</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-soft space-y-1">
      <span class="text-xs font-bold text-slate-400 uppercase block">Total Paid Out</span>
      <span class="text-3xl font-black text-slate-900 mt-1 block">₦2,320,000</span>
      <span class="text-[11px] text-slate-500 block mt-2">14 successful settlements</span>
    </div>

  </div>

  <!-- PAYOUT HISTORY TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
      <i class="fas fa-history text-pp-600"></i> Payout Settlement History
    </h3>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Payout Reference</th>
            <th class="p-3.5">Amount</th>
            <th class="p-3.5">Bank Destination</th>
            <th class="p-3.5">Date</th>
            <th class="p-3.5">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          <tr>
            <td class="p-3.5 font-bold text-slate-900">#PP-10231</td>
            <td class="p-3.5 font-extrabold text-slate-900">₦850,000</td>
            <td class="p-3.5 text-slate-600">GTBank (0123456789)</td>
            <td class="p-3.5 text-slate-500">Aug 15, 2026</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">SUCCESSFUL</span></td>
          </tr>
          <tr>
            <td class="p-3.5 font-bold text-slate-900">#PP-10190</td>
            <td class="p-3.5 font-extrabold text-slate-900">₦620,000</td>
            <td class="p-3.5 text-slate-600">GTBank (0123456789)</td>
            <td class="p-3.5 text-slate-500">Aug 01, 2026</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">SUCCESSFUL</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>