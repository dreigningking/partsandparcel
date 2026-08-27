<div class="flex flex-col gap-6">
  
  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Commercial Invoices</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track commercial invoice records, Escrow payment states, and billing history.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">All (6)</button>
      <button class="px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50">Paid &amp; Protected (4)</button>
      <button class="px-3 py-1.5 rounded-lg text-amber-800 hover:bg-amber-50">Pending Escrow (1)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Direct Payment (1)</button>
    </div>
  </div>

  <!-- METRICS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Total Invoice Volume</span>
      <span class="text-2xl font-black text-slate-950">₦1,850,000</span>
      <span class="text-[11px] text-slate-500 block">6 Commercial Invoices</span>
    </div>

    <div class="bg-white rounded-3xl border-2 border-pp-500 p-5 shadow-soft space-y-1">
      <span class="text-xs text-pp-700 font-bold uppercase block flex items-center gap-1">
        <i class="fas fa-shield-alt text-pp-600"></i> Escrow Protected Volume
      </span>
      <span class="text-2xl font-black text-pp-700">₦1,450,000</span>
      <span class="text-[11px] text-emerald-600 font-bold block">100% Escrow dispute protection</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-amber-800 font-bold uppercase block">Direct Seller Invoices</span>
      <span class="text-2xl font-black text-slate-900">₦400,000</span>
      <span class="text-[11px] text-amber-700 font-bold block">Direct bank transfers</span>
    </div>
  </div>

  <!-- INVOICES TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-file-invoice-dollar text-pp-600"></i> Invoice Transaction Records
      </h3>
      <span class="text-xs text-slate-500 font-medium">Sorted by date</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Invoice Ref</th>
            <th class="p-3.5">Related Deal / Offer</th>
            <th class="p-3.5">Buyer / Seller</th>
            <th class="p-3.5 text-right">Amount</th>
            <th class="p-3.5">Fulfillment Path</th>
            <th class="p-3.5">Payment Method</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('invoices.view', ['id' => 'INV-9082']) }}" class="text-pp-600 hover:underline">#INV-9082</a>
            </td>
            <td class="p-3.5 font-bold text-slate-900">HP EliteBook 840 G5 Package</td>
            <td class="p-3.5 text-slate-600">TechSam (Buyer)</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦265,000</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-pp-100 text-pp-800 text-[10px] font-bold">Seller Delivery</span></td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Escrow Protected</span></td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">PAID</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('invoices.view', ['id' => 'INV-9082']) }}" class="px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition">View Invoice →</a>
            </td>
          </tr>

          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('invoices.view', ['id' => 'INV-9081']) }}" class="text-pp-600 hover:underline">#INV-9081</a>
            </td>
            <td class="p-3.5 font-bold text-slate-900">Dell Latitude 5420 Scrap Unit</td>
            <td class="p-3.5 text-slate-600">Abel Tech Parts (Buyer)</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦150,000</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold">Self-Pickup</span></td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Escrow Protected</span></td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">PAID</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('invoices.view', ['id' => 'INV-9081']) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-[11px] transition">View Invoice →</a>
            </td>
          </tr>

          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('invoices.view', ['id' => 'INV-9079']) }}" class="text-pp-600 hover:underline">#INV-9079</a>
            </td>
            <td class="p-3.5 font-bold text-slate-900">Dell Latitude Motherboard (Tested)</td>
            <td class="p-3.5 text-slate-600">Abel Electronics (Seller)</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦85,000</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Community Rider</span></td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-bold">Direct Seller Transfer</span></td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold">PENDING SELLER CONFIRM</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('invoices.view', ['id' => 'INV-9079']) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-[11px] transition">View Invoice →</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>