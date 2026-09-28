<div class="max-w-5xl mx-auto flex flex-col gap-6" x-data="webcamCapture()">

  <!-- TOP HEADER & BACK LINK -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myitems') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to Inventory
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Inventory Registration Wizard</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950">Register New Inventory Asset &amp; Listing</h1>
      <p class="text-xs text-slate-500 mt-0.5">Register complete devices, spare parts, or salvage scrap units for component harvesting and marketplace sales.</p>
    </div>
  </div>

  <!-- WIZARD STEP PROGRESS BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      
      <!-- STEP 1 INDICATOR -->
      <div class="flex items-center gap-3 p-3 rounded-xl transition {{ $step === 1 ? 'bg-pp-50 border border-pp-200 text-pp-700' : 'bg-slate-50 text-slate-500' }}">
        <div class="w-8 h-8 rounded-lg text-xs font-extrabold grid place-items-center {{ $step === 1 ? 'bg-pp-600 text-white' : 'bg-slate-200 text-slate-700' }}">1</div>
        <div>
          <div class="text-xs font-extrabold">Item Type &amp; Details</div>
          <div class="text-[10px] text-slate-500">Type, model, condition &amp; media</div>
        </div>
      </div>

      <!-- STEP 2 INDICATOR -->
      <div class="flex items-center gap-3 p-3 rounded-xl transition {{ $step === 2 ? 'bg-amber-50 border border-amber-200 text-amber-900' : 'bg-slate-50 text-slate-500' }}">
        <div class="w-8 h-8 rounded-lg text-xs font-extrabold grid place-items-center {{ $step === 2 ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700' }}">2</div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="text-xs font-extrabold">Harvesting Matrix</span>
            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-200 text-amber-900">Scrap Only</span>
          </div>
          <div class="text-[10px] text-slate-500">Component disassembly &amp; sub-parts</div>
        </div>
      </div>

      <!-- STEP 3 INDICATOR -->
      <div class="flex items-center gap-3 p-3 rounded-xl transition {{ $step === 3 ? 'bg-pp-50 border border-pp-200 text-pp-700' : 'bg-slate-50 text-slate-500' }}">
        <div class="w-8 h-8 rounded-lg text-xs font-extrabold grid place-items-center {{ $step === 3 ? 'bg-pp-600 text-white' : 'bg-slate-200 text-slate-700' }}">3</div>
        <div>
          <div class="text-xs font-extrabold">Pricing &amp; Listing</div>
          <div class="text-[10px] text-slate-500">Marketplace pricing &amp; warranty</div>
        </div>
      </div>

    </div>
  </div>

  <!-- STEP 1: ITEM TYPE & PHYSICAL CONDITION -->
  @if($step === 1)
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-6 shadow-soft">
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-boxes-stacked text-pp-600"></i> Step 1: Item Classification &amp; Condition
        </h2>
        <span class="text-xs text-slate-400 font-semibold">General Asset Details</span>
      </div>

      <!-- ITEM TYPE SELECTOR CARDS -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Item Type Classification <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          
          <!-- COMPLETE DEVICE -->
          <label class="flex flex-col p-4 rounded-2xl border cursor-pointer transition {{ $item_type === 'whole' ? 'border-pp-500 bg-pp-50/60 ring-2 ring-pp-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
            <input type="radio" wire:model.live="item_type" value="whole" class="sr-only" />
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                💻 Complete Device
              </span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1">Whole working/repairable items (Laptop, Phone, Car, Generator)</span>
          </label>

          <!-- SPARE PART -->
          <label class="flex flex-col p-4 rounded-2xl border cursor-pointer transition {{ $item_type === 'part' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
            <input type="radio" wire:model.live="item_type" value="part" class="sr-only" />
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                ⚙️ Spare Part / Component
              </span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1">Standalone spare components sold individually (Screen, Battery, Motor)</span>
          </label>

          <!-- SCRAP FOR PARTS -->
          <label class="flex flex-col p-4 rounded-2xl border cursor-pointer transition {{ $item_type === 'scrap' ? 'border-amber-500 bg-amber-50/80 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
            <input type="radio" wire:model.live="item_type" value="scrap" class="sr-only" />
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-extrabold text-amber-950 flex items-center gap-1.5">
                🛠️ Scrap / For Parts Unit
              </span>
            </div>
            <span class="text-[11px] text-amber-900 font-medium mt-1">Non-working or damaged asset intended for salvage / disassembly</span>
          </label>

        </div>
      </div>

      <!-- CATALOG CLASSIFICATION (CATEGORY, BRAND, MODEL) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
          <select wire:model.live="category_id" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
            <option value="">Select Category</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Brand / Make <span class="text-rose-500">*</span></label>
          <select wire:model.live="brand_id" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
            <option value="">Select Brand</option>
            @foreach($brands as $brand)
              <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Device Model</label>
          <select wire:model.live="model_id" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
            <option value="">Select Model (or general)</option>
            @foreach($models as $m)
              <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- ITEM NAME / TITLE & STOCK LOCATION -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Item Title / Descriptive Name <span class="text-rose-500">*</span></label>
          <input type="text" wire:model="name" placeholder="e.g. HP EliteBook 840 G5 Laptop" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-pp-500 transition" />
          @error('name') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-bold text-slate-700">Physical Stock Location <span class="text-rose-500">*</span></label>
            <button type="button" wire:click="openLocationModal" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1 cursor-pointer">
              <i class="fas fa-plus text-[10px]"></i> Add New Location
            </button>
          </div>

          <select wire:model="location_id" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
            <option value="">Select Store / Workshop Location</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}">{{ $loc->label }} ({{ $loc->city }}, {{ $loc->state }})</option>
            @endforeach
          </select>
          @error('location_id') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror

          @if(session()->has('location_success'))
            <span class="text-[11px] text-emerald-600 font-bold mt-1 block">✅ {{ session('location_success') }}</span>
          @endif
        </div>
      </div>

      <!-- PHYSICAL CONDITION CUSTOM RADIO DROPDOWN & FAULT NOTES ROW (DESKTOP SAME ROW) -->
      <div class="grid grid-cols-1 {{ $item_type !== 'scrap' ? 'md:grid-cols-2' : '' }} gap-4">
        
        <!-- PHYSICAL CONDITION RADIO DROPDOWN MENU (HIDDEN IF SCRAP) -->
        @if($item_type !== 'scrap')
          <div x-data="{ openConditionDropdown: false }" @click.outside="openConditionDropdown = false" class="relative">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Physical Condition <span class="text-rose-500">*</span></label>
            
            <!-- DROPDOWN BUTTON TRIGGER -->
            <button type="button" @click="openConditionDropdown = !openConditionDropdown" class="w-full p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition flex items-center justify-between shadow-2xs cursor-pointer">
              <div class="gap-2 text-left truncate">
                @if($condition_status === 'new')
                  <div class="font-extrabold text-slate-900">🌟 Brand New</div>
                  <span class="text-slate-400 font-normal truncate">— Unopened or brand new in box</span>
                @elseif($condition_status === 'used')
                  <div class="font-extrabold text-slate-900">✅ Used - Working</div>
                  <span class="text-slate-400 font-normal truncate">— Fully functional, normal wear &amp; tear</span>
                @elseif($condition_status === 'refurbished')
                  <div class="font-extrabold text-slate-900">🛠 Refurbished</div>
                  <span class="text-slate-400 font-normal truncate">— Restored &amp; retested working condition</span>
                @else
                  <span class="font-extrabold text-slate-900">Select Physical Condition</span>
                @endif
              </div>
              <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-2" :class="{ 'rotate-180': openConditionDropdown }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
              </svg>
            </button>

            <!-- FLOATING RADIO DROPDOWN MENU -->
            <div x-show="openConditionDropdown" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 x-cloak
                 class="absolute left-0 right-0 mt-1.5 z-30 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 space-y-1">
              
              <!-- OPTION 1: BRAND NEW -->
              <label @click="$wire.set('condition_status', 'new'); openConditionDropdown = false" 
                     class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition hover:bg-slate-50 {{ $condition_status === 'new' ? 'bg-pp-50/80 text-pp-900' : '' }}">
                <input type="radio" name="custom_condition_status" value="new" wire:model.live="condition_status" class="mt-0.5 w-4 h-4 text-pp-600 border-slate-300 focus:ring-pp-500" />
                <div>
                  <span class="text-xs font-extrabold text-slate-900 block">🌟 Brand New</span>
                  <span class="text-[11px] text-slate-500 font-medium block mt-0.5">Unopened or brand new in box.</span>
                </div>
              </label>

              <!-- OPTION 2: USED - WORKING -->
              <label @click="$wire.set('condition_status', 'used'); openConditionDropdown = false" 
                     class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition hover:bg-slate-50 {{ $condition_status === 'used' ? 'bg-pp-50/80 text-pp-900' : '' }}">
                <input type="radio" name="custom_condition_status" value="used" wire:model.live="condition_status" class="mt-0.5 w-4 h-4 text-pp-600 border-slate-300 focus:ring-pp-500" />
                <div>
                  <span class="text-xs font-extrabold text-slate-900 block">✅ Used - Working</span>
                  <span class="text-[11px] text-slate-500 font-medium block mt-0.5">Fully functional, normal wear &amp; tear.</span>
                </div>
              </label>

              <!-- OPTION 3: REFURBISHED -->
              <label @click="$wire.set('condition_status', 'refurbished'); openConditionDropdown = false" 
                     class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition hover:bg-slate-50 {{ $condition_status === 'refurbished' ? 'bg-pp-50/80 text-pp-900' : '' }}">
                <input type="radio" name="custom_condition_status" value="refurbished" class="mt-0.5 w-4 h-4 text-pp-600 border-slate-300 focus:ring-pp-500" />
                <div>
                  <span class="text-xs font-extrabold text-slate-900 block">🛠 Refurbished</span>
                  <span class="text-[11px] text-slate-500 font-medium block mt-0.5">Restored &amp; retested working condition.</span>
                </div>
              </label>

            </div>
            @error('condition_status') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
          </div>
        @endif

        <!-- CONDITION NOTES & FAULT DETAILS -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Condition Notes &amp; Fault Details</label>
          <textarea wire:model="condition_notes" rows="2" placeholder="Describe specific details or faults (e.g. Minor cosmetic scuffs, missing charger, clean board)..." class="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-pp-500 transition"></textarea>
        </div>
      </div>

      <!-- PUBLIC OVERVIEW DESCRIPTION -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1.5">Public Overview Description</label>
        <textarea wire:model="description" rows="3" placeholder="Detailed product specifications, history, or warranty notes..." class="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-pp-500 transition"></textarea>
      </div>

      <!-- ITEM MEDIA FIELD (PHOTOS & VIDEO) -->
      <div class="space-y-3 border-t border-slate-100 pt-5">
        <div class="flex items-center justify-between">
          <div>
            <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Item Media (Photos &amp; Video)</label>
            <p class="text-[11px] text-slate-500 mt-0.5">Upload photos and video clips of the item or capture directly via camera.</p>
          </div>

          <div class="flex items-center gap-2">
            <!-- HIDDEN FILE INPUT FOR UPLOADING FILES -->
            <input type="file" wire:model="photos" id="fileUploadInput" multiple accept="image/*,video/*" class="hidden" />

            <!-- HIDDEN FILE INPUT FOR FALLBACK CAMERA CAPTURE -->
            <input type="file" wire:model="photos" id="cameraUploadInput" capture="environment" accept="image/*,video/*" class="hidden" />

            <button type="button" onclick="document.getElementById('fileUploadInput').click()" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-pp-50 hover:border-pp-300 text-slate-700 hover:text-pp-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-folder-open text-xs"></i> Upload Media
            </button>

            <button type="button" @click="openCamera" class="px-3 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="fas fa-camera text-xs"></i> Camera Capture
            </button>
          </div>
        </div>

        <!-- UPLOAD LOADING INDICATOR -->
        <div wire:loading wire:target="photos" class="p-3 rounded-xl bg-pp-50 border border-pp-200 text-pp-700 text-xs font-bold flex items-center gap-2">
          <i class="fas fa-spinner fa-spin"></i> Processing media files... Please wait.
        </div>

        @error('photos.*') <span class="text-[11px] text-rose-600 font-bold block">{{ $message }}</span> @enderror

        <!-- MEDIA PREVIEWS GRID -->
        @if(!empty($photos))
          <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-2">
            @foreach($photos as $index => $photo)
              <div class="relative group rounded-2xl border border-slate-200 overflow-hidden bg-slate-900 aspect-square grid place-items-center">
                @if(str_starts_with($photo->getMimeType(), 'video/'))
                  <div class="text-white text-center p-2">
                    <i class="fas fa-video text-2xl mb-1 text-pp-400"></i>
                    <span class="text-[10px] font-bold block truncate max-w-[80px]">{{ $photo->getClientOriginalName() }}</span>
                  </div>
                @else
                  <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" alt="Item preview" />
                @endif

                <button type="button" wire:click="removePhoto({{ $index }})" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white grid place-items-center text-[10px] shadow-sm transition cursor-pointer">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- STEP 1 FOOTER BUTTONS -->
      <div class="border-t border-slate-100 pt-5 flex flex-wrap items-center justify-between gap-3">
        <button type="button" wire:click="saveUnlisted" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
          💾 Save without Listing
        </button>

        <div class="flex items-center gap-2.5">
          <button type="button" wire:click="skipStep2ToStep3" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
            <span>List Item →</span>
          </button>

          @if($item_type === 'scrap')
            <button type="button" wire:click="goToStep2" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <span>Proceed to Disassembly Matrix →</span>
            </button>
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- STEP 2: DISASSEMBLY & COMPONENT HARVESTING MATRIX -->
  @if($step === 2)
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-6 shadow-soft">
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-microchip text-amber-600"></i> Step 2: Component Disassembly &amp; Harvesting Matrix
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Define harvested sub-parts from this scrap unit or choose to list the whole unit as-is.</p>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold uppercase">Salvage Dismantling</span>
      </div>

      <!-- NON-TECHNICIAN ABEL BANNER: AS-IS TOGGLE -->
      <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-xl bg-amber-200 text-amber-900 grid place-items-center text-sm font-bold shrink-0 mt-0.5">
            💡
          </div>
          <div>
            <h4 class="text-xs font-extrabold text-amber-950">Don't know the exact components inside?</h4>
            <p class="text-[11px] text-amber-900">You can list this whole scrap unit as-is without specifying individual internal parts.</p>
          </div>
        </div>

        <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3 py-2 rounded-xl border border-amber-300 shadow-2xs shrink-0">
          <input type="checkbox" wire:model.live="as_is_scrap" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500" />
          <span class="text-xs font-extrabold text-amber-950">List Whole Scrap Unit As-Is</span>
        </label>
      </div>

      @if(!$as_is_scrap)
        <!-- SMART COMPONENT SUGGESTION CHIPS -->
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-700">Quick-Add Suggested Components for this Category:</label>
          </div>
          <div class="flex flex-wrap gap-2">
            @foreach($this->suggested_component_chips as $chip)
              <button type="button" wire:click="addSuggestedComponent('{{ addslashes($chip) }}')" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-pp-50 hover:border-pp-300 hover:text-pp-700 text-xs font-semibold text-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                <span>+ {{ $chip }}</span>
              </button>
            @endforeach
          </div>
        </div>

        <!-- HARVEST MATRIX TABLE -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-900">Harvestable Component Parts</h3>
            <button type="button" wire:click="addCustomComponentRow" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1 cursor-pointer">
              <i class="fas fa-plus text-[10px]"></i> Add Custom Component Row
            </button>
          </div>

          <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-xs">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
                  <th class="p-3">Component Part Name</th>
                  <th class="p-3 w-44">Condition Status</th>
                  <th class="p-3">Harvest Notes</th>
                  <th class="p-3 text-center w-16">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach($harvested_components as $index => $comp)
                  <tr class="hover:bg-slate-50/50 transition">
                    <!-- COMPONENT NAME -->
                    <td class="p-3">
                      <input type="text" wire:model="harvested_components.{{ $index }}.name" placeholder="Component Name (e.g. Display Assembly)" class="w-full p-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                    </td>

                    <!-- CONDITION STATUS -->
                    <td class="p-3">
                      <select wire:model="harvested_components.{{ $index }}.condition_status" class="w-full p-2.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition">
                        <option value="Testing working">Testing working</option>
                        <option value="Untested">Untested</option>
                        <option value="Repaired">Repaired</option>
                      </select>
                    </td>

                    <!-- HARVEST NOTES -->
                    <td class="p-3">
                      <input type="text" wire:model="harvested_components.{{ $index }}.notes" placeholder="e.g. Tested fine, minor scuff" class="w-full p-2.5 rounded-lg border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500 transition" />
                    </td>

                    <!-- ACTION (REMOVE ROW) -->
                    <td class="p-3 text-center">
                      <button type="button" wire:click="removeComponentRow({{ $index }})" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                        <i class="fas fa-trash-alt text-xs"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- STEP 2 FOOTER BUTTONS -->
      <div class="border-t border-slate-100 pt-5 flex flex-wrap items-center justify-between gap-3">
        <button type="button" wire:click="backStep" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
          ← Back to Step 1
        </button>

        <div class="flex items-center gap-2.5">
          <button type="button" wire:click="saveUnlisted" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
            💾 Save without Listing
          </button>

          <button type="button" wire:click="goToStep3" class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition flex items-center gap-2 cursor-pointer">
            <span>Proceed to Listing →</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- STEP 3: PRICING, LOCATION, WARRANTY & PUBLISHING -->
  @if($step === 3)
    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-8 shadow-soft">
      
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-tag text-pp-600"></i> Step 3: Marketplace Pricing &amp; Warranty
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Configure pricing and warranty terms for your marketplace listing.</p>
        </div>
        <span class="text-xs text-slate-400 font-semibold">Final Marketplace Setup</span>
      </div>

      <!-- FOR SCRAP ITEMS: LISTING TOGGLES -->
      @if($item_type === 'scrap')
        @php
          $validComponentsCount = collect($harvested_components)->filter(fn($c) => !empty(trim($c['name'] ?? '')))->count();
          $hasComponents = !$as_is_scrap && $disassemble_mode && $validComponentsCount > 0;
        @endphp
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Salvage Unit Marketplace Options</h3>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="flex items-center gap-3 p-3 rounded-xl border transition {{ !$hasComponents ? 'bg-slate-100 border-slate-200 opacity-60 cursor-not-allowed select-none' : 'bg-white border-slate-200 cursor-pointer hover:border-pp-300' }}">
              <input type="checkbox" wire:model.live="include_whole_listing" {{ !$hasComponents ? 'disabled checked' : '' }} class="w-4 h-4 text-pp-600 rounded border-slate-300 focus:ring-pp-500 {{ !$hasComponents ? 'cursor-not-allowed' : '' }}" />
              <div>
                <span class="text-xs font-bold text-slate-900 block">Publish Whole Scrap Unit Listing</span>
                <span class="text-[10px] text-slate-500">
                  @if(!$hasComponents)
                    Whole unit listing is required when no components are harvested
                  @else
                    List entire device unit as a single scrap item
                  @endif
                </span>
              </div>
            </label>

            @if($hasComponents)
              <label class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer transition hover:border-pp-300">
                <input type="checkbox" wire:model.live="include_component_listings" class="w-4 h-4 text-pp-600 rounded border-slate-300 focus:ring-pp-500" />
                <div>
                  <span class="text-xs font-bold text-slate-900 block">Add listing for individual components</span>
                  <span class="text-[10px] text-slate-500">Publish separate component listings from disassembly matrix</span>
                </div>
              </label>
            @endif
          </div>
        </div>
      @endif

      <!-- SECTION 1: MARKETPLACE PRICING & STOCK QUANTITY -->
      <div class="space-y-4">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
          <span class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 text-[10px] grid place-items-center font-black">1</span>
          <span>Marketplace Pricing &amp; Stock Quantity</span>
        </h3>

        @if($item_type === 'scrap')
          @if($include_whole_listing)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Whole Scrap Unit Listing Price (₦) <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-3 text-slate-400 font-bold text-xs">₦</span>
                  <input type="number" wire:model="price" placeholder="150000" class="w-full pl-8 p-3 rounded-xl border border-slate-200 text-sm font-extrabold text-slate-950 outline-none focus:border-pp-500 transition" />
                </div>
                @error('price') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Stock Quantity <span class="text-rose-500">*</span></label>
                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 flex items-center justify-between h-[46px]">
                  <span>1 Unit (Unique Salvage Asset)</span>
                  <span class="text-[10px] font-normal text-slate-500">(Scrap Unit)</span>
                </div>
              </div>
            </div>
          @endif

          @if(!$as_is_scrap && $disassemble_mode && $include_component_listings && !empty($harvested_components))
            <div class="space-y-2 pt-2">
              <label class="block text-xs font-bold text-slate-800">Individual Component Pricing Matrix</label>
              <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-xs">
                <table class="w-full text-left text-xs border-collapse">
                  <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
                      <th class="p-3">Component Name</th>
                      <th class="p-3 w-36">Condition</th>
                      <th class="p-3 w-44">Listing Price (₦)</th>
                      <th class="p-3 text-center w-28">List for Sale?</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    @foreach($harvested_components as $index => $comp)
                      <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-3 font-bold text-slate-900">{{ $comp['name'] ?: 'Unnamed Component' }}</td>
                        <td class="p-3 text-slate-600 font-semibold">{{ $comp['condition_status'] ?? 'Testing working' }}</td>
                        <td class="p-3">
                          <div class="relative">
                            <span class="absolute left-2.5 top-2.5 text-slate-400 font-bold text-xs">₦</span>
                            <input type="number" wire:model="harvested_components.{{ $index }}.price" placeholder="0" class="w-full pl-7 p-2 rounded-lg border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 transition" />
                          </div>
                        </td>
                        <td class="p-3 text-center">
                          <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="harvested_components.{{ $index }}.list_for_sale" class="w-4 h-4 text-pp-600 rounded border-slate-300 focus:ring-pp-500" />
                          </label>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @endif
        @else
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Unit Listing Price (₦) <span class="text-rose-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-slate-400 font-bold text-xs">₦</span>
                <input type="number" wire:model="price" placeholder="150000" class="w-full pl-8 p-3 rounded-xl border border-slate-200 text-sm font-extrabold text-slate-950 outline-none focus:border-pp-500 transition" />
              </div>
              @error('price') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Stock Quantity <span class="text-rose-500">*</span></label>
              <input type="number" wire:model="quantity" min="1" placeholder="1" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-950 outline-none focus:border-pp-500 transition" />
              @error('quantity') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
          </div>
        @endif
      </div>

      <!-- SECTION 2: WARRANTY TERMS & GUARANTEES -->
      <div class="space-y-4">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
          <span class="w-5 h-5 rounded-full bg-pp-100 text-pp-700 text-[10px] grid place-items-center font-black">2</span>
          <span>Warranty &amp; Return Terms</span>
        </h3>

        @if($item_type === 'scrap' && !$as_is_scrap && $disassemble_mode && $include_component_listings && !empty($harvested_components))
          <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-800">Individual Component Warranty Matrix</label>
            <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-xs">
              <table class="w-full text-left text-xs border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
                    <th class="p-3">Component Name</th>
                    <th class="p-3 w-32">Condition</th>
                    <th class="p-3 w-36">Warranty (Days)</th>
                    <th class="p-3">Warranty Terms &amp; Conditions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach($harvested_components as $index => $comp)
                    <tr class="hover:bg-slate-50/50 transition">
                      <td class="p-3 font-bold text-slate-900">{{ $comp['name'] ?: 'Unnamed Component' }}</td>
                      <td class="p-3 text-slate-600 font-semibold">{{ $comp['condition_status'] ?? 'Testing working' }}</td>
                      <td class="p-3">
                        <input type="number" wire:model="harvested_components.{{ $index }}.warranty_period_days" placeholder="0" class="w-full p-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
                      </td>
                      <td class="p-3">
                        <input type="text" wire:model="harvested_components.{{ $index }}.warranty_terms" placeholder="e.g. 7-day testing warranty" class="w-full p-2 rounded-lg border border-slate-200 text-xs text-slate-800 outline-none focus:border-pp-500 transition" />
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Warranty Period (Days)</label>
              <input type="number" wire:model="warranty_period_days" placeholder="0" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Warranty Terms &amp; Conditions</label>
              <input type="text" wire:model="warranty_terms" placeholder="e.g. 7-day money back if unworked" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
          </div>
        @endif
      </div>

      <!-- STEP 3 FOOTER BUTTONS -->
      <div class="border-t border-slate-100 pt-5 flex items-center justify-between gap-3">
        <button type="button" wire:click="backStep" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
          ← Back to Previous Step
        </button>

        <button type="button" wire:click="submitListing" class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-sm hover:shadow transition flex items-center gap-2 cursor-pointer">
          <i class="fas fa-check-circle"></i>
          <span>Publish Listing(s) to Marketplace</span>
        </button>
      </div>

    </div>
  @endif

  <!-- WEBRTC CAMERA CAPTURE MODAL DIALOG -->
  <div x-show="cameraModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 backdrop-blur-xs p-4">
    <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl max-w-xl w-full p-5 space-y-4 text-white relative">
      <!-- HEADER & MODE TOGGLE TABS -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-pp-600 text-white grid place-items-center text-sm font-black">
            <i class="fas" :class="captureMode === 'video' ? 'fa-video' : 'fa-camera'"></i>
          </div>
          <div>
            <h3 class="text-sm font-extrabold" x-text="captureMode === 'video' ? 'Video Recording' : 'Camera Snapshot'"></h3>
            <p class="text-[11px] text-slate-400" x-text="captureMode === 'video' ? 'Record short video clips of your item' : 'Capture photo directly using browser camera'"></p>
          </div>
        </div>

        <!-- MODE SWITCHER -->
        <div class="flex items-center bg-slate-800 p-1 rounded-xl shrink-0" x-show="!isRecording">
          <button type="button" @click="switchMode('photo')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer" :class="captureMode === 'photo' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'">
            <i class="fas fa-camera text-[10px] mr-1"></i> Photo
          </button>
          <button type="button" @click="switchMode('video')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer" :class="captureMode === 'video' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'">
            <i class="fas fa-video text-[10px] mr-1"></i> Video
          </button>
        </div>

        <button type="button" @click="closeCamera" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 grid place-items-center transition cursor-pointer">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>

      <div class="relative bg-black rounded-2xl overflow-hidden aspect-video grid place-items-center border border-slate-800">
        <video x-ref="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>

        <!-- RECORDING OVERLAY BADGE -->
        <div x-show="isRecording" x-cloak class="absolute top-3 left-3 bg-rose-600/90 text-white text-xs font-extrabold px-3 py-1.5 rounded-full flex items-center gap-2 backdrop-blur-xs shadow-md animate-pulse">
          <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
          <span>REC <span x-text="formatTime(recordingSeconds)"></span></span>
        </div>
      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" @click="closeCamera" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 transition cursor-pointer">
          Cancel
        </button>

        <!-- PHOTO MODE ACTION -->
        <template x-if="captureMode === 'photo'">
          <button type="button" @click="takeSnapshot" class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-2 cursor-pointer">
            <i class="fas fa-camera text-sm"></i> Snap Photo
          </button>
        </template>

        <!-- VIDEO MODE ACTIONS -->
        <template x-if="captureMode === 'video'">
          <div>
            <button x-show="!isRecording" type="button" @click="startRecording" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-2 cursor-pointer">
              <i class="fas fa-circle text-xs text-white"></i> Start Recording
            </button>
            <button x-show="isRecording" type="button" @click="stopRecording(true)" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-2 cursor-pointer">
              <i class="fas fa-square text-xs"></i> Stop &amp; Save Video
            </button>
          </div>
        </template>
      </div>
    </div>
  </div>

  <!-- ADD LOCATION MODAL DIALOG -->
  @if($showLocationModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-950">Add New Stock Location</h3>
              <p class="text-[11px] text-slate-500">Save a workshop, warehouse, or store location</p>
            </div>
          </div>
          <button type="button" wire:click="closeLocationModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Location Label <span class="text-rose-500">*</span></label>
            <input type="text" wire:model="newLocationLabel" placeholder="e.g. Main Shop, Ikeja Warehouse" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
            @error('newLocationLabel') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Street Address <span class="text-rose-500">*</span></label>
            <input type="text" wire:model="newLocationAddress" placeholder="e.g. Shop B12, Computer Village" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
            @error('newLocationAddress') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">City <span class="text-rose-500">*</span></label>
              <input type="text" wire:model="newLocationCity" placeholder="Ikeja" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('newLocationCity') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">State <span class="text-rose-500">*</span></label>
              <input type="text" wire:model="newLocationState" placeholder="Lagos" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition" />
              @error('newLocationState') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Contact Name</label>
              <input type="text" wire:model="newLocationContactName" placeholder="Full Name" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Contact Phone</label>
              <input type="text" wire:model="newLocationPhone" placeholder="+234 800 000 0000" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
          </div>
        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
          <button type="button" wire:click="closeLocationModal" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
            Cancel
          </button>
          <button type="button" wire:click="createLocation" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5">
            <i class="fas fa-check text-[10px]"></i> Save &amp; Select Location
          </button>
        </div>
      </div>
    </div>
  @endif

</div>
