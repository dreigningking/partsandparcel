<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-extrabold text-amber-900">Filter Scrap &amp; Salvage</h3>
        <button type="button" wire:click="resetFilters" class="text-[11px] font-bold text-amber-900 hover:underline cursor-pointer">Reset All</button>
    </div>

    <!-- SEARCH (FIRST ELEMENT) -->
    <div>
        <label for="filterSearchScraps_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2">Search</label>
        <div class="relative">
            <input type="text" id="filterSearchScraps_{{ $isMobile ? 'mobile' : 'desktop' }}" wire:model.live.debounce.300ms="search" placeholder="Search scrap units..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-amber-900 bg-amber-50/40 focus:bg-white focus:border-amber-600 outline-none transition">
            <i class="fas fa-search absolute left-2.5 top-3 text-amber-500 text-xs"></i>
        </div>
    </div>

    <!-- BRAND -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Brand</label>
        <div class="space-y-2 text-xs text-slate-700">
            @forelse($availableBrands as $brandOption)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brandOption->id }}" class="rounded accent-amber-600 w-4 h-4" />
                    <span>{{ $brandOption->name }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">All brands included</p>
            @endforelse
        </div>
    </div>

    <!-- PRIMARY DAMAGE / FAULT -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Primary Damage / Fault</label>
        <div class="space-y-2 text-xs text-amber-900">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedFaults" value="Screen" class="rounded accent-amber-600 w-4 h-4" />
                <span>Screen Fault / Cracked LCD</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedFaults" value="Water" class="rounded accent-amber-600 w-4 h-4" />
                <span>Water / Liquid Damage</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedFaults" value="Dead Board" class="rounded accent-amber-600 w-4 h-4" />
                <span>Dead Board / No Power</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedFaults" value="Broken" class="rounded accent-amber-600 w-4 h-4" />
                <span>Hinge / Case Broken</span>
            </label>
        </div>
    </div>

    <!-- HARVESTABLE WORKING PARTS -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Harvestable Working Parts</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedHarvestable" value="Motherboard" class="rounded accent-amber-600 w-4 h-4" />
                <span class="font-semibold text-emerald-700">✓ Motherboard Working</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedHarvestable" value="RAM" class="rounded accent-amber-600 w-4 h-4" />
                <span class="font-semibold text-emerald-700">✓ RAM &amp; Storage Intact</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedHarvestable" value="Case" class="rounded accent-amber-600 w-4 h-4" />
                <span class="font-semibold text-emerald-700">✓ Original Casing Good</span>
            </label>
        </div>
    </div>

    <!-- COMPONENT CONDITION STATUS -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Component Status</label>
        <div class="space-y-2 text-xs text-amber-900">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedComponentConditions" value="Testing working" class="rounded accent-amber-600 w-4 h-4" />
                <span>Testing working</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedComponentConditions" value="Untested" class="rounded accent-amber-600 w-4 h-4" />
                <span>Untested</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="selectedComponentConditions" value="Repaired" class="rounded accent-amber-600 w-4 h-4" />
                <span>Repaired</span>
            </label>
        </div>
    </div>

    <!-- SALVAGE BUDGET (PRICE RANGE) -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Salvage Budget (₦)</label>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <input type="number" wire:model.live.debounce.400ms="minPrice" placeholder="Min ₦" class="p-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-amber-600" />
            <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="Max ₦" class="p-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-amber-600" />
        </div>
    </div>

    <!-- LOCATION -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-2.5">Location</label>
        <select wire:model.live="selectedLocation" class="w-full p-2 border border-slate-200 rounded-lg text-xs font-medium outline-none bg-slate-50 focus:bg-white focus:border-amber-600">
            <option value="">All Locations (Nigeria)</option>
            @foreach($availableLocations as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>
    </div>

    <!-- RESET BUTTON (AT THE BOTTOM) -->
    <div class="pt-4 border-t border-slate-100">
        <button type="button" wire:click="resetFilters" class="w-full py-2.5 rounded-xl border border-amber-200 text-xs font-extrabold text-amber-800 hover:border-amber-600 hover:text-amber-900 hover:bg-amber-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-undo-alt"></i> Reset Filters
        </button>
    </div>
</div>
