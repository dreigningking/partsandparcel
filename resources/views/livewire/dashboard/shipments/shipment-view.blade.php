<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('shipments') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Shipments
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Waybill #WAY-8842</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Parcel Tracking &amp; Delivery Progress</span>
        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase">
          IN TRANSIT
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Seller Direct Dispatch for Invoice #INV-9082</p>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-3 py-1.5 rounded-xl bg-pp-50 text-pp-700 border border-pp-200 text-xs font-extrabold">
        Estimated Delivery: Today by 4:30 PM
      </span>
    </div>
  </div>

  <!-- STEPPER TIMELINE -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
      <i class="fas fa-route text-pp-600"></i> Dispatch Progress Timeline
    </h3>

    <div class="grid grid-cols-4 gap-2 text-center text-xs relative">
      <div class="space-y-1">
        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold grid place-items-center mx-auto">✓</div>
        <span class="font-bold text-slate-900 block">Order Packed</span>
        <span class="text-[10px] text-slate-400">10:00 AM</span>
      </div>

      <div class="space-y-1">
        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-extrabold grid place-items-center mx-auto">✓</div>
        <span class="font-bold text-slate-900 block">Rider Dispatched</span>
        <span class="text-[10px] text-slate-400">11:30 AM</span>
      </div>

      <div class="space-y-1">
        <div class="w-8 h-8 rounded-full bg-pp-600 text-white font-extrabold grid place-items-center mx-auto">3</div>
        <span class="font-bold text-pp-700 block">In Transit (Third Mainland)</span>
        <span class="text-[10px] text-pp-600 font-bold">En Route</span>
      </div>

      <div class="space-y-1 opacity-50">
        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 font-bold grid place-items-center mx-auto">4</div>
        <span class="font-bold text-slate-700 block">Delivered &amp; Confirmed</span>
        <span class="text-[10px] text-slate-400">Pending</span>
      </div>
    </div>
  </div>

  <!-- SHIPMENT DETAILS & RIDER INFO -->
  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
        Parcel Contents &amp; Destination
      </h3>
      <div class="space-y-2 text-xs text-slate-700">
        <div><strong>Contents:</strong> HP EliteBook 840 G5 Laptop + 16GB RAM</div>
        <div><strong>Sender:</strong> Adam Computers (Computer Village, Ikeja)</div>
        <div><strong>Destination:</strong> TechSam, 45 Admiralty Way, Lekki Phase 1, Lagos</div>
      </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
        Assigned Delivery Courier / Rider
      </h3>
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-pp-100 text-pp-700 font-extrabold text-base grid place-items-center">
          <i class="fas fa-motorcycle"></i>
        </div>
        <div>
          <h4 class="font-extrabold text-slate-900 text-sm">GIG Express Dispatch Rider</h4>
          <p class="text-xs text-slate-500">Rider: Tunde Bakare (0803 123 4567)</p>
        </div>
      </div>
    </div>
  </div>

</div>