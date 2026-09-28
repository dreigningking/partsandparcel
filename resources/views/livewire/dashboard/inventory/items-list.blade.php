<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-extrabold text-pp-600 uppercase tracking-wider">Inventory &amp; Asset Stream</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950">Registered Inventory &amp; Disassembly Matrix</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage complete devices, harvested component parts, location assignments, and marketplace listings.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('item.create') }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
        <i class="fas fa-plus"></i> Register New Item / Device
      </a>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- FILTER CONTROLS BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
    <div class="flex items-center justify-between flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-filter text-pp-600"></i> Filter Inventory
      </h3>

      @if($search || $selectedCategory || $selectedBrand || $selectedCondition || $selectedLocation || $selectedStatus)
        <button type="button" wire:click="resetFilters" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1 cursor-pointer">
          <i class="fas fa-undo text-[10px]"></i> Reset Filters
        </button>
      @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 text-xs">
      
      <!-- 1. SEARCH INPUT (TITLE / MODEL / ANYTHING) -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Search</label>
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input type="text" wire:model.live.debounce.300ms="search" placeholder="Title, model, brand..." class="w-full pl-8 p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 bg-slate-50/50 transition" />
        </div>
      </div>

      <!-- 2. CATEGORY FILTER -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Category</label>
        <select wire:model.live="selectedCategory" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 3. BRAND FILTER -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Brand</label>
        <select wire:model.live="selectedBrand" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Brands</option>
          @foreach($brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 4. CONDITION STATUS FILTER -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Condition Status</label>
        <select wire:model.live="selectedCondition" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Conditions</option>
          <option value="new">🌟 Brand New</option>
          <option value="used">✅ Used - Working</option>
          <option value="refurbished">🛠 Refurbished</option>
          <option value="faulty">🛠️ Scrap / For Parts</option>
        </select>
      </div>

      <!-- 5. LOCATION FILTER -->
      <div>
        <label class="block font-bold text-slate-700 mb-1">Location</label>
        <select wire:model.live="selectedLocation" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 bg-slate-50/50 transition">
          <option value="">All Locations</option>
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}">{{ $loc->label }} ({{ $loc->city }})</option>
          @endforeach
        </select>
      </div>

    </div>
  </div>

  <!-- INVENTORY TABLE CONTAINER -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
      <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <i class="fas fa-boxes text-pp-600"></i> Registered Items Stream ({{ $items->total() }})
      </h3>
      <span class="text-xs text-slate-400 font-semibold">Page {{ $items->currentPage() }} of {{ $items->lastPage() ?: 1 }}</span>
    </div>

    <!-- RESPONSIVE TABLE WRAPPER -->
    <div class="overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-xs">
      <table class="w-full text-left text-xs border-collapse min-w-[750px]">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider">
            <th class="p-3.5 min-w-[200px]">Item Name / Title</th>
            <th class="p-3.5 min-w-[120px]">Category</th>
            <th class="p-3.5 min-w-[150px]">Brand &amp; Model</th>
            <th class="p-3.5 min-w-[140px]">Condition</th>
            <th class="p-3.5 min-w-[130px]">Location</th>
            <th class="p-3.5 min-w-[120px]">Status</th>
            <th class="p-3.5 text-center min-w-[140px]">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
          @forelse($items as $item)
            <tr class="hover:bg-slate-50/80 transition">
              
              <!-- 1. ITEM NAME / TITLE -->
              <td class="p-3.5">
                <a href="{{ route('item.view', ['id' => $item->id]) }}" class="font-extrabold text-slate-900 hover:text-pp-600 transition block text-xs">
                  {{ $item->name ?: ($item->deviceModel?->name ?? 'Inventory Asset #' . $item->id) }}
                </a>
                @if($item->condition_notes)
                  <span class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">{{ $item->condition_notes }}</span>
                @endif
              </td>

              <!-- 2. CATEGORY -->
              <td class="p-3.5">
                <span class="px-2.5 py-1 rounded-md bg-slate-100 font-bold text-slate-700 text-[10px] inline-block">
                  {{ $item->deviceModel?->category?->name ?? 'General' }}
                </span>
              </td>

              <!-- 3. BRAND & MODEL (COMBINED IN ONE COLUMN) -->
              <td class="p-3.5">
                <span class="font-extrabold text-slate-900 block text-xs">
                  {{ $item->deviceModel?->brand?->name ?? '—' }}
                </span>
                <span class="text-[11px] text-slate-500 font-medium block">
                  {{ $item->deviceModel?->name ?? 'Unspecified Model' }}
                </span>
              </td>

              <!-- 4. CONDITION -->
              <td class="p-3.5">
                @if($item->condition_status === 'new')
                  <span class="px-2.5 py-1 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🌟 Brand New
                  </span>
                @elseif($item->condition_status === 'used' || $item->condition_status === 'working')
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    ✅ Used - Working
                  </span>
                @elseif($item->condition_status === 'refurbished')
                  <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 font-extrabold text-[10px] inline-flex items-center gap-1">
                    🛠 Refurbished
                  </span>
                @elseif($item->condition_status === 'faulty' || $item->condition_status === 'scrap')
                  <div class="flex flex-col gap-0.5">
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] inline-flex items-center gap-1 w-fit">
                      🛠️ Scrap / For Parts
                    </span>
                    @if($item->components->count() > 0)
                      <span class="text-[10px] text-amber-700 font-bold">({{ $item->components->count() }} parts harvested)</span>
                    @endif
                  </div>
                @else
                  <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]">
                    {{ ucfirst($item->condition_status) }}
                  </span>
                @endif
              </td>

              <!-- 5. LOCATION -->
              <td class="p-3.5">
                @php
                  $loc = $item->listing?->location;
                  if (!$loc && $item->components->isNotEmpty()) {
                      $loc = $item->components->first()?->listing?->location;
                  }
                @endphp

                @if($loc)
                  <span class="text-xs font-bold text-slate-800 block">{{ $loc->label }}</span>
                  <span class="text-[10px] text-slate-500 block">{{ $loc->city }}, {{ $loc->state }}</span>
                @else
                  <span class="text-slate-400 text-xs italic">Unassigned</span>
                @endif
              </td>

              <!-- 6. STATUS -->
              <td class="p-3.5">
                @php
                  $hasWholeListing = (bool) $item->listing;
                  $listedCompCount = $item->components->filter(fn($c) => $c->listing)->count();
                @endphp

                @if($hasWholeListing && $listedCompCount > 0)
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] inline-block">
                    Listed (Whole + Parts)
                  </span>
                @elseif($hasWholeListing)
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] inline-block">
                    Listed (Whole Unit)
                  </span>
                @elseif($listedCompCount > 0)
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] inline-block">
                    Listed ({{ $listedCompCount }} Parts)
                  </span>
                @else
                  <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] inline-block">
                    Unlisted Asset
                  </span>
                @endif
              </td>

              <!-- 7. ACTION BUTTONS -->
              <td class="p-3.5 text-center">
                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                  
                  <!-- LIST BUTTON (FOR UNLISTED ITEMS) -->
                  @if(!$hasWholeListing && $listedCompCount === 0)
                    <button type="button" wire:click="openCreateListingModal({{ $item->id }})" class="px-2.5 py-1 rounded-lg bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-[10px] transition shadow-2xs cursor-pointer flex items-center gap-1" title="Publish Marketplace Listing">
                      <i class="fas fa-tag"></i> List
                    </button>
                  @endif

                  <!-- DISASSEMBLY (ABEL / SCRAP ONLY) -->
                  @if($item->condition_status === 'scrap' || $item->condition_status === 'faulty')
                    <a href="{{ route('item.view', ['id' => $item->id]) }}" class="px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-[10px] transition shadow-2xs" title="Disassembly Matrix">
                      <i class="fas fa-microchip mr-0.5"></i> Disassembly
                    </a>
                  @endif

                  <!-- EDIT -->
                  <a href="{{ route('item.view', ['id' => $item->id]) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-700 text-[10px] transition" title="Edit / View Details">
                    <i class="fas fa-edit mr-0.5"></i> Edit
                  </a>

                  <!-- DELETE -->
                  <button type="button" wire:click="deleteItem({{ $item->id }})" wire:confirm="Are you sure you want to delete this inventory item and all associated listings?" class="px-2.5 py-1 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[10px] transition cursor-pointer" title="Delete Item">
                    <i class="fas fa-trash-alt mr-0.5"></i> Delete
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="p-8 text-center text-slate-400 font-semibold">
                <div class="max-w-xs mx-auto space-y-2">
                  <i class="fas fa-inbox text-3xl text-slate-300 block"></i>
                  <p class="text-xs font-bold text-slate-600">No inventory items match your search filters.</p>
                  <button type="button" wire:click="resetFilters" class="text-xs font-bold text-pp-600 hover:underline cursor-pointer">
                    Clear all filters
                  </button>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- PAGINATION LINKS -->
    @if($items->hasPages())
      <div class="pt-3 border-t border-slate-100">
        {{ $items->links() }}
      </div>
    @endif

  </div>

  <!-- CREATE NEW LISTING MODAL DIALOG -->
  @if($showCreateListingModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black">
              <i class="fas fa-tag"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-950">Publish Marketplace Listing</h3>
              <p class="text-[11px] text-slate-500">Select an unlisted asset or component to list for sale</p>
            </div>
          </div>
          <button type="button" wire:click="closeCreateListingModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <div class="space-y-4 text-xs">
          
          <!-- 1. SEARCHABLE DROPDOWN SELECT FOR ITEM / COMPONENT -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Select Item / Component to List <span class="text-rose-500">*</span></label>
            <select wire:model.live="selectedAssetKey" class="w-full p-3 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 bg-white transition">
              <option value="">-- Choose Unlisted Item or Component --</option>
              @if(!empty($unlistedAssets['items']) && count($unlistedAssets['items']) > 0)
                <optgroup label="📦 Complete Devices / Items">
                  @foreach($unlistedAssets['items'] as $uItem)
                    <option value="item_{{ $uItem['id'] }}">{{ $uItem['name'] }} ({{ ucfirst($uItem['condition']) }})</option>
                  @endforeach
                </optgroup>
              @endif
              @if(!empty($unlistedAssets['components']) && count($unlistedAssets['components']) > 0)
                <optgroup label="🧩 Harvested Sub-Components">
                  @foreach($unlistedAssets['components'] as $uComp)
                    <option value="component_{{ $uComp['id'] }}">{{ $uComp['name'] }} (Component of {{ $uComp['parent_item'] }})</option>
                  @endforeach
                </optgroup>
              @endif
            </select>
            @error('selectedAssetKey') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- 2. PRICE & QUANTITY -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Listing Price (₦) <span class="text-rose-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₦</span>
                <input type="number" wire:model="price" placeholder="0.00" class="w-full pl-8 p-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 outline-none focus:border-pp-500 transition" />
              </div>
              @error('price') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Quantity <span class="text-rose-500">*</span></label>
              <input type="number" wire:model="quantity" min="1" {{ $isQuantityDisabled ? 'disabled' : '' }} class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-extrabold outline-none focus:border-pp-500 transition {{ $isQuantityDisabled ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' }}" />
              @if($isQuantityDisabled)
                <span class="text-[9px] text-slate-500 font-semibold block mt-0.5">Quantity defaults to 1 for components.</span>
              @endif
              @error('quantity') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
          </div>

          <!-- 3. WARRANTY PERIOD & TERMS -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Warranty Period (Days)</label>
              <input type="number" wire:model="warranty_period_days" placeholder="0" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Warranty Terms</label>
              <input type="text" wire:model="warranty_terms" placeholder="e.g. 7-day inspection warranty" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition" />
            </div>
          </div>

        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
          <button type="button" wire:click="closeCreateListingModal" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
            Cancel
          </button>
          <button type="button" wire:click="createListing" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5">
            <i class="fas fa-check text-[10px]"></i> Publish Listing
          </button>
        </div>
      </div>
    </div>
  @endif

</div>