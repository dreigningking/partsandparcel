<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-extrabold text-slate-900">Filter Spare Parts</h3>
        <button type="button" class="text-[11px] font-bold text-pp-600 hover:underline cursor-pointer">Reset All</button>
    </div>

    <!-- SEARCH (FIRST ELEMENT) -->
    <div>
        <label for="filterSearchParts_{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">Search</label>
        <div class="relative">
            <input type="text" id="filterSearchParts_{{ $isMobile ? 'mobile' : 'desktop' }}" placeholder="Search spare parts..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:border-pp-600 outline-none transition">
            <i class="fas fa-search absolute left-2.5 top-3 text-slate-400 text-xs"></i>
        </div>
    </div>

    <!-- COMPONENT TYPE -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Component / Part Type</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Laptop Batteries</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Display LCD Screens</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Motherboards &amp; Logic Boards</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Keyboards &amp; Top Cases</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>RAM &amp; NVMe SSD Storage</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Original Chargers</span></label>
        </div>
    </div>

    <!-- COMPATIBLE BRAND -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Compatible Brand</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>HP EliteBook / ProBook</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Dell Latitude / Inspiron</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="rounded accent-pp-600 w-4 h-4" /><span>Apple MacBook</span></label>
        </div>
    </div>

    <!-- TESTING GRADE -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Testing Grade</label>
        <div class="space-y-2 text-xs text-slate-700">
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>100% Tested Working</span></label>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="rounded accent-pp-600 w-4 h-4" /><span>Brand New Genuine</span></label>
        </div>
    </div>

    <!-- PRICE RANGE -->
    <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5">Price Range (₦)</label>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <input placeholder="Min ₦" class="p-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-pp-600" value="5,000" />
            <input placeholder="Max ₦" class="p-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-pp-600" value="200,000" />
        </div>
    </div>

    <!-- RESET BUTTON (AT THE BOTTOM) -->
    <div class="pt-4 border-t border-slate-100">
        <button type="button" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-600 hover:border-pp-600 hover:text-pp-600 hover:bg-pp-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-undo-alt"></i> Reset Filters
        </button>
    </div>
</div>
