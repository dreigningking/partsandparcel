<div class="flex flex-col gap-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Saved Delivery &amp; Pickup Locations</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your saved shop, workshop, warehouse, and pickup destination addresses.</p>
    </div>

    <button
      type="button"
      wire:click="openCreateModal"
      class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer self-start sm:self-auto"
    >
      <i class="fas fa-plus"></i>
      <span>Add New Location</span>
    </button>
  </div>

  <!-- FLASH MESSAGE -->
  @if(session()->has('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- SEARCH & STATS BAR -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="relative w-full sm:w-80">
      <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
      <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="Filter by label, city, address, or state..."
        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-pp-500 transition"
      />
      @if($search !== '')
        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
          <i class="fas fa-times"></i>
        </button>
      @endif
    </div>

    <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-pp-600"></span>
        <span>Total: <strong>{{ $totalCount }}</strong> location{{ $totalCount === 1 ? '' : 's' }}</span>
      </div>
      @if($defaultLocation)
        <div class="hidden md:flex items-center gap-1.5 text-slate-500">
          <i class="fas fa-star text-amber-500 text-[11px]"></i>
          <span>Primary: <strong class="text-slate-900">{{ $defaultLocation->label }}</strong> ({{ $defaultLocation->city }})</span>
        </div>
      @endif
    </div>
  </div>

  <!-- LOCATIONS GRID -->
  @if($locations->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-soft space-y-4">
      <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center mx-auto text-2xl shadow-xs">
        <i class="fas fa-map-marked-alt"></i>
      </div>
      <div class="max-w-md mx-auto space-y-1">
        <h3 class="text-base font-extrabold text-slate-900">
          {{ $search !== '' ? 'No matching locations found' : 'No locations added yet' }}
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed">
          {{ $search !== '' ? 'Try adjusting your search keywords to locate your saved addresses.' : 'Add your workshop, warehouse, shop or home pickup address. You can choose any saved location when listing inventory or fulfilling deliveries.' }}
        </p>
      </div>
      @if($search === '')
        <div>
          <button
            type="button"
            wire:click="openCreateModal"
            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-2 cursor-pointer"
          >
            <i class="fas fa-plus"></i> Add Your First Location
          </button>
        </div>
      @endif
    </div>
  @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($locations as $loc)
        <div
          wire:key="loc-card-{{ $loc->id }}"
          class="bg-white rounded-3xl p-6 space-y-4 shadow-soft flex flex-col justify-between transition-all duration-150 {{ $loc->is_default ? 'border-2 border-pp-500 ring-4 ring-pp-500/10' : 'border border-slate-200 hover:border-slate-300' }}"
        >
          <div class="space-y-3.5">
            <!-- CARD TOP BADGES -->
            <div class="flex items-center justify-between gap-2">
              <span class="px-2.5 py-0.5 rounded-full font-black text-[10px] uppercase tracking-wider {{ $loc->is_default ? 'bg-pp-100 text-pp-800' : 'bg-slate-100 text-slate-600' }}">
                {{ $loc->is_default ? 'Primary Store / Address' : 'Additional Location' }}
              </span>

              @if($loc->is_default)
                <span class="text-xs font-bold text-pp-600 flex items-center gap-1">
                  <i class="fas fa-check-circle text-[11px]"></i> Default
                </span>
              @else
                <button
                  type="button"
                  wire:click="setDefault({{ $loc->id }})"
                  class="text-xs font-bold text-slate-500 hover:text-pp-600 hover:underline cursor-pointer flex items-center gap-1 transition"
                  title="Make this the default address"
                >
                  <i class="far fa-star text-[11px]"></i> Set Default
                </button>
              @endif
            </div>

            <!-- LOCATION TITLE & CITY/STATE -->
            <div>
              <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>{{ $loc->label }}</span>
              </h3>
              <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                <i class="fas fa-map-marker-alt text-pp-600 text-[11px]"></i>
                <span class="font-semibold text-slate-700">{{ $loc->city }}</span>,
                <span>{{ $loc->state?->name ?? 'State Unspecified' }}</span>
                @if($loc->country)
                  <span class="text-slate-400 font-normal">({{ $loc->country->name }})</span>
                @endif
              </p>
            </div>

            <!-- COORDINATES PILL (LAT & LONG) -->
            @if($loc->latitude && $loc->longitude)
              <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-100 text-slate-600 text-[10px] font-mono">
                <i class="fas fa-crosshairs text-pp-500 text-[9px]"></i>
                <span>Lat: {{ number_format((float)$loc->latitude, 4) }}</span>
                <span class="text-slate-300">|</span>
                <span>Lng: {{ number_format((float)$loc->longitude, 4) }}</span>
              </div>
            @endif

            <!-- ADDRESS BOX -->
            <div class="text-xs text-slate-700 leading-relaxed bg-slate-50/80 p-3 rounded-2xl border border-slate-100 space-y-1">
              <div class="font-medium text-slate-900">{{ $loc->address_line_1 }}</div>
              @if($loc->address_line_2)
                <div class="text-slate-500 text-[11px]">{{ $loc->address_line_2 }}</div>
              @endif
              @if($loc->postal_code)
                <div class="text-[10px] text-slate-400 font-mono">Postal Code: {{ $loc->postal_code }}</div>
              @endif
            </div>

            <!-- CONTACT DETAILS -->
            @if($loc->contact_name || $loc->phone)
              <div class="pt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                @if($loc->contact_name)
                  <span class="flex items-center gap-1">
                    <i class="fas fa-user text-slate-400 text-[10px]"></i>
                    <span class="font-semibold text-slate-700">{{ $loc->contact_name }}</span>
                  </span>
                @endif
                @if($loc->phone)
                  <span class="flex items-center gap-1">
                    <i class="fas fa-phone text-slate-400 text-[10px]"></i>
                    <span>{{ $loc->phone }}</span>
                  </span>
                @endif
              </div>
            @endif
          </div>

          <!-- CARD ACTIONS -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold mt-2">
            <button
              type="button"
              wire:click="openEditModal({{ $loc->id }})"
              class="text-pp-600 hover:text-pp-700 hover:underline cursor-pointer flex items-center gap-1"
            >
              <i class="fas fa-edit text-[10px]"></i> Edit Details
            </button>

            <button
              type="button"
              wire:click="confirmDelete({{ $loc->id }})"
              class="text-rose-500 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1 transition"
            >
              <i class="fas fa-trash-alt text-[10px]"></i> Delete
            </button>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <!-- ADD / EDIT LOCATION MODAL -->
  @if($showLocationModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5 relative max-h-[90vh] overflow-y-auto">
        
        <!-- MODAL HEADER -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm font-black">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-950">
                {{ $editingLocationId ? 'Edit Location Details' : 'Add New Location' }}
              </h3>
              <p class="text-[11px] text-slate-500">
                {{ $editingLocationId ? 'Update your address and contact coordinates' : 'Save a workshop, warehouse, shop or home pickup address' }}
              </p>
            </div>
          </div>
          <button
            type="button"
            wire:click="closeModal"
            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 grid place-items-center transition cursor-pointer"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- MODAL FORM BODY -->
        <form wire:submit.prevent="saveLocation" class="space-y-4 text-xs">
          
          <!-- LOCATION LABEL -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">
              Location Name / Label <span class="text-rose-500">*</span>
            </label>
            <input
              type="text"
              wire:model="label"
              placeholder="e.g. Computer Village Workshop, Ikeja Warehouse, Main Shop"
              class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-pp-500 transition"
            />
            @error('label') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- STREET ADDRESS -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">
              Street Address <span class="text-rose-500">*</span>
            </label>
            <input
              type="text"
              wire:model="address_line_1"
              placeholder="e.g. 14 Medical Road, Plaza 3"
              class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"
            />
            @error('address_line_1') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- ADDRESS LINE 2 (OPTIONAL) -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">
              Apartment, Suite, Unit, Landmark <span class="text-slate-400 font-normal lowercase">(optional)</span>
            </label>
            <input
              type="text"
              wire:model="address_line_2"
              placeholder="e.g. Shop B12, Behind Zenith Bank"
              class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"
            />
          </div>

          <!-- CITY & STATE -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">
                City <span class="text-rose-500">*</span>
              </label>
              <input
                type="text"
                wire:model="city"
                placeholder="e.g. Ikeja"
                class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 outline-none focus:border-pp-500 transition"
              />
              @error('city') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">
                State <span class="text-rose-500">*</span>
              </label>
              <x-searchable-select
                wire:model="state_id"
                :options="$states->map(fn($s) => ['value' => $s->id, 'label' => $s->name])"
                placeholder="Select State"
                search-placeholder="Search states..."
              />
              @error('state_id') <span class="text-[10px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
          </div>

          <!-- CONTACT DETAILS & POSTAL CODE -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Contact Name</label>
              <input
                type="text"
                wire:model="contact_name"
                placeholder="Full Name"
                class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"
              />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Contact Phone</label>
              <input
                type="text"
                wire:model="phone"
                placeholder="+234 800 000 0000"
                class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"
              />
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Postal Code <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
            <input
              type="text"
              wire:model="postal_code"
              placeholder="e.g. 100001"
              class="w-full p-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 transition"
            />
          </div>

          <!-- DEFAULT ADDRESS TOGGLE -->
          <div class="pt-2">
            <label class="flex items-start gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
              <input
                type="checkbox"
                wire:model="is_default"
                class="w-4 h-4 mt-0.5 text-pp-600 rounded border-slate-300 focus:ring-pp-500 cursor-pointer"
              />
              <div>
                <span class="text-xs font-bold text-slate-900 block">Set as Primary / Default Location</span>
                <p class="text-[11px] text-slate-500 mt-0.5">
                  This address will be selected automatically when listing inventory or checking out.
                </p>
              </div>
            </label>
          </div>

          <!-- MODAL BUTTONS -->
          <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2.5">
            <button
              type="button"
              wire:click="closeModal"
              class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              wire:loading.attr="disabled"
              class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer flex items-center gap-1.5"
            >
              <i class="fas fa-check text-[10px]"></i>
              <span wire:loading.remove>{{ $editingLocationId ? 'Save Changes' : 'Save Location' }}</span>
              <span wire:loading>Saving...</span>
            </button>
          </div>
        </form>

      </div>
    </div>
  @endif

  <!-- DELETE CONFIRMATION MODAL -->
  @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 space-y-4 relative text-center">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 grid place-items-center mx-auto text-xl">
          <i class="fas fa-trash-alt"></i>
        </div>
        <div class="space-y-1">
          <h3 class="text-sm font-extrabold text-slate-950">Delete This Location?</h3>
          <p class="text-xs text-slate-500">
            Are you sure you want to remove this saved address? Any items currently assigned to it will retain their record, but this address will no longer be available for new listings.
          </p>
        </div>
        <div class="pt-2 flex items-center justify-center gap-2.5">
          <button
            type="button"
            wire:click="cancelDelete"
            class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="deleteLocation({{ $confirmingDeleteId }})"
            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-xs transition cursor-pointer"
          >
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
  @endif

</div>