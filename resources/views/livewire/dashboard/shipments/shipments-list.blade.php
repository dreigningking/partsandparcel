<div class="flex flex-col gap-6">
  
  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Logistics &amp; Dispatch Center</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track package shipments, community errand dispatches, and local rider progress.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button class="px-3 py-1.5 rounded-lg bg-pp-600 text-white">Active (3)</button>
      <button class="px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50">Delivered (28)</button>
      <button class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">Self-Pickups (12)</button>
    </div>
  </div>

  <!-- METRICS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Active In-Transit Dispatches</span>
      <span class="text-2xl font-black text-slate-950">3 Shipments</span>
      <span class="text-[11px] text-slate-500 block">2 Seller Direct · 1 Community Rider</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-emerald-700 font-bold uppercase block">Completed Deliveries</span>
      <span class="text-2xl font-black text-emerald-700">28 Parcels</span>
      <span class="text-[11px] text-slate-500 block">100% verified fulfillment rate</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Avg Dispatch Speed</span>
      <span class="text-2xl font-black text-slate-900">1.2 Hours</span>
      <span class="text-[11px] text-pp-600 font-bold block">Same-day Lagos delivery</span>
    </div>
  </div>

  <!-- SHIPMENTS TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-truck text-pp-600"></i> Dispatch &amp; Waybill Tracking
      </h3>
      <span class="text-xs text-slate-500 font-medium">Updated live</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase font-extrabold border-b border-slate-200">
          <tr>
            <th class="p-3.5">Waybill / Tracking</th>
            <th class="p-3.5">Related Invoice</th>
            <th class="p-3.5">Origin &amp; Destination</th>
            <th class="p-3.5">Fulfillment Method</th>
            <th class="p-3.5">Estimated Arrival</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('shipments.view', ['id' => 'WAY-8842']) }}" class="text-pp-600 hover:underline">#WAY-8842</a>
            </td>
            <td class="p-3.5 text-slate-600">#INV-9082</td>
            <td class="p-3.5 text-slate-900 font-bold">Ikeja → Lekki Phase 1</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-pp-100 text-pp-800 text-[10px] font-bold">Seller Direct Courier</span></td>
            <td class="p-3.5 text-slate-600">Today, 4:30 PM</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold">IN TRANSIT</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('shipments.view', ['id' => 'WAY-8842']) }}" class="px-3 py-1.5 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[11px] transition">Track Package →</a>
            </td>
          </tr>

          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3.5 font-bold text-slate-900">
              <a href="{{ route('shipments.view', ['id' => 'WAY-8840']) }}" class="text-pp-600 hover:underline">#WAY-8840</a>
            </td>
            <td class="p-3.5 text-slate-600">#INV-9079</td>
            <td class="p-3.5 text-slate-900 font-bold">Computer Village → Yaba</td>
            <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Community Rider</span></td>
            <td class="p-3.5 text-slate-600">Delivered Yesterday</td>
            <td class="p-3.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">DELIVERED</span></td>
            <td class="p-3.5 text-right">
              <a href="{{ route('shipments.view', ['id' => 'WAY-8840']) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-[11px] transition">View Details →</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>