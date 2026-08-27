<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Saved Wishlist Items &amp; Parts</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track saved parts, devices, and scraped components for upcoming purchases.</p>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-3 py-1.5 rounded-xl bg-pp-50 text-pp-700 font-extrabold text-xs border border-pp-200">
        3 Saved Items
      </span>
    </div>
  </div>

  <!-- WISHLIST CARDS GRID -->
  <div class="grid sm:grid-cols-3 gap-5">
    
    <!-- WISHLIST ITEM 1 -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[9px] font-bold">DEVICE</span>
          <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">IN STOCK</span>
        </div>

        <div class="w-full h-32 rounded-2xl bg-slate-100 grid place-items-center text-4xl">
          💻
        </div>

        <div>
          <h3 class="text-sm font-extrabold text-slate-900">HP EliteBook 840 G5 Laptop</h3>
          <p class="text-xs text-pp-700 font-extrabold mt-0.5">₦280,000</p>
          <p class="text-[11px] text-slate-500 mt-1">Seller: Adam Computers (Computer Village)</p>
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <a href="{{ route('checkout') }}?seller=adam" class="flex-1 py-2 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-2xs transition">
          Buy Now →
        </a>
        <button class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Remove">
          <i class="fas fa-trash-alt text-xs"></i>
        </button>
      </div>
    </div>

    <!-- WISHLIST ITEM 2 -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[9px] font-bold">PART</span>
          <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">1 AVAILABLE</span>
        </div>

        <div class="w-full h-32 rounded-2xl bg-pp-50 grid place-items-center text-4xl">
          ⚡
        </div>

        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Dell Latitude 5420 Motherboard (Tested)</h3>
          <p class="text-xs text-pp-700 font-extrabold mt-0.5">₦85,000</p>
          <p class="text-[11px] text-slate-500 mt-1">Seller: Abel Electronics (Ikeja)</p>
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <a href="{{ route('checkout') }}?seller=abel" class="flex-1 py-2 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-2xs transition">
          Buy Now →
        </a>
        <button class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Remove">
          <i class="fas fa-trash-alt text-xs"></i>
        </button>
      </div>
    </div>

    <!-- WISHLIST ITEM 3 -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="px-2 py-0.5 rounded bg-amber-600 text-white text-[9px] font-extrabold">SCRAP</span>
          <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-extrabold">PRICE DROP</span>
        </div>

        <div class="w-full h-32 rounded-2xl bg-amber-50 grid place-items-center text-4xl">
          🛠️
        </div>

        <div>
          <h3 class="text-sm font-extrabold text-slate-900">HP EliteBook 840 G5 Scrap Battery</h3>
          <p class="text-xs text-pp-700 font-extrabold mt-0.5">₦25,000</p>
          <p class="text-[11px] text-slate-500 mt-1">Seller: Seth Repair Yard (Oregun)</p>
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <a href="{{ route('checkout') }}?seller=seth" class="flex-1 py-2 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-2xs transition">
          Buy Now →
        </a>
        <button class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Remove">
          <i class="fas fa-trash-alt text-xs"></i>
        </button>
      </div>
    </div>

  </div>

</div>