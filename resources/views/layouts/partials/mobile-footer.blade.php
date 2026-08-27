<!-- MOBILE BOTTOM NAVIGATION (Crucial Mobile UX Requirement) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-t border-slate-200 px-3 py-2 flex items-center justify-around text-center">
  <a href="{{ route('welcome') }}" class="flex flex-col items-center text-[10px] font-bold text-pp-600">
    <span class="text-base">🏠</span>
    <span>Home</span>
  </a>
  <button type="button" onclick="toggleMobileBrowse()" class="flex flex-col items-center text-[10px] font-semibold text-slate-600 cursor-pointer">
    <span class="text-base">🔍</span>
    <span>Browse</span>
  </button>
  <a href="{{ route('community') }}" class="flex flex-col items-center text-[10px] font-semibold text-slate-600">
    <span class="text-base">💬</span>
    <span>Community</span>
  </a>
  <a href="{{ route('cart') }}" class="flex flex-col items-center text-[10px] font-semibold text-slate-600 relative">
    <span class="text-base">🛒</span>
    <span>Cart</span>
    <span class="absolute -top-1 right-2 w-3.5 h-3.5 bg-pp-600 text-white rounded-full text-[9px] font-bold grid place-items-center">3</span>
  </a>
  <button type="button" onclick="handleMobileAccountClick()" class="flex flex-col items-center text-[10px] font-semibold text-slate-600 cursor-pointer">
    <span class="text-base">👤</span>
    <span>Account</span>
  </button>
</div>

<!-- MOBILE BROWSE CATEGORIES OVERLAY -->
<div id="mobileBrowseOverlay" class="lg:hidden fixed inset-0 bg-white z-[85] flex flex-col mobile-browse-overlay">
  
  <!-- SEARCH BAR & HEADER -->
  <div class="p-4 border-b border-slate-200 bg-white shrink-0 space-y-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-pp-600 text-white grid place-items-center font-extrabold text-sm shadow-xs">P</div>
        <span class="font-extrabold text-base text-slate-900">Explore Categories</span>
      </div>
      <button onclick="closeMobileBrowse()" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 font-extrabold text-lg grid place-items-center cursor-pointer hover:bg-slate-200 transition">✕</button>
    </div>

    <!-- SEARCH FORM -->
    <form action="{{ route('category') }}" method="GET" class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-11 bg-slate-50">
      <span class="pl-3 text-slate-400">⌕</span>
      <input name="q" class="flex-1 px-3 outline-none text-xs font-medium bg-transparent" placeholder="Search devices, parts, models or scrap..." />
      <button type="submit" class="px-4 bg-pp-600 text-white text-xs font-bold h-full">Search</button>
    </form>
  </div>

  <!-- MULTILEVEL COLLAPSIBLE CATEGORIES LIST -->
  <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3 bg-slate-50/50">
    
    <!-- 1. ELECTRONICS & GADGETS -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=electronics" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">💻</span>
          <span>Electronics &amp; Gadgets</span>
        </a>
        <button onclick="toggleMobileCat('electronics', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-electronics" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-electronics" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=laptops" class="block font-semibold text-slate-700 hover:text-pp-600">Complete Laptops (HP, Dell, Apple, Lenovo)</a>
        <a href="{{ route('category') }}?tab=parts&cat=laptops" class="block font-semibold text-slate-700 hover:text-pp-600">Laptop Parts (Screens, Batteries, Motherboards)</a>
        <a href="{{ route('category') }}?cat=phones" class="block font-semibold text-slate-700 hover:text-pp-600">Phones (iPhone, Samsung, Pixel, Tecno)</a>
        <a href="{{ route('category') }}?tab=scrap&cat=electronics" class="block font-bold text-amber-800 hover:underline">Scrap &amp; Salvage Electronics →</a>
      </div>
    </div>

    <!-- 2. VEHICLES & AUTO PARTS -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=vehicles" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🚗</span>
          <span>Vehicles &amp; Auto Parts</span>
        </a>
        <button onclick="toggleMobileCat('vehicles', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-vehicles" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-vehicles" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=vehicles&brand=toyota" class="block font-semibold text-slate-700 hover:text-pp-600">Cars (Toyota, Honda, Lexus, Mercedes)</a>
        <a href="{{ route('category') }}?tab=parts&part=engine" class="block font-semibold text-slate-700 hover:text-pp-600">Engines, Gearboxes &amp; Transmission</a>
        <a href="{{ route('category') }}?tab=parts&part=ecu" class="block font-semibold text-slate-700 hover:text-pp-600">ECUs &amp; Brain Boxes</a>
        <a href="{{ route('category') }}?tab=scrap&cat=vehicles" class="block font-bold text-amber-800 hover:underline">Accident &amp; Scrap Vehicles →</a>
      </div>
    </div>

    <!-- 3. APPLIANCES & HOME -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=appliances" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">⚙️</span>
          <span>Appliances &amp; Home</span>
        </a>
        <button onclick="toggleMobileCat('appliances', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-appliances" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-appliances" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=washing-machines" class="block font-semibold text-slate-700 hover:text-pp-600">Washing Machines &amp; Refrigerators</a>
        <a href="{{ route('category') }}?cat=air-conditioners" class="block font-semibold text-slate-700 hover:text-pp-600">Air Conditioners &amp; Inverters</a>
        <a href="{{ route('category') }}?tab=parts&cat=appliances" class="block font-semibold text-slate-700 hover:text-pp-600">Appliance Parts (Motors, Pumps, Compressors)</a>
      </div>
    </div>

    <!-- 4. EQUIPMENT & POWER -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=equipment" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">⚡</span>
          <span>Equipment &amp; Power</span>
        </a>
        <button onclick="toggleMobileCat('equipment', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-equipment" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-equipment" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?brand=caterpillar" class="block font-semibold text-slate-700 hover:text-pp-600">Generators (Caterpillar, Cummins, Perkins)</a>
        <a href="{{ route('category') }}?brand=bosch" class="block font-semibold text-slate-700 hover:text-pp-600">Workshop &amp; Power Tools (Bosch, Makita, DeWalt)</a>
        <a href="{{ route('category') }}?tab=parts&part=injector" class="block font-semibold text-slate-700 hover:text-pp-600">Generator Parts &amp; Diesel Injectors</a>
      </div>
    </div>

    <!-- 5. CONSTRUCTION & HEAVY MACHINERY -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=construction" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🏗️</span>
          <span>Construction Machinery</span>
        </a>
        <button onclick="toggleMobileCat('construction', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-construction" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-construction" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=excavators" class="block font-semibold text-slate-700 hover:text-pp-600">Excavators (CAT, Komatsu, Volvo)</a>
        <a href="{{ route('category') }}?cat=bulldozers" class="block font-semibold text-slate-700 hover:text-pp-600">Bulldozers, Cranes &amp; Backhoes</a>
        <a href="{{ route('category') }}?tab=parts&cat=construction" class="block font-semibold text-slate-700 hover:text-pp-600">Hydraulic Pumps &amp; Undercarriage Parts</a>
      </div>
    </div>

    <!-- 6. SCRAP & SALVAGE HUB -->
    <div class="bg-amber-50/80 rounded-2xl border border-amber-200/80 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?tab=scrap" class="flex items-center gap-3 font-extrabold text-xs text-amber-900 flex-1 hover:underline">
          <span class="text-base">♻️</span>
          <span>Scrap &amp; Salvage Hub</span>
        </a>
        <button onclick="toggleMobileCat('scrap', event)" class="w-8 h-8 rounded-xl hover:bg-amber-100 grid place-items-center font-bold text-amber-800 transition cursor-pointer">
          <svg id="mobile-chev-scrap" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-scrap" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-amber-200/60">
        <a href="{{ route('category') }}?tab=scrap&cat=laptops" class="block font-semibold text-amber-900 hover:underline">Damaged Laptops &amp; MacBooks</a>
        <a href="{{ route('category') }}?tab=scrap&cat=phones" class="block font-semibold text-amber-900 hover:underline">Broken Screen Phones</a>
        <a href="{{ route('category') }}?tab=scrap&cat=vehicles" class="block font-semibold text-amber-900 hover:underline">Accident Cars for Salvage</a>
        <a href="{{ route('category') }}?tab=scrap&cat=generators" class="block font-bold text-amber-900 hover:underline">Non-working Generators →</a>
      </div>
    </div>

  </div>
</div>
