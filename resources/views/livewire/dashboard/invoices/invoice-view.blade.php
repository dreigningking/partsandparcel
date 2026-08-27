<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('invoices') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Invoices
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Commercial Record</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Commercial Invoice #INV-9082</span>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase">
          ✓ PAID &amp; ESCROW PROTECTED
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Commercial agreement resulting from accepted Offer #OFF-9021</p>
    </div>

    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-print"></i> Print Invoice
      </button>
      <button class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-download"></i> Download Receipt
      </button>
    </div>
  </div>

  <!-- INVOICE CARD -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    
    <!-- INVOICE META -->
    <div class="flex flex-col sm:flex-row justify-between gap-6 border-b border-slate-100 pb-6 text-xs">
      <div class="space-y-1">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Seller / Provider Details</span>
        <h3 class="text-base font-extrabold text-slate-900">Adam Computers Ltd</h3>
        <p class="text-slate-600">Computer Village, Ikeja, Lagos</p>
        <p class="text-slate-500">Bank: GTBank · Acc: 0123456789</p>
      </div>

      <div class="space-y-1 text-left sm:text-right">
        <span class="text-slate-400 font-bold uppercase text-[10px]">Customer Details</span>
        <h3 class="text-base font-extrabold text-slate-900">TechSam (Samuel Ike)</h3>
        <p class="text-slate-600">Lekki Phase 1, Lagos</p>
        <p class="text-slate-500">Payment: Parts &amp; Parcel Escrow (Card / Transfer)</p>
      </div>
    </div>

    <!-- LINE ITEMS TABLE -->
    <div class="space-y-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-list-ul text-pp-600"></i> Agreed Commercial Line Items
      </h3>

      <div class="overflow-x-auto rounded-2xl border border-slate-200">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
            <tr>
              <th class="p-3">Item / Service Description</th>
              <th class="p-3">Type</th>
              <th class="p-3 text-center">Qty</th>
              <th class="p-3 text-right">Unit Price</th>
              <th class="p-3">Agreed Warranty</th>
              <th class="p-3 text-right">Total Price</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
            <tr>
              <td class="p-3 font-bold text-slate-900">HP EliteBook 840 G5 Laptop</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold">ITEM</span></td>
              <td class="p-3 text-center font-bold">1</td>
              <td class="p-3 text-right font-extrabold">₦250,000</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">14 DAYS</span></td>
              <td class="p-3 text-right font-black text-slate-950">₦250,000</td>
            </tr>
            <tr>
              <td class="p-3 font-bold text-slate-900">16GB DDR4 RAM Upgrade Service</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-pp-100 text-pp-800 text-[9px] font-bold">SERVICE</span></td>
              <td class="p-3 text-center font-bold">1</td>
              <td class="p-3 text-right font-extrabold">₦10,000</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">14 DAYS</span></td>
              <td class="p-3 text-right font-black text-slate-950">₦10,000</td>
            </tr>
            <tr>
              <td class="p-3 font-bold text-slate-900">Express Seller Delivery to Lekki Phase 1</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">DELIVERY</span></td>
              <td class="p-3 text-center font-bold">1</td>
              <td class="p-3 text-right font-extrabold">₦10,000</td>
              <td class="p-3 text-slate-400">N/A</td>
              <td class="p-3 text-right font-black text-slate-950">₦10,000</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TOTALS & ESCROW PROTECTION -->
    <div class="grid sm:grid-cols-2 gap-6 pt-3 border-t border-slate-100 text-xs">
      <div class="p-4 rounded-2xl bg-pp-50 border border-pp-200 space-y-2">
        <b class="text-pp-900 font-bold flex items-center gap-1.5"><i class="fas fa-shield-alt text-pp-600"></i> Escrow Protection Terms:</b>
        <p class="text-slate-700 leading-relaxed">
          Funds are held securely by Parts &amp; Parcel until the buyer receives and verifies the package within the 14-day warranty period.
        </p>
      </div>

      <div class="space-y-1.5 text-right">
        <div class="flex justify-between text-slate-600"><span>Subtotal:</span><span>₦270,000</span></div>
        <div class="flex justify-between text-pp-700 font-bold"><span>Agreed Discount:</span><span>-₦5,000</span></div>
        <div class="flex justify-between text-slate-600"><span>Escrow Fee (+):</span><span>₦1,500</span></div>
        <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline text-base">
          <span class="font-bold text-slate-900">Total Paid:</span>
          <span class="text-2xl font-black text-slate-950">₦266,500</span>
        </div>
      </div>
    </div>

  </div>

</div>