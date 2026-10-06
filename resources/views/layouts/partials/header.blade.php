<!-- TOP ANNOUNCEMENT BAR -->
<div class="hidden lg:flex bg-pp-900 text-white py-2 px-4 text-xs font-medium text-center flex items-center justify-center gap-2">
  <span class="bg-pp-600 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider">Nigeria's #1</span>
  <span>Marketplace for devices, machines, spare parts &amp; salvage items.</span>
  <a href="{{ route('subscriptions') }}" class="underline hover:text-pp-200 ml-1 font-semibold">Sell on Parts &amp; Parcel →</a>
</div>

<!-- HEADER NAVIGATION (ONLY TOP ROW STICKY) -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200/80 shadow-xs">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="h-[72px] flex items-center justify-between gap-4">
      
      <!-- LOGO -->
      <a href="{{ route('welcome') }}" class="flex items-center gap-2.5 shrink-0">
        <div class="w-10 h-10 rounded-xl bg-pp-600 text-white grid place-items-center shadow-sm">
          <svg viewBox="0 0 32 32" class="w-6 h-6" fill="none">
            <path d="M16 3 27 9.2v13.6L16 29 5 22.8V9.2L16 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
            <path d="M16 3v13m11-6.8-11 6.8L5 9.2M16 16v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-tight">Parts &amp; Parcel</span>
      </a>

      <!-- GLOBAL SEARCH BAR WITH LIVE SUGGESTIONS -->
      <livewire:header-search />

      <!-- USER ACTIONS & NAVIGATION LINKS -->
      @include('layouts.partials.header-user-actions')
    </div>
  </div>
</header>

<!-- NON-STICKY CATEGORY BAR (SCROLLS AWAY NATURALLY WITH PAGE) -->
<div class="bg-white border-b border-slate-200 text-slate-700 relative z-40">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <nav class="hidden lg:flex h-11 items-center justify-between w-full text-xs font-bold">
      <a href="{{ route('category') }}?cat=electronics" data-target="mega-electronics" data-menu="electronics" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        📱 Electronics <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=appliances" data-target="mega-appliances" data-menu="appliances" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🔌 Appliances <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=vehicles" data-target="mega-vehicles" data-menu="vehicles" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🚗 Vehicles <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=heavy-equipment" data-target="mega-heavy-equipment" data-menu="heavy-equipment" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🚜 Heavy Equipment <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=construction" data-target="mega-construction" data-menu="construction" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🏗️ Construction <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=industrial" data-target="mega-industrial" data-menu="industrial" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🏭 Industrial <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=agricultural" data-target="mega-agricultural" data-menu="agricultural" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        🌾 Agricultural <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?cat=power-energy" data-target="mega-power-energy" data-menu="power-energy" class="nav-trigger px-2.5 h-9 rounded-lg hover:bg-pp-50 hover:text-pp-600 flex items-center gap-1 transition whitespace-nowrap">
        ⚡ Power &amp; Energy <svg class="nav-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
      <a href="{{ route('category') }}?tab=scrap&cat=scrap-salvage" data-target="mega-scrap-salvage" data-menu="scrap-salvage" class="nav-trigger px-3 h-9 rounded-lg hover:bg-amber-100 flex items-center gap-1 transition whitespace-nowrap text-amber-800 bg-amber-100/80 border border-amber-300/60 shadow-xs">
        🛠️ Scrap &amp; Salvage <svg class="nav-chevron w-3.5 h-3.5 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
      </a>
    </nav>
  </div>

  <!-- MEGA MENUS CONTAINERS -->

  <!-- 1. MEGA MENU: ELECTRONICS -->
  <div id="mega-electronics" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-pp-50 p-6 border border-pp-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-pp-600">Category Overview</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Electronics &amp; Gadgets</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Smartphones, laptops, testing boards, replacement displays, original batteries &amp; salvage electronics.</p>
        <a href="{{ route('category') }}?cat=electronics" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-pp-600 hover:text-pp-700">Explore All Electronics →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Complete Devices</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=phones" class="hover:text-pp-600 font-semibold text-slate-800">Smartphones &amp; Tablets</a></li>
          <li><a href="{{ route('category') }}?cat=laptops" class="hover:text-pp-600 font-semibold text-slate-800">Laptops &amp; Computers</a></li>
          <li><a href="{{ route('category') }}?cat=audio-sound" class="hover:text-pp-600">Audio, Headphones &amp; Speakers</a></li>
          <li><a href="{{ route('category') }}?cat=cameras-sensors" class="hover:text-pp-600">Cameras &amp; Image Sensors</a></li>
          <li><a href="{{ route('category') }}?cat=networking-telecom" class="hover:text-pp-600">Networking &amp; Telecom Hardware</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Popular Spare Parts</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=screens-displays" class="hover:text-pp-600 font-semibold text-slate-800">Screens &amp; Displays</a></li>
          <li><a href="{{ route('category') }}?cat=batteries-chargers" class="hover:text-pp-600 font-semibold text-slate-800">Batteries &amp; Charging Adapters</a></li>
          <li><a href="{{ route('category') }}?cat=motherboards-logic-boards" class="hover:text-pp-600">Motherboards &amp; Logic Boards</a></li>
          <li><a href="{{ route('category') }}?cat=storage-memory" class="hover:text-pp-600">RAM, Storage &amp; SSDs</a></li>
          <li><a href="{{ route('category') }}?cat=keyboards-input" class="hover:text-pp-600">Keyboards &amp; Input Devices</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Leading Brands</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=phones&brand=apple" class="hover:text-pp-600">Apple iPhone, iPad &amp; Mac</a></li>
          <li><a href="{{ route('category') }}?cat=phones&brand=samsung" class="hover:text-pp-600">Samsung Galaxy S &amp; Note Series</a></li>
          <li><a href="{{ route('category') }}?cat=laptops&brand=hp" class="hover:text-pp-600">HP (EliteBook, ProBook, Envy)</a></li>
          <li><a href="{{ route('category') }}?cat=laptops&brand=dell" class="hover:text-pp-600">Dell (XPS, Latitude, Inspiron)</a></li>
          <li><a href="{{ route('category') }}?cat=laptops&brand=lenovo" class="hover:text-pp-600">Lenovo ThinkPad &amp; Legion</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Scrap &amp; Salvage Hub</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?tab=scrap&cat=salvage-electronics" class="hover:text-amber-700 font-semibold">Salvage Electronics &amp; Dead Boards</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=scrap-laptops-motherboards" class="hover:text-amber-700">Damaged Laptops &amp; Motherboards</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=electronics" class="text-amber-700 font-bold hover:underline">Browse All Electronics Scrap →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 2. MEGA MENU: APPLIANCES -->
  <div id="mega-appliances" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-4 gap-8">
      <div class="col-span-1 rounded-2xl bg-indigo-50 p-6 border border-indigo-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Category Overview</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Appliances &amp; Parts</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Washing machines, AC compressors, refrigerators, water dispensers &amp; appliance control units.</p>
        <a href="{{ route('category') }}?cat=appliances" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-indigo-700 hover:underline">Explore Appliances →</a>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-900 uppercase mb-3">Major Home Appliances</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=washing-machines" class="hover:text-pp-600 font-semibold text-slate-800">Washing Machines &amp; Dryers</a></li>
          <li><a href="{{ route('category') }}?cat=refrigerators" class="hover:text-pp-600 font-semibold text-slate-800">Refrigerators &amp; Deep Freezers</a></li>
          <li><a href="{{ route('category') }}?cat=air-conditioners" class="hover:text-pp-600 font-semibold text-slate-800">Air Conditioners &amp; Inverters</a></li>
          <li><a href="{{ route('category') }}?cat=kitchen-appliances" class="hover:text-pp-600">Microwaves &amp; Kitchen Appliances</a></li>
          <li><a href="{{ route('category') }}?cat=water-heaters-dispensers" class="hover:text-pp-600">Water Dispensers &amp; Heaters</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-900 uppercase mb-3">Appliance Spare Parts</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=appliance-compressors" class="hover:text-pp-600 font-semibold text-slate-800">Compressors &amp; Gas Tanks</a></li>
          <li><a href="{{ route('category') }}?cat=appliance-motors-pumps" class="hover:text-pp-600 font-semibold text-slate-800">Washing Machine Motors &amp; Pumps</a></li>
          <li><a href="{{ route('category') }}?cat=appliance-control-boards" class="hover:text-pp-600">Appliance Control Boards &amp; Capacitors</a></li>
          <li><a href="{{ route('category') }}?cat=appliances&brand=lg" class="hover:text-pp-600">LG &amp; Samsung Original Parts</a></li>
          <li><a href="{{ route('category') }}?cat=appliances&brand=haier-thermocool" class="hover:text-pp-600">Thermocool Deep Freezer Spares</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-900 uppercase mb-3">Salvage &amp; Sourcing</h4>
        <ul class="space-y-2 text-xs text-slate-600 mb-4">
          <li><a href="{{ route('category') }}?tab=scrap&cat=salvage-appliances" class="text-amber-700 font-bold hover:underline">Salvage Appliances &amp; Compressors →</a></li>
        </ul>
        <div class="rounded-xl bg-slate-50 p-3 border border-slate-200">
          <p class="text-xs text-slate-600 mb-2">Can't find a specific washer motor or AC part?</p>
          <a href="{{ route('community') }}" class="text-xs font-bold text-pp-600 hover:underline">Post Request to Technicians →</a>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. MEGA MENU: VEHICLES -->
  <div id="mega-vehicles" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-emerald-50/70 p-6 border border-emerald-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Vehicle Hub</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Vehicles &amp; Auto Parts</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Passenger cars, haulage trucks, buses, complete Tokunbo engines, gearboxes &amp; salvage cars.</p>
        <a href="{{ route('category') }}?cat=vehicles" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-emerald-700 hover:underline">View All Vehicles →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Vehicles by Type</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=cars" class="hover:text-pp-600 font-semibold text-slate-800">Passenger Cars &amp; Sedans</a></li>
          <li><a href="{{ route('category') }}?cat=trucks" class="hover:text-pp-600 font-semibold text-slate-800">Haulage Trucks &amp; Trailers</a></li>
          <li><a href="{{ route('category') }}?cat=buses" class="hover:text-pp-600">Buses &amp; Commercial Vans</a></li>
          <li><a href="{{ route('category') }}?cat=motorcycles" class="hover:text-pp-600">Motorcycles &amp; Tricycles (Keke)</a></li>
          <li><a href="{{ route('category') }}?cat=cars&brand=toyota" class="hover:text-pp-600">Toyota (Corolla, Camry, RAV4)</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Mechanical Spares</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=complete-engines" class="hover:text-pp-600 font-semibold text-slate-800">Complete Tokunbo Engines</a></li>
          <li><a href="{{ route('category') }}?cat=gearboxes-transmissions" class="hover:text-pp-600 font-semibold text-slate-800">Gearboxes &amp; Transmissions</a></li>
          <li><a href="{{ route('category') }}?cat=suspension-brakes" class="hover:text-pp-600">Suspension, Brakes &amp; Steering</a></li>
          <li><a href="{{ route('category') }}?cat=alternators-starters" class="hover:text-pp-600">Alternators &amp; Starter Motors</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Auto Electrical &amp; Body</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=ecus-electrical" class="hover:text-pp-600 font-semibold text-slate-800">ECUs, Sensors &amp; Brain Boxes</a></li>
          <li><a href="{{ route('category') }}?cat=vehicle-body-parts" class="hover:text-pp-600">Auto Body Panels &amp; Bumpers</a></li>
          <li><a href="{{ route('category') }}?cat=cars&brand=mercedes-benz" class="hover:text-pp-600">Mercedes-Benz Parts</a></li>
          <li><a href="{{ route('category') }}?cat=cars&brand=lexus" class="hover:text-pp-600">Lexus ES &amp; RX Components</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Accident / Salvage</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?tab=scrap&cat=salvage-vehicles" class="hover:text-amber-700 font-semibold text-slate-800">Accident &amp; Salvage Vehicles</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=scrap-engine-blocks" class="hover:text-amber-700">Non-Running Engine Blocks</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=vehicles" class="text-amber-700 font-bold hover:underline">Scrap Vehicles for Parts →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 4. MEGA MENU: HEAVY EQUIPMENT -->
  <div id="mega-heavy-equipment" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-amber-50 p-6 border border-amber-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Heavy Machinery</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Heavy Equipment Hub</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Excavators, bulldozers, wheel loaders, mobile cranes, forklifts &amp; heavy hydraulic components.</p>
        <a href="{{ route('category') }}?cat=heavy-equipment" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-amber-700 hover:underline">Explore Heavy Equipment →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Earthmoving Machinery</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=excavators" class="hover:text-pp-600 font-semibold text-slate-800">Excavators &amp; Diggers</a></li>
          <li><a href="{{ route('category') }}?cat=bulldozers" class="hover:text-pp-600 font-semibold text-slate-800">Bulldozers &amp; Crawlers</a></li>
          <li><a href="{{ route('category') }}?cat=wheel-loaders" class="hover:text-pp-600">Wheel Loaders &amp; Backhoes</a></li>
          <li><a href="{{ route('category') }}?cat=road-rollers-compactors" class="hover:text-pp-600">Road Rollers &amp; Compactors</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Cranes &amp; Handling</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=cranes" class="hover:text-pp-600 font-semibold text-slate-800">Heavy Cranes &amp; Lifting Equipment</a></li>
          <li><a href="{{ route('category') }}?cat=forklifts-material-handling" class="hover:text-pp-600 font-semibold text-slate-800">Forklifts &amp; Material Handling</a></li>
          <li><a href="{{ route('category') }}?cat=cranes&brand=tadano" class="hover:text-pp-600">Tadano Hydraulic Cranes</a></li>
          <li><a href="{{ route('category') }}?cat=cranes&brand=liebherr" class="hover:text-pp-600">Liebherr Mobile Cranes</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Heavy Spares &amp; Hydraulics</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=undercarriage-tracks" class="hover:text-pp-600 font-semibold text-slate-800">Undercarriage, Tracks &amp; Rollers</a></li>
          <li><a href="{{ route('category') }}?cat=hydraulic-pumps-cylinders" class="hover:text-pp-600 font-semibold text-slate-800">Heavy Hydraulic Pumps &amp; Rams</a></li>
          <li><a href="{{ route('category') }}?cat=heavy-equipment&brand=caterpillar" class="hover:text-pp-600">Caterpillar (320D, D6R, D8R)</a></li>
          <li><a href="{{ route('category') }}?cat=heavy-equipment&brand=komatsu" class="hover:text-pp-600">Komatsu (PC200, PC300)</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Salvage Heavy Equipment</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?tab=scrap&cat=salvage-heavy-equipment" class="hover:text-amber-700 font-semibold text-slate-800">Decommissioned Equipment Units</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=heavy-equipment" class="text-amber-700 font-bold hover:underline">Non-Working Excavators &amp; Crawlers →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 5. MEGA MENU: CONSTRUCTION -->
  <div id="mega-construction" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-orange-50 p-6 border border-orange-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-orange-700">Building &amp; Civil</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Construction Machinery</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Concrete mixers, scaffolding, formwork, compactors, breakers, tower cranes &amp; surveying tools.</p>
        <a href="{{ route('category') }}?cat=construction" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-orange-700 hover:underline">View All Construction →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Concrete &amp; Formwork</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=concrete-mixers-pumps" class="hover:text-pp-600 font-semibold text-slate-800">Concrete Mixers &amp; Batch Plants</a></li>
          <li><a href="{{ route('category') }}?cat=scaffolding-formwork" class="hover:text-pp-600 font-semibold text-slate-800">Scaffolding &amp; Formwork Systems</a></li>
          <li><a href="{{ route('category') }}?cat=tamping-rammers-compactors" class="hover:text-pp-600">Tamping Rammers &amp; Plate Compactors</a></li>
          <li><a href="{{ route('category') }}?cat=asphalt-paving-machines" class="hover:text-pp-600">Asphalt Pavers &amp; Screeds</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Drills, Breakers &amp; Cranes</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=demolition-breakers-drills" class="hover:text-pp-600 font-semibold text-slate-800">Demolition Breakers &amp; Rock Drills</a></li>
          <li><a href="{{ route('category') }}?cat=tower-gantry-cranes" class="hover:text-pp-600 font-semibold text-slate-800">Tower Cranes &amp; Hoists</a></li>
          <li><a href="{{ route('category') }}?cat=surveying-instruments" class="hover:text-pp-600">Surveying &amp; Laser Levels</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Construction Tools</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=construction-tools" class="hover:text-pp-600 font-semibold text-slate-800">Construction Power &amp; Hand Tools</a></li>
          <li><a href="{{ route('category') }}?cat=construction-tools&brand=bosch" class="hover:text-pp-600">Bosch Professional (Drills, Grinders)</a></li>
          <li><a href="{{ route('category') }}?cat=construction-tools&brand=makita" class="hover:text-pp-600">Makita Rotary Hammers</a></li>
          <li><a href="{{ route('category') }}?cat=construction-tools&brand=dewalt" class="hover:text-pp-600">DeWalt Heavy Duty Systems</a></li>
        </ul>
      </div>
      <div class="col-span-1 rounded-2xl bg-amber-50 p-5 border border-amber-200">
        <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2">Construction Hub</h4>
        <p class="text-xs text-amber-800 mb-3">Find salvage mixers, hydraulic cylinders, track chains, and broken boom parts.</p>
        <div class="space-y-2 text-xs">
          <a href="{{ route('category') }}?tab=parts&cat=construction" class="block font-bold text-pp-600 hover:underline">→ Browse Construction Spares</a>
          <a href="{{ route('category') }}?tab=scrap&cat=construction" class="block font-bold text-amber-800 hover:underline">→ Non-Working Units for Salvage</a>
        </div>
      </div>
    </div>
  </div>

  <!-- 6. MEGA MENU: INDUSTRIAL -->
  <div id="mega-industrial" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-4 gap-8">
      <div class="col-span-1 rounded-2xl bg-slate-100 p-6 border border-slate-200">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Factory &amp; Plants</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Industrial Machinery</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Rotary compressors, 3-phase electric motors, switchgear, CNC machinery, PLCs &amp; industrial valves.</p>
        <a href="{{ route('category') }}?cat=industrial" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-pp-600 hover:text-pp-700">Explore Industrial →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Machinery &amp; Drives</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=compressors" class="hover:text-pp-600 font-semibold text-slate-800">Industrial Screw Compressors</a></li>
          <li><a href="{{ route('category') }}?cat=motors" class="hover:text-pp-600 font-semibold text-slate-800">3-Phase Electric Motors &amp; Drives</a></li>
          <li><a href="{{ route('category') }}?cat=switchgear" class="hover:text-pp-600 font-semibold text-slate-800">Industrial Switchgear &amp; Transformers</a></li>
          <li><a href="{{ route('category') }}?cat=industrial-pumps" class="hover:text-pp-600">Industrial Water &amp; Chemical Pumps</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Production &amp; Automation</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=cnc-metalworking" class="hover:text-pp-600 font-semibold text-slate-800">CNC Lathes &amp; Metalworking Machinery</a></li>
          <li><a href="{{ route('category') }}?cat=industrial-automation" class="hover:text-pp-600 font-semibold text-slate-800">Industrial Automation &amp; PLCs</a></li>
          <li><a href="{{ route('category') }}?cat=packaging-equipment" class="hover:text-pp-600">Packaging &amp; Bottling Machinery</a></li>
          <li><a href="{{ route('category') }}?cat=valves-piping-seals" class="hover:text-pp-600">Industrial Valves &amp; Mechanical Seals</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Industrial Brands</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=compressors&brand=ingersoll-rand" class="hover:text-pp-600">Ingersoll Rand Compressors</a></li>
          <li><a href="{{ route('category') }}?cat=compressors&brand=atlas-copco" class="hover:text-pp-600">Atlas Copco GA Series</a></li>
          <li><a href="{{ route('category') }}?cat=motors&brand=abb" class="hover:text-pp-600">ABB Motors &amp; Industrial Drives</a></li>
          <li><a href="{{ route('category') }}?cat=switchgear&brand=siemens" class="hover:text-pp-600">Siemens Automation &amp; Switchgear</a></li>
          <li><a href="{{ route('community') }}" class="text-xs font-bold text-pp-600 hover:underline block pt-2">Post Request to Industrial Dealers →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 7. MEGA MENU: AGRICULTURAL -->
  <div id="mega-agricultural" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-4 gap-8">
      <div class="col-span-1 rounded-2xl bg-emerald-50 p-6 border border-emerald-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Farm &amp; Agro</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Agricultural Machinery</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Farm tractors, harvesters, ploughs, irrigation pumps, feed mills &amp; tractor replacement spares.</p>
        <a href="{{ route('category') }}?cat=agricultural" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-emerald-700 hover:underline">Explore Agricultural →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Farm Machinery</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=tractors" class="hover:text-pp-600 font-semibold text-slate-800">Farm Tractors &amp; Tillage Machinery</a></li>
          <li><a href="{{ route('category') }}?cat=harvesters-combines" class="hover:text-pp-600 font-semibold text-slate-800">Harvesters &amp; Combine Heads</a></li>
          <li><a href="{{ route('category') }}?cat=ploughs-harrows-tillers" class="hover:text-pp-600">Ploughs, Harrows &amp; Rotavators</a></li>
          <li><a href="{{ route('category') }}?cat=planters-spreaders" class="hover:text-pp-600">Planters, Seeders &amp; Sprayers</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Systems &amp; Implements</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=irrigation-systems" class="hover:text-pp-600 font-semibold text-slate-800">Irrigation Pumps &amp; Sprinkler Systems</a></li>
          <li><a href="{{ route('category') }}?cat=grain-milling-processing" class="hover:text-pp-600 font-semibold text-slate-800">Grain Milling &amp; Processing Machines</a></li>
          <li><a href="{{ route('category') }}?cat=poultry-livestock-equipment" class="hover:text-pp-600">Poultry &amp; Livestock Farm Equipment</a></li>
          <li><a href="{{ route('category') }}?cat=tractor-spare-parts" class="hover:text-pp-600 font-semibold text-slate-800">Tractor Gearboxes &amp; Implement Spares</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Top Tractor Brands</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=tractors&brand=massey-ferguson" class="hover:text-pp-600 font-semibold text-slate-800">Massey Ferguson (375, 385, 240)</a></li>
          <li><a href="{{ route('category') }}?cat=tractors&brand=john-deere" class="hover:text-pp-600 font-semibold text-slate-800">John Deere Utility &amp; Ag Tractors</a></li>
          <li><a href="{{ route('category') }}?cat=tractors&brand=mahindra" class="hover:text-pp-600">Mahindra (575 DI, Arjun Novo)</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=agricultural" class="text-xs font-bold text-amber-800 hover:underline block pt-2">Damaged Farm Units for Salvage →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 8. MEGA MENU: POWER & ENERGY -->
  <div id="mega-power-energy" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-slate-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-amber-50 p-6 border border-amber-100">
        <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Power &amp; Energy</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Power &amp; Energy Hub</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Diesel generators, solar panels, hybrid inverters, deep cycle batteries, transformers &amp; fuel injectors.</p>
        <a href="{{ route('category') }}?cat=power-energy" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-amber-700 hover:underline">Explore Power &amp; Energy →</a>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Generators &amp; Plants</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=generators" class="hover:text-pp-600 font-semibold text-slate-800">Diesel &amp; Industrial Generators</a></li>
          <li><a href="{{ route('category') }}?cat=alternators-avr" class="hover:text-pp-600 font-semibold text-slate-800">Generator Alternators &amp; AVRs</a></li>
          <li><a href="{{ route('category') }}?cat=power-plants" class="hover:text-pp-600">Industrial Power Plants &amp; Turbines</a></li>
          <li><a href="{{ route('category') }}?cat=electrical-transformers" class="hover:text-pp-600">Distribution Transformers &amp; HT Panels</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Solar &amp; Storage</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=solar-inverters" class="hover:text-pp-600 font-semibold text-slate-800">Solar Panels &amp; Inverters</a></li>
          <li><a href="{{ route('category') }}?cat=energy-storage-batteries" class="hover:text-pp-600 font-semibold text-slate-800">Deep Cycle &amp; Lithium Batteries</a></li>
          <li><a href="{{ route('category') }}?cat=solar-inverters&brand=felicity-solar" class="hover:text-pp-600">Felicity Solar Hybrid Inverters</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Generator Spares &amp; Brands</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=generator-injectors-pumps" class="hover:text-pp-600 font-semibold text-slate-800">Diesel Fuel Injectors &amp; Pumps</a></li>
          <li><a href="{{ route('category') }}?cat=generator-accessories-parts" class="hover:text-pp-600">Generator Enclosures &amp; Engine Parts</a></li>
          <li><a href="{{ route('category') }}?cat=generators&brand=mikano" class="hover:text-pp-600">Mikano Diesel Generators</a></li>
          <li><a href="{{ route('category') }}?cat=generators&brand=perkins" class="hover:text-pp-600">Perkins Industrial Sets</a></li>
          <li><a href="{{ route('category') }}?cat=generators&brand=cummins" class="hover:text-pp-600">Cummins Generator Sets</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Salvage Generators</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?tab=scrap&cat=salvage-generators" class="hover:text-amber-700 font-semibold text-slate-800">Faulty Generators &amp; Salvage Motors</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=generators" class="text-amber-700 font-bold hover:underline">Non-Working Generators for Parts →</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 9. MEGA MENU: SCRAP & SALVAGE HUB -->
  <div id="mega-scrap-salvage" class="mega-menu absolute left-0 right-0 top-full bg-white border-b border-amber-200 shadow-soft z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-7 grid grid-cols-5 gap-8">
      <div class="col-span-1 rounded-2xl bg-amber-50 p-6 border border-amber-200">
        <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700">Salvage Marketplace</div>
        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Scrap &amp; Salvage Hub</h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-600">Damaged devices, accident vehicles &amp; non-working machines for technicians to harvest functional spare parts.</p>
        <a href="{{ route('category') }}?cat=scrap-salvage" class="inline-flex items-center gap-1 mt-6 text-xs font-bold text-amber-800 hover:underline">Explore Salvage Hub →</a>
      </div>

      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Salvage Electronics</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=salvage-electronics" class="hover:text-amber-700 font-semibold text-slate-800">Salvage Electronics &amp; Dead Boards</a></li>
          <li><a href="{{ route('category') }}?cat=scrap-laptops-motherboards" class="hover:text-amber-700 font-semibold text-slate-800">Damaged Laptops &amp; Motherboards</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=laptops" class="hover:text-amber-700">Water Damaged MacBooks</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=phones" class="hover:text-amber-700">Broken Screen iPhones &amp; Samsungs</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Salvage Vehicles &amp; Auto</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=salvage-vehicles" class="hover:text-amber-700 font-semibold text-slate-800">Accident &amp; Salvage Vehicles</a></li>
          <li><a href="{{ route('category') }}?cat=scrap-engine-blocks" class="hover:text-amber-700 font-semibold text-slate-800">Non-Running Engine Blocks &amp; Castings</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=trucks" class="hover:text-amber-700">Damaged Haulage Trucks for Parts</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Heavy &amp; Industrial Salvage</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=salvage-heavy-equipment" class="hover:text-amber-700 font-semibold text-slate-800">Decommissioned Equipment Units</a></li>
          <li><a href="{{ route('category') }}?cat=salvage-generators" class="hover:text-amber-700 font-semibold text-slate-800">Faulty Generators &amp; Salvage Motors</a></li>
          <li><a href="{{ route('category') }}?cat=scrap-metal-bulk" class="hover:text-amber-700 font-semibold text-slate-800">Bulk Scrap Metal &amp; Copper</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3">Salvage Appliances</h4>
        <ul class="space-y-2 text-xs text-slate-600">
          <li><a href="{{ route('category') }}?cat=salvage-appliances" class="hover:text-amber-700 font-semibold text-slate-800">Salvage Home Appliances &amp; Compressors</a></li>
          <li><a href="{{ route('category') }}?tab=scrap&cat=appliances" class="hover:text-amber-700">Faulty Washing Machines &amp; Motors</a></li>
          <li><a href="{{ route('category') }}?tab=scrap" class="text-amber-800 font-extrabold hover:underline block pt-2">Browse All Salvage Listings →</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
