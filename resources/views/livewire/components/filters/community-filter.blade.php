<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-sliders-h text-pp-600"></i> Filter Requests
        </h3>
        <button type="button" class="text-[11px] font-bold text-pp-600 hover:underline cursor-pointer">Reset All</button>
    </div>

    <!-- SEARCH (FIRST ELEMENT) -->
    <div>
        <label for="filterSearch_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Search</label>
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" id="filterSearch_{{ $isMobile ? 'mobile' : 'desktop' }}" placeholder="Search requests..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <i class="fas fa-search absolute left-2.5 top-3 text-slate-400 text-xs"></i>
        </div>
    </div>

    <!-- Category -->
    <div class="pt-4 border-t border-slate-100">
        <label for="filterCategory_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Category</label>
        <select wire:model.live="category" id="filterCategory_{{ $isMobile ? 'mobile' : 'desktop' }}" class="w-full p-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <option value="">All Categories</option>
            <option value="Electronics">Electronics</option>
            <option value="Vehicles">Vehicles</option>
            <option value="Appliances">Appliances</option>
            <option value="Equipment">Equipment</option>
            <option value="Construction">Construction</option>
            <option value="Industrial">Industrial</option>
            <option value="Agricultural">Agricultural</option>
        </select>
    </div>

    <!-- Request Type -->
    <div class="pt-4 border-t border-slate-100">
        <label for="filterType_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Request Type</label>
        <select wire:model.live="type" id="filterType_{{ $isMobile ? 'mobile' : 'desktop' }}" class="w-full p-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <option value="">All Types</option>
            <option value="Product / Part">Product / Part</option>
            <option value="Repair / Service">Repair / Service</option>
            <option value="Question / Advice">Question / Advice</option>
        </select>
    </div>

    <!-- Location -->
    <div class="pt-4 border-t border-slate-100">
        <label for="filterLocation_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Location</label>
        <select wire:model.live="location" id="filterLocation_{{ $isMobile ? 'mobile' : 'desktop' }}" class="w-full p-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <option value="">All Nigeria</option>
            <option value="Lagos">Lagos</option>
            <option value="Abuja">Abuja</option>
            <option value="Port Harcourt">Port Harcourt</option>
            <option value="Kano">Kano</option>
        </select>
    </div>

    <!-- Budget Range -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Budget Range (₦)</label>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <input type="number" wire:model.live.debounce.300ms="budgetMin" placeholder="Min ₦" min="0" class="p-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <input type="number" wire:model.live.debounce.300ms="budgetMax" placeholder="Max ₦" min="0" class="p-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
        </div>
    </div>

    <!-- Status -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Status</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="status" value="open" class="rounded accent-pp-600 w-4 h-4"><span>Open</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="status" value="offers" class="rounded accent-pp-600 w-4 h-4"><span>Offers Received</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="status" value="fulfilled" class="rounded accent-pp-600 w-4 h-4"><span>Recently Fulfilled</span>
            </label>
        </div>
    </div>

    <!-- RESET BUTTON (AT THE BOTTOM) -->
    <div class="pt-4 border-t border-slate-100">
        <button wire:click="resetFilters" type="button" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-600 hover:border-pp-600 hover:text-pp-600 hover:bg-pp-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-undo-alt"></i> Reset Filters
        </button>
    </div>
</div>
