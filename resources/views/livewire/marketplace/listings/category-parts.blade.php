<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        <livewire:components.category-parts-filter :isMobile="false" key="desktop-parts-filter" />
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('parts')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-pp-600"></i> Filter Spare Parts
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center">
                <span class="font-bold text-slate-900">Showing 1-12 of 380 Replacement Spare Parts</span>
            </div>
            
            <div class="flex justify-center items-center gap-2">
                <span class="text-slate-400 font-medium">Sort by:</span>
                <select class="border border-slate-200 rounded-lg p-1.5 font-semibold text-slate-800 outline-none bg-slate-50">
                    <option>Relevance</option>
                    <option>Newest First</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- PARTS GRID -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- PART 1 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-600 text-white">SPARE PART</span>
                    <span class="text-6xl group-hover:scale-105 transition">🔋</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Seth Electronics</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">HP EliteBook 840 G5 Battery</h3>
                    <p class="text-xs text-slate-500 mt-1">Part #TT03XL · Fits 840 G5 &amp; G6</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦25,000</span>
                        <span class="text-xs font-bold text-emerald-600">7 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Ikeja, Lagos</span>
                        <span class="font-bold text-emerald-600">Tested Working</span>
                    </div>
                </div>
            </a>

            <!-- PART 2 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-600 text-white">SPARE PART</span>
                    <span class="text-6xl group-hover:scale-105 transition">🖥️</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Computer Village Screens</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">HP 14" FHD IPS Display LCD</h3>
                    <p class="text-xs text-slate-500 mt-1">30-Pin EDP Connector · Matte Anti-glare</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦45,000</span>
                        <span class="text-xs font-bold text-emerald-600">12 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Ikeja, Lagos</span>
                        <span class="font-bold text-slate-700">Tested Grade A</span>
                    </div>
                </div>
            </a>

            <!-- PART 3 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-600 text-white">SPARE PART</span>
                    <span class="text-6xl group-hover:scale-105 transition">⚙️</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Abel Tech Parts</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">Dell Latitude 5420 Motherboard</h3>
                    <p class="text-xs text-slate-500 mt-1">Intel i5 11th Gen · Onboard Graphics · Clean</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦110,000</span>
                        <span class="text-xs font-bold text-emerald-600">2 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Ikeja, Lagos</span>
                        <span class="font-bold text-emerald-600">100% Tested</span>
                    </div>
                </div>
            </a>
        </div>
    </main>

    <!-- MOBILE FILTER DRAWER (SLIDES FROM RIGHT INSTANTLY) -->
    <div id="categoryPartsFilterOverlay" onclick="closeCategoryFilterDrawer('parts')" class="overlay fixed inset-0 bg-slate-950/40 z-[150]"></div>
    <aside id="categoryPartsFilterDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-80 max-w-[85vw] bg-white z-[160] p-5 overflow-y-auto custom-scrollbar shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 shrink-0">
            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-pp-600"></i> Filters
            </h3>
            <button onclick="closeCategoryFilterDrawer('parts')" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-lg font-bold transition cursor-pointer" aria-label="Close filters">×</button>
        </div>
        <div class="flex-1">
            <livewire:components.category-parts-filter :isMobile="true" key="mobile-parts-filter" />
        </div>
    </aside>
</div>
