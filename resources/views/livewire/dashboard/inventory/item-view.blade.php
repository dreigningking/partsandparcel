<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myitems') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Inventory
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Item Ref: #ITM-4081</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>HP EliteBook 840 G5 Scrap Laptop</span>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
          DISASSEMBLY MATRIX ACTIVE
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Scrap unit harvested for tested working components</p>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
        + Add Harvested Part to Listing
      </button>
    </div>
  </div>

  <!-- DISASSEMBLY MATRIX TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
      <i class="fas fa-microchip text-pp-600"></i> Harvested Component Status &amp; Availability Matrix
    </h3>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Component Part Name</th>
            <th class="p-3.5">Condition</th>
            <th class="p-3.5">Stock Status</th>
            <th class="p-3.5">Harvested Notes</th>
            <th class="p-3.5 text-right">Individual Price</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr>
            <td class="p-3.5 font-bold text-slate-900">Motherboard (Core i5 8th Gen)</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">TESTED WORKING</span></td>
            <td class="p-3.5 font-bold text-emerald-600">Available (1)</td>
            <td class="p-3.5 text-slate-500">Clean board, boots to BIOS cleanly</td>
            <td class="p-3.5 text-right font-black text-slate-950">₦80,000</td>
          </tr>
          <tr>
            <td class="p-3.5 font-bold text-slate-900">16GB DDR4 RAM Module</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">TESTED WORKING</span></td>
            <td class="p-3.5 font-bold text-emerald-600">Available (1)</td>
            <td class="p-3.5 text-slate-500">Dual-channel module, 2666MHz</td>
            <td class="p-3.5 text-right font-black text-slate-950">₦10,000</td>
          </tr>
          <tr>
            <td class="p-3.5 font-bold text-slate-900">50Wh Original Battery</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-extrabold">WEAK CHARGE</span></td>
            <td class="p-3.5 font-bold text-amber-700">Available (1)</td>
            <td class="p-3.5 text-slate-500">Holds 65% health capacity</td>
            <td class="p-3.5 text-right font-black text-slate-950">₦15,000</td>
          </tr>
          <tr>
            <td class="p-3.5 font-bold text-slate-900">14.0" FHD IPS Screen</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-extrabold">DAMAGED</span></td>
            <td class="p-3.5 text-slate-400">Not Available</td>
            <td class="p-3.5 text-slate-400">Cracked display panel</td>
            <td class="p-3.5 text-right font-bold text-slate-400">N/A</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>