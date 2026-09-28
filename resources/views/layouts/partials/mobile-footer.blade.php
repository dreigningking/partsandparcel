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

  <!-- MULTILEVEL COLLAPSIBLE CATEGORIES LIST (ALL 9 PRIMARY CATEGORIES & CHILDREN) -->
  <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3 bg-slate-50/50">
    
    <!-- 1. ELECTRONICS -->
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
        <a href="{{ route('category') }}?cat=phones" class="block font-semibold text-slate-700 hover:text-pp-600">Smartphones &amp; Tablets</a>
        <a href="{{ route('category') }}?cat=laptops" class="block font-semibold text-slate-700 hover:text-pp-600">Laptops &amp; Computers</a>
        <a href="{{ route('category') }}?cat=screens-displays" class="block font-semibold text-slate-700 hover:text-pp-600">Screens &amp; Displays</a>
        <a href="{{ route('category') }}?cat=batteries-chargers" class="block font-semibold text-slate-700 hover:text-pp-600">Batteries &amp; Charging Adapters</a>
        <a href="{{ route('category') }}?cat=motherboards-logic-boards" class="block font-semibold text-slate-700 hover:text-pp-600">Motherboards &amp; Logic Boards</a>
        <a href="{{ route('category') }}?cat=storage-memory" class="block font-semibold text-slate-700 hover:text-pp-600">RAM, Storage &amp; SSDs</a>
        <a href="{{ route('category') }}?cat=keyboards-input" class="block font-semibold text-slate-700 hover:text-pp-600">Keyboards &amp; Input Devices</a>
        <a href="{{ route('category') }}?cat=audio-sound" class="block font-semibold text-slate-700 hover:text-pp-600">Audio, Headphones &amp; Speakers</a>
        <a href="{{ route('category') }}?cat=cameras-sensors" class="block font-semibold text-slate-700 hover:text-pp-600">Cameras &amp; Image Sensors</a>
        <a href="{{ route('category') }}?cat=networking-telecom" class="block font-semibold text-slate-700 hover:text-pp-600">Networking &amp; Telecom Hardware</a>
        <a href="{{ route('category') }}?tab=scrap&cat=salvage-electronics" class="block font-bold text-amber-800 hover:underline">Scrap &amp; Salvage Electronics →</a>
      </div>
    </div>

    <!-- 2. APPLIANCES -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=appliances" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🔌</span>
          <span>Appliances &amp; Parts</span>
        </a>
        <button onclick="toggleMobileCat('appliances', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-appliances" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-appliances" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=washing-machines" class="block font-semibold text-slate-700 hover:text-pp-600">Washing Machines &amp; Dryers</a>
        <a href="{{ route('category') }}?cat=refrigerators" class="block font-semibold text-slate-700 hover:text-pp-600">Refrigerators &amp; Deep Freezers</a>
        <a href="{{ route('category') }}?cat=air-conditioners" class="block font-semibold text-slate-700 hover:text-pp-600">Air Conditioners &amp; Inverters</a>
        <a href="{{ route('category') }}?cat=kitchen-appliances" class="block font-semibold text-slate-700 hover:text-pp-600">Microwaves &amp; Kitchen Appliances</a>
        <a href="{{ route('category') }}?cat=appliance-compressors" class="block font-semibold text-slate-700 hover:text-pp-600">Compressors &amp; Gas Tanks</a>
        <a href="{{ route('category') }}?cat=appliance-motors-pumps" class="block font-semibold text-slate-700 hover:text-pp-600">Washing Machine Motors &amp; Pumps</a>
        <a href="{{ route('category') }}?cat=appliance-control-boards" class="block font-semibold text-slate-700 hover:text-pp-600">Appliance Control Boards &amp; Capacitors</a>
        <a href="{{ route('category') }}?cat=water-heaters-dispensers" class="block font-semibold text-slate-700 hover:text-pp-600">Water Dispensers &amp; Heaters</a>
        <a href="{{ route('category') }}?tab=scrap&cat=salvage-appliances" class="block font-bold text-amber-800 hover:underline">Salvage Appliances &amp; Compressors →</a>
      </div>
    </div>

    <!-- 3. VEHICLES -->
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
        <a href="{{ route('category') }}?cat=cars" class="block font-semibold text-slate-700 hover:text-pp-600">Passenger Cars &amp; Sedans</a>
        <a href="{{ route('category') }}?cat=trucks" class="block font-semibold text-slate-700 hover:text-pp-600">Haulage Trucks &amp; Trailers</a>
        <a href="{{ route('category') }}?cat=buses" class="block font-semibold text-slate-700 hover:text-pp-600">Buses &amp; Commercial Vans</a>
        <a href="{{ route('category') }}?cat=motorcycles" class="block font-semibold text-slate-700 hover:text-pp-600">Motorcycles &amp; Tricycles (Keke)</a>
        <a href="{{ route('category') }}?cat=complete-engines" class="block font-semibold text-slate-700 hover:text-pp-600">Complete Tokunbo Engines</a>
        <a href="{{ route('category') }}?cat=gearboxes-transmissions" class="block font-semibold text-slate-700 hover:text-pp-600">Gearboxes &amp; Transmissions</a>
        <a href="{{ route('category') }}?cat=ecus-electrical" class="block font-semibold text-slate-700 hover:text-pp-600">ECUs, Sensors &amp; Brain Boxes</a>
        <a href="{{ route('category') }}?cat=suspension-brakes" class="block font-semibold text-slate-700 hover:text-pp-600">Suspension, Brakes &amp; Steering</a>
        <a href="{{ route('category') }}?cat=vehicle-body-parts" class="block font-semibold text-slate-700 hover:text-pp-600">Auto Body Panels &amp; Bumpers</a>
        <a href="{{ route('category') }}?cat=alternators-starters" class="block font-semibold text-slate-700 hover:text-pp-600">Alternators &amp; Starter Motors</a>
        <a href="{{ route('category') }}?tab=scrap&cat=salvage-vehicles" class="block font-bold text-amber-800 hover:underline">Accident &amp; Scrap Vehicles →</a>
      </div>
    </div>

    <!-- 4. HEAVY EQUIPMENT -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=heavy-equipment" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🚜</span>
          <span>Heavy Equipment</span>
        </a>
        <button onclick="toggleMobileCat('heavy-equipment', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-heavy-equipment" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-heavy-equipment" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=excavators" class="block font-semibold text-slate-700 hover:text-pp-600">Excavators &amp; Diggers</a>
        <a href="{{ route('category') }}?cat=bulldozers" class="block font-semibold text-slate-700 hover:text-pp-600">Bulldozers &amp; Crawlers</a>
        <a href="{{ route('category') }}?cat=wheel-loaders" class="block font-semibold text-slate-700 hover:text-pp-600">Wheel Loaders &amp; Backhoes</a>
        <a href="{{ route('category') }}?cat=cranes" class="block font-semibold text-slate-700 hover:text-pp-600">Heavy Cranes &amp; Lifting Equipment</a>
        <a href="{{ route('category') }}?cat=forklifts-material-handling" class="block font-semibold text-slate-700 hover:text-pp-600">Forklifts &amp; Material Handling</a>
        <a href="{{ route('category') }}?cat=road-rollers-compactors" class="block font-semibold text-slate-700 hover:text-pp-600">Road Rollers &amp; Compactors</a>
        <a href="{{ route('category') }}?cat=undercarriage-tracks" class="block font-semibold text-slate-700 hover:text-pp-600">Undercarriage, Tracks &amp; Rollers</a>
        <a href="{{ route('category') }}?cat=hydraulic-pumps-cylinders" class="block font-semibold text-slate-700 hover:text-pp-600">Heavy Hydraulic Pumps &amp; Rams</a>
        <a href="{{ route('category') }}?tab=scrap&cat=salvage-heavy-equipment" class="block font-bold text-amber-800 hover:underline">Decommissioned Equipment for Salvage →</a>
      </div>
    </div>

    <!-- 5. CONSTRUCTION -->
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
        <a href="{{ route('category') }}?cat=concrete-mixers-pumps" class="block font-semibold text-slate-700 hover:text-pp-600">Concrete Mixers &amp; Batch Plants</a>
        <a href="{{ route('category') }}?cat=scaffolding-formwork" class="block font-semibold text-slate-700 hover:text-pp-600">Scaffolding &amp; Formwork Systems</a>
        <a href="{{ route('category') }}?cat=tamping-rammers-compactors" class="block font-semibold text-slate-700 hover:text-pp-600">Tamping Rammers &amp; Plate Compactors</a>
        <a href="{{ route('category') }}?cat=demolition-breakers-drills" class="block font-semibold text-slate-700 hover:text-pp-600">Demolition Breakers &amp; Rock Drills</a>
        <a href="{{ route('category') }}?cat=surveying-instruments" class="block font-semibold text-slate-700 hover:text-pp-600">Surveying &amp; Laser Levels</a>
        <a href="{{ route('category') }}?cat=tower-gantry-cranes" class="block font-semibold text-slate-700 hover:text-pp-600">Tower Cranes &amp; Hoists</a>
        <a href="{{ route('category') }}?cat=asphalt-paving-machines" class="block font-semibold text-slate-700 hover:text-pp-600">Asphalt Pavers &amp; Screeds</a>
        <a href="{{ route('category') }}?cat=construction-tools" class="block font-semibold text-slate-700 hover:text-pp-600">Construction Power &amp; Hand Tools</a>
        <a href="{{ route('category') }}?tab=parts&cat=construction" class="block font-bold text-pp-600 hover:underline">Construction Replacement Parts →</a>
      </div>
    </div>

    <!-- 6. INDUSTRIAL -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=industrial" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🏭</span>
          <span>Industrial Machinery</span>
        </a>
        <button onclick="toggleMobileCat('industrial', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-industrial" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-industrial" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=compressors" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Screw Compressors</a>
        <a href="{{ route('category') }}?cat=motors" class="block font-semibold text-slate-700 hover:text-pp-600">3-Phase Electric Motors &amp; Drives</a>
        <a href="{{ route('category') }}?cat=switchgear" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Switchgear &amp; Transformers</a>
        <a href="{{ route('category') }}?cat=industrial-pumps" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Water &amp; Chemical Pumps</a>
        <a href="{{ route('category') }}?cat=cnc-metalworking" class="block font-semibold text-slate-700 hover:text-pp-600">CNC Lathes &amp; Metalworking Machinery</a>
        <a href="{{ route('category') }}?cat=industrial-automation" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Automation &amp; PLCs</a>
        <a href="{{ route('category') }}?cat=packaging-equipment" class="block font-semibold text-slate-700 hover:text-pp-600">Packaging &amp; Bottling Machinery</a>
        <a href="{{ route('category') }}?cat=valves-piping-seals" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Valves &amp; Mechanical Seals</a>
        <a href="{{ route('community') }}" class="block font-bold text-pp-600 hover:underline">Post Request to Industrial Dealers →</a>
      </div>
    </div>

    <!-- 7. AGRICULTURAL -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=agricultural" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">🌾</span>
          <span>Agricultural Machinery</span>
        </a>
        <button onclick="toggleMobileCat('agricultural', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-agricultural" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-agricultural" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=tractors" class="block font-semibold text-slate-700 hover:text-pp-600">Farm Tractors &amp; Tillage Machinery</a>
        <a href="{{ route('category') }}?cat=harvesters-combines" class="block font-semibold text-slate-700 hover:text-pp-600">Harvesters &amp; Combine Heads</a>
        <a href="{{ route('category') }}?cat=ploughs-harrows-tillers" class="block font-semibold text-slate-700 hover:text-pp-600">Ploughs, Harrows &amp; Rotavators</a>
        <a href="{{ route('category') }}?cat=irrigation-systems" class="block font-semibold text-slate-700 hover:text-pp-600">Irrigation Pumps &amp; Sprinkler Systems</a>
        <a href="{{ route('category') }}?cat=planters-spreaders" class="block font-semibold text-slate-700 hover:text-pp-600">Planters, Seeders &amp; Sprayers</a>
        <a href="{{ route('category') }}?cat=grain-milling-processing" class="block font-semibold text-slate-700 hover:text-pp-600">Grain Milling &amp; Processing Machines</a>
        <a href="{{ route('category') }}?cat=poultry-livestock-equipment" class="block font-semibold text-slate-700 hover:text-pp-600">Poultry &amp; Livestock Farm Equipment</a>
        <a href="{{ route('category') }}?cat=tractor-spare-parts" class="block font-semibold text-slate-700 hover:text-pp-600">Tractor Gearboxes &amp; Implement Spares</a>
        <a href="{{ route('category') }}?tab=scrap&cat=agricultural" class="block font-bold text-amber-800 hover:underline">Damaged Farm Machinery for Salvage →</a>
      </div>
    </div>

    <!-- 8. POWER & ENERGY -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=power-energy" class="flex items-center gap-3 font-extrabold text-xs text-slate-900 flex-1 hover:text-pp-600 transition">
          <span class="text-base">⚡</span>
          <span>Power &amp; Energy</span>
        </a>
        <button onclick="toggleMobileCat('power-energy', event)" class="w-8 h-8 rounded-xl hover:bg-slate-100 grid place-items-center font-bold text-slate-600 transition cursor-pointer">
          <svg id="mobile-chev-power-energy" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-power-energy" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-slate-100">
        <a href="{{ route('category') }}?cat=generators" class="block font-semibold text-slate-700 hover:text-pp-600">Diesel &amp; Heavy Industrial Generators</a>
        <a href="{{ route('category') }}?cat=solar-inverters" class="block font-semibold text-slate-700 hover:text-pp-600">Solar Panels &amp; Inverters</a>
        <a href="{{ route('category') }}?cat=energy-storage-batteries" class="block font-semibold text-slate-700 hover:text-pp-600">Deep Cycle &amp; Lithium Batteries</a>
        <a href="{{ route('category') }}?cat=alternators-avr" class="block font-semibold text-slate-700 hover:text-pp-600">Generator Alternators &amp; AVRs</a>
        <a href="{{ route('category') }}?cat=power-plants" class="block font-semibold text-slate-700 hover:text-pp-600">Industrial Power Plants &amp; Turbines</a>
        <a href="{{ route('category') }}?cat=electrical-transformers" class="block font-semibold text-slate-700 hover:text-pp-600">Distribution Transformers &amp; HT Panels</a>
        <a href="{{ route('category') }}?cat=generator-injectors-pumps" class="block font-semibold text-slate-700 hover:text-pp-600">Diesel Fuel Injectors &amp; Pumps</a>
        <a href="{{ route('category') }}?cat=generator-accessories-parts" class="block font-semibold text-slate-700 hover:text-pp-600">Generator Enclosures &amp; Engine Parts</a>
        <a href="{{ route('category') }}?tab=scrap&cat=salvage-generators" class="block font-bold text-amber-800 hover:underline">Non-Working Generators for Parts →</a>
      </div>
    </div>

    <!-- 9. SCRAP & SALVAGE HUB -->
    <div class="bg-amber-50/80 rounded-2xl border border-amber-200/80 p-1">
      <div class="flex items-center justify-between p-3">
        <a href="{{ route('category') }}?cat=scrap-salvage" class="flex items-center gap-3 font-extrabold text-xs text-amber-900 flex-1 hover:underline">
          <span class="text-base">♻️</span>
          <span>Scrap &amp; Salvage Hub</span>
        </a>
        <button onclick="toggleMobileCat('scrap-salvage', event)" class="w-8 h-8 rounded-xl hover:bg-amber-100 grid place-items-center font-bold text-amber-800 transition cursor-pointer">
          <svg id="mobile-chev-scrap-salvage" class="w-4 h-4 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.03a.75.75 0 111.06-1.06l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div id="mobile-sub-scrap-salvage" class="hidden pl-6 pr-3 pb-3 pt-1 space-y-2 text-xs border-t border-amber-200/60">
        <a href="{{ route('category') }}?cat=salvage-electronics" class="block font-semibold text-amber-900 hover:underline">Salvage Electronics &amp; Dead Boards</a>
        <a href="{{ route('category') }}?cat=salvage-vehicles" class="block font-semibold text-amber-900 hover:underline">Accident &amp; Salvage Vehicles</a>
        <a href="{{ route('category') }}?cat=salvage-heavy-equipment" class="block font-semibold text-amber-900 hover:underline">Decommissioned Equipment Units</a>
        <a href="{{ route('category') }}?cat=salvage-generators" class="block font-semibold text-amber-900 hover:underline">Faulty Generators &amp; Salvage Motors</a>
        <a href="{{ route('category') }}?cat=salvage-appliances" class="block font-semibold text-amber-900 hover:underline">Salvage Home Appliances &amp; Compressors</a>
        <a href="{{ route('category') }}?cat=scrap-laptops-motherboards" class="block font-semibold text-amber-900 hover:underline">Damaged Laptops &amp; Motherboards</a>
        <a href="{{ route('category') }}?cat=scrap-engine-blocks" class="block font-semibold text-amber-900 hover:underline">Non-Running Engine Blocks &amp; Castings</a>
        <a href="{{ route('category') }}?cat=scrap-metal-bulk" class="block font-semibold text-amber-900 hover:underline">Bulk Scrap Metal &amp; Copper</a>
        <a href="{{ route('category') }}?tab=scrap" class="block font-bold text-amber-900 hover:underline">Browse All Salvage Listings →</a>
      </div>
    </div>

  </div>
</div>
