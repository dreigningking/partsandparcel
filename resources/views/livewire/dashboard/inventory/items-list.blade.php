<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Registered Inventory &amp; Disassembly Matrix</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track whole devices, harvested components, and inventory disassembly statuses.</p>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-plus"></i> Register New Item / Device
      </button>
    </div>
  </div>

  <!-- INVENTORY TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-boxes text-pp-600"></i> Registered Items Stream (3 Items)
      </h3>
      <span class="text-xs text-slate-500 font-medium">Sorted by date</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Item Name / Title</th>
            <th class="p-3.5">Category Type</th>
            <th class="p-3.5">Model / Series</th>
            <th class="p-3.5 text-right">Standard Price</th>
            <th class="p-3.5">Available Stock</th>
            <th class="p-3.5">Disassembly Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('item.view', ['id' => 'ITM-4081']) }}" class="text-pp-600 hover:underline">HP EliteBook 840 G5 Scrap Laptop</a>
            </td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-amber-600 text-white text-[9px] font-extrabold">SCRAP</span></td>
            <td class="p-3.5 text-slate-600">EliteBook 840 G5</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦150,000</td>
            <td class="p-3.5 font-bold text-emerald-600">1 Unit</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">HARVESTED (4 PARTS)</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('item.view', ['id' => 'ITM-4081']) }}" class="px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition">View Disassembly Matrix →</a>
            </td>
          </tr>

          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('item.view', ['id' => 'ITM-4082']) }}" class="text-pp-600 hover:underline">Dell Latitude 5420 Motherboard (Tested)</a>
            </td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[9px] font-bold">PART</span></td>
            <td class="p-3.5 text-slate-600">Latitude 5420</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦85,000</td>
            <td class="p-3.5 font-bold text-emerald-600">2 Units</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold">TESTED WORKING</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('item.view', ['id' => 'ITM-4082']) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-[11px] transition">View Details →</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>