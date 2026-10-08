
<!-- HERO SECTION - CLEAN LIGHT BACKGROUND WITH COMPOSITE COLLAGE -->
<div>
  <section class="hero-section bg-gradient-to-b from-slate-50 via-white to-slate-50 border-b border-slate-200/80 py-10 lg:py-16 relative overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        
        <!-- LEFT TEXT & ACTION COLUMN -->
        <div class="lg:col-span-6 space-y-6">
          
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-950 tracking-tight leading-[1.15]">
            Find the <span class="text-pp-600">machine.</span><br />
            Find the <span class="text-pp-600">part.</span><br />
            Find who <span class="text-pp-600">has it.</span>
          </h1>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-medium max-w-xl">
            Buy complete devices, replacement parts, or damaged machines for salvage across Nigeria. Connect directly with technicians, repair shops &amp; verified dealers.
          </p>

          <!-- STATS BADGES ROW -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3 border-y border-slate-200/60 text-xs">
            <div class="flex items-center gap-2">
              <span class="text-base">👥</span>
              <div>
                <p class="font-extrabold text-slate-900">50K+</p>
                <p class="text-[11px] text-slate-500">Active Listings</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-base">😊</span>
              <div>
                <p class="font-extrabold text-slate-900">20K+</p>
                <p class="text-[11px] text-slate-500">Happy Buyers</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-base">✅</span>
              <div>
                <p class="font-extrabold text-slate-900">5K+</p>
                <p class="text-[11px] text-slate-500">Verified Sellers</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-base">🔒</span>
              <div>
                <p class="font-extrabold text-slate-900">100%</p>
                <p class="text-[11px] text-slate-500">Secure Payments</p>
              </div>
            </div>
          </div>

          <!-- ACTION BUTTONS -->
          <div class="flex flex-wrap items-center gap-3 pt-1">
            <a href="{{route('category')}}" class="px-7 py-3.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
              <span>Browse Marketplace</span>
              <span>→</span>
            </a>
            <a href="{{ route('community') }}" class="px-6 py-3.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-900 font-bold text-xs shadow-xs transition hover:bg-slate-50">
              Post a Request in Community
            </a>
          </div>

        </div>

        <!-- RIGHT COLLAGE IMAGE & POPULAR SEARCHES CARD -->
        <div class="lg:col-span-6 relative">
          <div class="relative rounded-3xl overflow-hidden shadow-card border border-slate-200/60 bg-white p-2">
            <img src="/images/collage.png" alt="Car, Crane, Caterpillar, Motherboard, Phone and Machine Parts" class="w-full h-auto object-cover rounded-2xl" />
          </div>

          <!-- POPULAR SEARCHES FLOATING CARD -->
          <div class="mt-4 sm:absolute sm:-bottom-20 sm:right-4 bg-white/95 backdrop-blur border border-slate-200 p-4 rounded-2xl shadow-xl max-w-sm">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Popular Searches</p>
            <div class="flex flex-wrap gap-1.5 text-xs">
              <a href="category.html?q=HP+Battery" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-pp-50 hover:text-pp-600 text-slate-700 font-semibold transition">HP Battery</a>
              <a href="category.html?q=Corolla+Gearbox" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-pp-50 hover:text-pp-600 text-slate-700 font-semibold transition">Corolla Gearbox</a>
              <a href="category.html?q=iPhone+Screen" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-pp-50 hover:text-pp-600 text-slate-700 font-semibold transition">iPhone Screen</a>
              <a href="category.html?q=Excavator+Pump" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-pp-50 hover:text-pp-600 text-slate-700 font-semibold transition">Excavator Pump</a>
              <a href="category.html?q=Dell+Motherboard" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-pp-50 hover:text-pp-600 text-slate-700 font-semibold transition">Dell Motherboard</a>
              <a href="category.html?tab=scrap" class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold transition">Damaged Laptop (Scrap)</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 4 INTENTS EXPLAINER SECTION -->
  <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <h2 class="text-xs font-extrabold uppercase tracking-widest text-pp-600">The Parts &amp; Parcel Way</h2>
      <p class="text-2xl sm:text-3xl font-extrabold text-slate-950 mt-1">4 Ways to Find What You Need</p>
      <p class="text-sm text-slate-500 mt-2">Whether you need a complete car, an individual laptop screen, or a damaged machine to harvest parts from.</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-pp-300 hover:shadow-hover transition duration-200 group cursor-default">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-pp-600 grid place-items-center text-2xl mb-4 group-hover:scale-110 transition duration-200">💻</div>
        <h3 class="text-lg font-bold text-slate-900">1. Complete Devices</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Working laptops, phones, cars, washing machines &amp; equipment ready for immediate use.</p>
      </div>

      <div class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-pp-300 hover:shadow-hover transition duration-200 group cursor-default">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 grid place-items-center text-2xl mb-4 group-hover:scale-110 transition duration-200">⚙️</div>
        <h3 class="text-lg font-bold text-slate-900">2. Spare Parts</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Original batteries, screens, engines, gearboxes, RAM &amp; hard-to-find replacement components.</p>
      </div>

      <div class="rounded-2xl bg-white border border-amber-200 p-6 hover:border-amber-400 hover:shadow-hover transition duration-200 group cursor-default bg-amber-50/30">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 grid place-items-center text-2xl mb-4 group-hover:scale-110 transition duration-200">🛠️</div>
        <h3 class="text-lg font-bold text-slate-900">3. Scrap &amp; Salvage</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Damaged or broken units sold cheap for technicians to harvest functional components.</p>
      </div>

      <div class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-pp-300 hover:shadow-hover transition duration-200 group cursor-default">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 grid place-items-center text-2xl mb-4 group-hover:scale-110 transition duration-200">💬</div>
        <h3 class="text-lg font-bold text-slate-900">4. Community Forum</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Can't find a part? Post a request to thousands of local technicians and dealers.</p>
      </div>
    </div>
  </section>

  <!-- POPULAR MARKETPLACE LISTINGS GRID (DYNAMIC PROMOTIONS) -->
  <livewire:components.promotions.home-page-promotion />

  <!-- DUAL ACTION HUB: SELLER & TECHNICIAN CTA CARDS -->
  <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid md:grid-cols-2 gap-6">
      
      <!-- CARD 1: SELLER PORTAL -->
      <div class="rounded-3xl bg-gradient-to-br from-pp-900 via-indigo-950 to-slate-950 text-white p-7 sm:p-9 shadow-soft relative overflow-hidden flex flex-col justify-between border border-pp-800/50 min-h-[300px]">
        <div class="relative z-10 max-w-sm">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pp-500/30 text-pp-200 text-[11px] font-extrabold uppercase tracking-wider mb-3">
            <span>⚡ SELLER PORTAL</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight text-white">Do you have something to sell?</h2>
          <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed font-medium">
            List complete devices, spare parts, or damaged machines for salvage to thousands of verified buyers and repair shops across Nigeria.
          </p>
        </div>

        <div class="mt-6 pt-4 relative z-10">
          <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-pp-600 hover:bg-pp-500 text-white font-extrabold text-xs shadow-md transition transform hover:-translate-y-0.5">
            <span>Start Selling Now</span>
            <span>→</span>
          </a>
        </div>

        <!-- BOX WITH MACHINE PARTS IMAGE -->
        <div class="absolute -right-4 -bottom-4 w-44 sm:w-56 h-44 sm:h-56 opacity-90 pointer-events-none">
          <img src="/images/box.png" alt="Box with machine parts" class="w-full h-full object-cover rounded-2xl" />
        </div>
      </div>

      <!-- CARD 2: TECHNICIAN & REPAIR NETWORK -->
      <div class="rounded-3xl bg-gradient-to-br from-emerald-950 via-slate-900 to-emerald-900 text-white p-7 sm:p-9 shadow-soft relative overflow-hidden flex flex-col justify-between border border-emerald-900/50 min-h-[300px]">
        <div class="relative z-10 max-w-sm">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-extrabold uppercase tracking-wider mb-3">
            <span>🔧 REPAIR &amp; HELP NETWORK</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight text-white">Can't find what you need?</h2>
          <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed font-medium">
            Post a request and let sellers, repairers, installers, suppliers and other members of the community help you find it.
          </p>
        </div>

        <div class="mt-6 pt-4 relative z-10">
          <a href="{{ route('community') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-md transition transform hover:-translate-y-0.5">
            <span>Join the Community</span>
            <span>→</span>
          </a>
        </div>

        <!-- TECHNICIAN MAN IMAGE -->
        <div class="absolute right-0 bottom-0 w-44 sm:w-52 h-48 sm:h-60 opacity-90 pointer-events-none">
          <img src="/images/artisan.png" alt="African technician smiling with folded arms" class="w-full h-full object-cover object-top rounded-tl-3xl" />
        </div>
      </div>

    </div>
  </section>

  <!-- VERIFIED & TRUSTED / SECURE PAYMENTS BANNER -->
  <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pb-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-slate-100">
        
        <div class="flex items-center gap-3.5 pt-2 md:pt-0 md:px-4">
          <div class="w-10 h-10 rounded-xl bg-pp-50 text-pp-600 font-extrabold grid place-items-center text-xl shrink-0">🛡️</div>
          <div>
            <h4 class="text-xs font-extrabold text-slate-900">Verified &amp; Trusted</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">All sellers are identity verified</p>
          </div>
        </div>

        <div class="flex items-center gap-3.5 pt-2 md:pt-0 md:px-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 font-extrabold grid place-items-center text-xl shrink-0">👛</div>
          <div>
            <h4 class="text-xs font-extrabold text-slate-900">Secure Payments</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Your money is safe with us</p>
          </div>
        </div>

        <div class="flex items-center gap-3.5 pt-2 md:pt-0 md:px-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold grid place-items-center text-xl shrink-0">🛡️</div>
          <div>
            <h4 class="text-xs font-extrabold text-slate-900">Dispute Protection</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">We've got your back</p>
          </div>
        </div>

        <div class="flex items-center gap-3.5 pt-2 md:pt-0 md:px-4">
          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 font-extrabold grid place-items-center text-xl shrink-0">👥</div>
          <div>
            <h4 class="text-xs font-extrabold text-slate-900">Nationwide Community</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">In every city across Nigeria</p>
          </div>
        </div>

      </div>
    </div>
  </section>

</div>
