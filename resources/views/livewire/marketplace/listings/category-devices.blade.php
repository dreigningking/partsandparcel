<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        <livewire:components.category-devices-filter :isMobile="false" key="desktop-devices-filter" />
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('devices')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-pp-600"></i> Filter Complete Devices
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center">
                <span class="font-bold text-slate-900">Showing 1-12 of 142 Complete Laptops in Lagos</span>
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

        <!-- LAPTOPS GRID -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- LAPTOP 1 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 text-white">COMPLETE DEVICE</span>
                    <span class="text-6xl group-hover:scale-105 transition">💻</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Adam Computers</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">HP EliteBook 840 G5</h3>
                    <p class="text-xs text-slate-500 mt-1">Intel Core i5 · 8GB RAM · 256GB SSD · 14"</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦280,000</span>
                        <span class="text-xs font-bold text-emerald-600">3 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Ikeja, Lagos</span>
                        <span class="font-semibold text-slate-700">Used - Excellent</span>
                    </div>
                </div>
            </a>

            <!-- LAPTOP 2 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 text-white">COMPLETE DEVICE</span>
                    <span class="text-6xl group-hover:scale-105 transition">💻</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Adam Computers</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">Dell Latitude 5400</h3>
                    <p class="text-xs text-slate-500 mt-1">Intel Core i5 8th Gen · 8GB RAM · 256GB</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦250,000</span>
                        <span class="text-xs font-bold text-emerald-600">2 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Computer Village, Lagos</span>
                        <span class="font-semibold text-slate-700">Used</span>
                    </div>
                </div>
            </a>

            <!-- LAPTOP 3 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 text-white">COMPLETE DEVICE</span>
                    <span class="text-6xl group-hover:scale-105 transition">💻</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">Lagos Tech Hub</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">MacBook Pro 2019 13"</h3>
                    <p class="text-xs text-slate-500 mt-1">Core i5 · 8GB RAM · Touch Bar · Space Gray</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦660,000</span>
                        <span class="text-xs font-bold text-emerald-600">1 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Victoria Island, Lagos</span>
                        <span class="font-semibold text-slate-700">Used</span>
                    </div>
                </div>
            </a>

            <!-- LAPTOP 4 -->
            <a href="{{ route('listing-details') }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200">
                <div class="relative product-img h-48 grid place-items-center p-4">
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 text-white">COMPLETE DEVICE</span>
                    <span class="text-6xl group-hover:scale-105 transition">💻</span>
                </div>
                <div class="p-5">
                    <span class="text-xs font-bold text-pp-600">ThinkPad Depot</span>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">Lenovo ThinkPad X1 Carbon</h3>
                    <p class="text-xs text-slate-500 mt-1">Core i7 10th Gen · 16GB RAM · 512GB SSD</p>
                    <div class="mt-4 flex items-baseline justify-between">
                        <span class="text-xl font-extrabold text-slate-900">₦420,000</span>
                        <span class="text-xs font-bold text-emerald-600">4 available</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>📍 Abuja CBD</span>
                        <span class="font-semibold text-slate-700">Tokunbo</span>
                    </div>
                </div>
            </a>
        </div>
    </main>

    <!-- MOBILE FILTER DRAWER (SLIDES FROM RIGHT INSTANTLY) -->
    <div id="categoryDevicesFilterOverlay" onclick="closeCategoryFilterDrawer('devices')" class="overlay fixed inset-0 bg-slate-950/40 z-[150]"></div>
    <aside id="categoryDevicesFilterDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-80 max-w-[85vw] bg-white z-[160] p-5 overflow-y-auto custom-scrollbar shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 shrink-0">
            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-pp-600"></i> Filters
            </h3>
            <button onclick="closeCategoryFilterDrawer('devices')" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-lg font-bold transition cursor-pointer" aria-label="Close filters">×</button>
        </div>
        <div class="flex-1">
            <livewire:components.category-devices-filter :isMobile="true" key="mobile-devices-filter" />
        </div>
    </aside>
</div>
