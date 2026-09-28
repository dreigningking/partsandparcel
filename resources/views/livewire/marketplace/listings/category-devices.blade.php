<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        @include('livewire.marketplace.listings.partials.category-devices-filter-content', ['isMobile' => false])
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('devices')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-pp-600"></i> Filter Complete Devices
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center sm:text-left">
                <span class="font-bold text-slate-900">Showing 1-{{ $listings->count() }} of {{ $totalCount }} Complete Devices{{ $activeCategory ? ' in ' . $activeCategory->name : '' }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <span class="text-slate-400 font-medium">Sort by:</span>
                <select wire:model.live="sortBy" class="border border-slate-200 rounded-lg p-1.5 font-semibold text-slate-800 outline-none bg-slate-50 cursor-pointer">
                    <option value="relevance">Relevance</option>
                    <option value="newest">Newest First</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- LAPTOPS / DEVICES GRID -->
        @if($listings->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($listings as $listing)
                    <a href="{{ route('listing-details', ['id' => $listing->id]) }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200">
                        <div class="relative product-img h-48 grid place-items-center p-4">
                            <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 text-white">COMPLETE DEVICE</span>
                            @if($listing->firstMediaUrl('images'))
                                <img src="{{ $listing->firstMediaUrl('images') }}" alt="{{ $listing->assetable->name ?? '' }}" class="h-full w-full object-contain group-hover:scale-105 transition" />
                            @elseif($listing->assetable && $listing->assetable->firstMediaUrl('images'))
                                <img src="{{ $listing->assetable->firstMediaUrl('images') }}" alt="{{ $listing->assetable->name ?? '' }}" class="h-full w-full object-contain group-hover:scale-105 transition" />
                            @else
                                <span class="text-6xl group-hover:scale-105 transition">💻</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-bold text-pp-600">{{ $listing->seller->store_name ?? ($listing->seller->name ?? 'Verified Seller') }}</span>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5">{{ $listing->assetable->name ?? ($listing->assetable->deviceModel->name ?? 'Complete Device') }}</h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $listing->assetable->description ?? ($listing->description ?? 'Fully functional complete device ready for use.') }}</p>
                            <div class="mt-4 flex items-baseline justify-between">
                                <span class="text-xl font-extrabold text-slate-900">₦{{ number_format($listing->price) }}</span>
                                <span class="text-xs font-bold text-emerald-600">{{ $listing->availableQuantity() }} available</span>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>📍 {{ $listing->location->city ?? ($listing->location->state ?? 'Nigeria') }}</span>
                                <span class="font-semibold text-slate-700 capitalize">{{ str_replace('_', ' ', $listing->assetable->condition_status ?? 'Used') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center space-y-3">
                <span class="text-5xl block">💻</span>
                <h3 class="text-base font-bold text-slate-900">No complete devices found matching your criteria</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Try clearing your search query, adjusting your price range, or selecting a broader category.</p>
                <div class="pt-2">
                    <button type="button" wire:click="resetFilters" class="px-4 py-2 rounded-xl bg-pp-50 text-pp-700 font-bold text-xs hover:bg-pp-100 transition cursor-pointer">Reset Filters</button>
                </div>
            </div>
        @endif
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
            @include('livewire.marketplace.listings.partials.category-devices-filter-content', ['isMobile' => true])
        </div>
    </aside>
</div>
