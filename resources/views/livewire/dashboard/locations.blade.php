<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Saved Delivery &amp; Pickup Locations</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your saved shop, workshop, warehouse, and delivery destination addresses.</p>
    </div>

    <button class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
      <i class="fas fa-plus"></i> Add New Location
    </button>
  </div>

  <!-- LOCATIONS GRID -->
  <div class="grid md:grid-cols-3 gap-5">
    
    <!-- LOCATION 1: PRIMARY WORKSHOP -->
    <div class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] uppercase">PRIMARY STORE / SHOP</span>
          <span class="text-xs font-bold text-pp-600">✓ Default</span>
        </div>

        <div>
          <h3 class="text-base font-extrabold text-slate-900">Computer Village Workshop</h3>
          <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1"><i class="fas fa-map-marker-alt text-pp-600"></i> Ikeja, Lagos</p>
        </div>

        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
          Plaza 3, Shop 14, Computer Village, Medical Road, Ikeja, Lagos State.
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
        <button class="text-pp-600 hover:underline cursor-pointer">Edit Details</button>
        <span class="text-slate-400">Primary Pickup Point</span>
      </div>
    </div>

    <!-- LOCATION 2: RESIDENTIAL -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] uppercase">RESIDENTIAL / HOME</span>
        </div>

        <div>
          <h3 class="text-base font-extrabold text-slate-900">Home Residence</h3>
          <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1"><i class="fas fa-map-marker-alt text-slate-400"></i> Ikeja, Lagos</p>
        </div>

        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
          12 Allen Avenue, Off Toyin Street, Ikeja, Lagos State.
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
        <button class="text-pp-600 hover:underline cursor-pointer">Edit</button>
        <button class="text-slate-600 hover:text-pp-600 cursor-pointer">Set as Default</button>
      </div>
    </div>

    <!-- LOCATION 3: LEKKI WAREHOUSE -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] uppercase">WAREHOUSE / DISPATCH</span>
        </div>

        <div>
          <h3 class="text-base font-extrabold text-slate-900">Lekki Dispatch Point</h3>
          <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1"><i class="fas fa-map-marker-alt text-slate-400"></i> Lekki Phase 1, Lagos</p>
        </div>

        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
          45 Admiralty Way, Lekki Phase 1, Lagos Island, Lagos State.
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
        <button class="text-pp-600 hover:underline cursor-pointer">Edit</button>
        <button class="text-slate-600 hover:text-pp-600 cursor-pointer">Set as Default</button>
      </div>
    </div>

  </div>

</div>