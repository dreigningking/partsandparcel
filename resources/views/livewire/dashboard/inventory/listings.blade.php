<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Marketplace Listings Management</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your active storefront listings, stock allocations, and prices.</p>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-plus"></i> Create New Listing
      </button>
    </div>
  </div>

  <!-- LISTINGS TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-store text-pp-600"></i> Active Public Listings (3 Listings)
      </h3>
      <span class="text-xs text-slate-500 font-medium">Sorted by creation date</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Listing Title</th>
            <th class="p-3.5">Category Type</th>
            <th class="p-3.5">Model Series</th>
            <th class="p-3.5 text-right">Price</th>
            <th class="p-3.5">Stock Status</th>
            <th class="p-3.5">Sales Count</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('mylisting.view', ['id' => 'LST-5091']) }}" class="text-pp-600 hover:underline">HP EliteBook 840 G5 Laptop</a>
            </td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold">DEVICE</span></td>
            <td class="p-3.5 text-slate-600">EliteBook 840 G5</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦280,000</td>
            <td class="p-3.5 font-bold text-emerald-600">3 Available</td>
            <td class="p-3.5 text-slate-600">5 Sold</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">ACTIVE</span></td>
            <td class="p-3.5 text-right space-x-2">
              <a href="{{ route('mylisting.view', ['id' => 'LST-5091']) }}" class="px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition">Manage Listing →</a>
            </td>
          </tr>

          <tr class="hover:bg-slate-50/80 transition bg-amber-50/30">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('mylisting.view', ['id' => 'LST-5092']) }}" class="text-pp-600 hover:underline">Dell Latitude 5420 Scrap Unit</a>
            </td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-amber-600 text-white text-[9px] font-extrabold">SCRAP</span></td>
            <td class="p-3.5 text-slate-600">Latitude 5420</td>
            <td class="p-3.5 text-right font-extrabold text-slate-950">₦150,000</td>
            <td class="p-3.5 font-bold text-amber-700">1 Available</td>
            <td class="p-3.5 text-slate-600">0 Sold</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold">RESERVED IN CART</span></td>
            <td class="p-3.5 text-right space-x-2">
              <a href="{{ route('mylisting.view', ['id' => 'LST-5092']) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-[11px] transition">Manage Listing →</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>