<div class="flex flex-col gap-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('mylistings') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Listings
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Listing Ref: #LST-5091</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>HP EliteBook 840 G5 Laptop</span>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] uppercase">
          PUBLIC &amp; ACTIVE
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">Device Listing · 3 Units Available · 342 Public Views</p>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
        Edit Listing
      </button>
    </div>
  </div>

  <!-- LISTING METRICS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Listed Unit Price</span>
      <span class="text-2xl font-black text-slate-950">₦280,000</span>
      <span class="text-[11px] text-pp-600 font-bold block">Negotiable via Package Offers</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Total Sales</span>
      <span class="text-2xl font-black text-slate-900">5 Units Sold</span>
      <span class="text-[11px] text-emerald-600 font-bold block">₦1,400,000 Gross Revenue</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-soft space-y-1">
      <span class="text-xs text-slate-400 font-bold uppercase block">Active Impressions</span>
      <span class="text-2xl font-black text-slate-900">342 Views</span>
      <span class="text-[11px] text-slate-500 block">18 Wishlist Saves</span>
    </div>
  </div>

</div>