<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-extrabold text-slate-900">Filter Category Requests</h3>
        <button type="button" wire:click="resetFilters" class="text-[11px] font-bold text-pp-600 hover:underline cursor-pointer">Reset All</button>
    </div>

    <!-- SEARCH (FIRST ELEMENT) -->
    <div>
        <label for="filterSearchCategoryRequests_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Search</label>
        <div class="relative">
            <input type="text" id="filterSearchCategoryRequests_{{ $isMobile ? 'mobile' : 'desktop' }}" wire:model.live.debounce.300ms="search" placeholder="Search category requests..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <i class="fas fa-search absolute left-2.5 top-3 text-slate-400 text-xs"></i>
        </div>
    </div>

    <!-- BRAND FILTER -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Filter Brands</label>
        <div class="space-y-2 text-xs text-slate-700">
            @forelse($availableBrands as $brandOption)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brandOption->id }}" class="rounded accent-pp-600 w-4 h-4" />
                    <span>{{ $brandOption->name }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">All brands included</p>
            @endforelse
        </div>
    </div>

    <!-- REQUEST TYPE FILTER (DEVICE, SERVICE, ADVICE) -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Request Type</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="selectedType" value="" name="request_type_{{ $isMobile ? 'm' : 'd' }}" class="rounded-full accent-pp-600 w-4 h-4" />
                <span class="font-semibold text-slate-800">All Requests</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="selectedType" value="device" name="request_type_{{ $isMobile ? 'm' : 'd' }}" class="rounded-full accent-pp-600 w-4 h-4" />
                <span class="font-semibold text-slate-800">💻 Device</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="selectedType" value="service" name="request_type_{{ $isMobile ? 'm' : 'd' }}" class="rounded-full accent-pp-600 w-4 h-4" />
                <span class="font-semibold text-slate-800">🛠️ Service</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="selectedType" value="advice" name="request_type_{{ $isMobile ? 'm' : 'd' }}" class="rounded-full accent-pp-600 w-4 h-4" />
                <span class="font-semibold text-slate-800">💡 Advice</span>
            </label>
        </div>
    </div>

    <!-- LOCATION FILTER -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Location</label>
        <select wire:model.live="selectedLocation" class="w-full p-2 border border-slate-200 rounded-lg text-xs font-medium outline-none bg-slate-50 focus:bg-white focus:border-pp-600">
            <option value="">All Locations (Nigeria)</option>
            @foreach($availableLocations as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>
    </div>

    <!-- RESET BUTTON (AT THE BOTTOM) -->
    <div class="pt-4 border-t border-slate-100">
        <button type="button" wire:click="resetFilters" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-600 hover:border-pp-600 hover:text-pp-600 hover:bg-pp-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-undo-alt"></i> Reset Filters
        </button>
    </div>
</div>
