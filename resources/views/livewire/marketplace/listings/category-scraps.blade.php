<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        <livewire:components.category-scraps-filter :isMobile="false" key="desktop-scraps-filter" />
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('scraps')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-amber-600"></i> Filter Scrap &amp; Salvage
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center">
                <span class="font-bold text-amber-900">Showing 1-12 of 95 Scrap &amp; Salvage Units</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <span class="text-slate-400 font-medium">Sort by:</span>
                <select class="border border-slate-200 rounded-lg p-1.5 font-semibold text-slate-800 outline-none bg-slate-50">
                    <option>Relevance</option>
                    <option>Newest First</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- SCRAP GRID -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- SCRAP UNIT 1 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-amber-300 bg-amber-50/20 overflow-hidden hover:shadow-card transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-extrabold px-2.5 py-0.5 rounded bg-amber-600 text-white uppercase tracking-wider">SCRAP / SALVAGE</span>
                    <span class="text-6xl group-hover:scale-105 transition">🛠️</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-amber-800">Abel Tech Parts</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-amber-700 mt-0.5">Dell Latitude 5420 (Screen Fault)</h3>
                    
                    <!-- COMPONENT STATUS MATRIX -->
                    <div class="mt-3 bg-amber-100/50 p-2.5 rounded-xl text-xs space-y-1 text-amber-900 border border-amber-200/60">
                        <div class="font-bold text-[11px] uppercase tracking-wide text-amber-800 border-b border-amber-200 pb-1 mb-1">Component Matrix:</div>
                        <div class="flex items-center justify-between"><span>Motherboard</span><span class="font-extrabold text-emerald-700">✓ WORKING</span></div>
                        <div class="flex items-center justify-between"><span>RAM (16GB)</span><span class="font-extrabold text-emerald-700">✓ WORKING</span></div>
                        <div class="flex items-center justify-between"><span>Screen</span><span class="font-extrabold text-rose-600">✕ DAMAGED</span></div>
                    </div>

                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦150,000</span>
                        <span class="text-xs font-bold text-amber-700">For Harvest</span>
                    </div>
                </div>
            </a>

            <!-- SCRAP UNIT 2 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-amber-300 bg-amber-50/20 overflow-hidden hover:shadow-card transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-extrabold px-2.5 py-0.5 rounded bg-amber-600 text-white uppercase tracking-wider">SCRAP / SALVAGE</span>
                    <span class="text-6xl group-hover:scale-105 transition">💻</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-amber-800">Lagos Salvage Yard</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-amber-700 mt-0.5">Water Damaged MacBook Air M1</h3>
                    
                    <div class="mt-3 bg-amber-100/50 p-2.5 rounded-xl text-xs space-y-1 text-amber-900 border border-amber-200/60">
                        <div class="font-bold text-[11px] uppercase tracking-wide text-amber-800 border-b border-amber-200 pb-1 mb-1">Component Matrix:</div>
                        <div class="flex items-center justify-between"><span>Retina Display</span><span class="font-extrabold text-emerald-700">✓ PERFECT</span></div>
                        <div class="flex items-center justify-between"><span>Aluminum Case</span><span class="font-extrabold text-emerald-700">✓ CLEAN</span></div>
                        <div class="flex items-center justify-between"><span>Logic Board</span><span class="font-extrabold text-rose-600">✕ WATER DAMAGED</span></div>
                    </div>

                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦180,000</span>
                        <span class="text-xs font-bold text-amber-700">For Harvest</span>
                    </div>
                </div>
            </a>
        </div>
    </main>

    <!-- MOBILE FILTER DRAWER (SLIDES FROM RIGHT INSTANTLY) -->
    <div id="categoryScrapsFilterOverlay" onclick="closeCategoryFilterDrawer('scraps')" class="overlay fixed inset-0 bg-slate-950/40 z-[150]"></div>
    <aside id="categoryScrapsFilterDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-80 max-w-[85vw] bg-white z-[160] p-5 overflow-y-auto custom-scrollbar shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 shrink-0">
            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-amber-600"></i> Filters
            </h3>
            <button onclick="closeCategoryFilterDrawer('scraps')" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-lg font-bold transition cursor-pointer" aria-label="Close filters">×</button>
        </div>
        <div class="flex-1">
            <livewire:components.category-scraps-filter :isMobile="true" key="mobile-scraps-filter" />
        </div>
    </aside>
</div>
