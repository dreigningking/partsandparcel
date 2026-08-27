<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-extrabold text-slate-900">Filter Category Requests</h3>
        <button type="button" class="text-[11px] font-bold text-pp-600 hover:underline cursor-pointer">Reset All</button>
    </div>

    <!-- SEARCH (FIRST ELEMENT) -->
    <div>
        <label for="filterSearchCategoryRequests_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Search</label>
        <div class="relative">
            <input type="text" id="filterSearchCategoryRequests_{{ $isMobile ? 'mobile' : 'desktop' }}" placeholder="Search category requests..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <i class="fas fa-search absolute left-2.5 top-3 text-slate-400 text-xs"></i>
        </div>
    </div>

    <!-- BRAND FILTER -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Filter Brands</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>HP Laptops</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Dell Laptops</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Apple MacBook</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Lenovo ThinkPad</span></label>
        </div>
    </div>

    <!-- SERVICE / HELP TYPE FILTER -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Service &amp; Help Needed</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span class="font-semibold text-slate-800">🛠️ Hardware Repairs</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span class="font-semibold text-slate-800">💻 Software &amp; OS</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span class="font-semibold text-slate-800">🔍 Spare Part Sourcing</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span class="font-semibold text-slate-800">🚚 Logistics Help</span></label>
        </div>
    </div>

    <!-- LOCATION FILTER -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Buyer Location</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Computer Village, Ikeja</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Wuse Zone 4 (Abuja)</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Bompai Market (Kano)</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Garrison (PH)</span></label>
        </div>
    </div>

    <!-- RESET BUTTON (AT THE BOTTOM) -->
    <div class="pt-4 border-t border-slate-100">
        <button type="button" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-600 hover:border-pp-600 hover:text-pp-600 hover:bg-pp-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-undo-alt"></i> Reset Filters
        </button>
    </div>
</div>
