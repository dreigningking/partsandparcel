<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        @include('livewire.marketplace.listings.partials.category-scraps-filter-content', ['isMobile' => false])
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('scraps')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-amber-600"></i> Filter Scrap &amp; Salvage
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center sm:text-left">
                <span class="font-bold text-amber-900">Showing 1-{{ $listings->count() }} of {{ $totalCount }} Scrap &amp; Salvage Units{{ $activeCategory ? ' in ' . $activeCategory->name : '' }}</span>
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

        <!-- SCRAP GRID -->
        @if($listings->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($listings as $listing)
                    <a href="{{ route('listing-details', ['id' => $listing->id]) }}" class="group rounded-2xl border border-amber-300 bg-amber-50/20 overflow-hidden hover:shadow-card transition duration-200">
                        <div class="relative product-img h-48 grid place-items-center p-4">
                            <span class="absolute top-3 left-3 text-[10px] font-extrabold px-2.5 py-0.5 rounded bg-amber-600 text-white uppercase tracking-wider">SCRAP / SALVAGE</span>
                            @if($listing->firstMediaUrl('images'))
                                <img src="{{ $listing->firstMediaUrl('images') }}" alt="{{ $listing->assetable->name ?? '' }}" class="h-full w-full object-contain group-hover:scale-105 transition" />
                            @elseif($listing->assetable && $listing->assetable->firstMediaUrl('images'))
                                <img src="{{ $listing->assetable->firstMediaUrl('images') }}" alt="{{ $listing->assetable->name ?? '' }}" class="h-full w-full object-contain group-hover:scale-105 transition" />
                            @else
                                <span class="text-6xl group-hover:scale-105 transition">🛠️</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-bold text-amber-800">{{ $listing->seller->store_name ?? ($listing->seller->name ?? 'Salvage Dealer') }}</span>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-amber-700 mt-0.5">{{ $listing->assetable->name ?? ($listing->assetable->deviceModel->name ?? 'Salvage Unit') }}</h3>
                            
                            <!-- COMPONENT STATUS MATRIX -->
                            @if($listing->assetable && $listing->assetable->components && $listing->assetable->components->count())
                                <div class="mt-3 bg-amber-100/50 p-2.5 rounded-xl text-xs space-y-1 text-amber-900 border border-amber-200/60">
                                    <div class="font-bold text-[11px] uppercase tracking-wide text-amber-800 border-b border-amber-200 pb-1 mb-1">Component Matrix:</div>
                                    @foreach($listing->assetable->components->take(4) as $comp)
                                        <div class="flex items-center justify-between">
                                            <span class="truncate pr-2">{{ $comp->name }}</span>
                                            <span class="font-extrabold {{ in_array($comp->condition_status, ['tested_working', 'tested_grade_a', 'working', 'clean']) ? 'text-emerald-700' : 'text-rose-600' }}">
                                                {{ in_array($comp->condition_status, ['tested_working', 'tested_grade_a', 'working', 'clean']) ? '✓ WORKING' : '✕ ' . strtoupper(str_replace('_', ' ', $comp->condition_status ?? 'FAULTY')) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($listing->assetable && $listing->assetable->condition_notes)
                                <div class="mt-3 bg-amber-100/50 p-2.5 rounded-xl text-xs space-y-1 text-amber-900 border border-amber-200/60">
                                    <div class="font-bold text-[11px] uppercase tracking-wide text-amber-800 border-b border-amber-200 pb-1 mb-1">Damage / Condition Notes:</div>
                                    <p class="text-xs text-amber-900 line-clamp-2 leading-relaxed">{{ $listing->assetable->condition_notes }}</p>
                                </div>
                            @endif

                            <div class="mt-4 flex items-baseline justify-between">
                                <span class="text-xl font-extrabold text-slate-900">₦{{ number_format($listing->price) }}</span>
                                <span class="text-xs font-bold text-amber-700">{{ $listing->availableQuantity() > 0 ? 'For Harvest' : 'Sold' }}</span>
                            </div>
                            <div class="mt-3 pt-3 border-t border-amber-200/60 flex items-center justify-between text-xs text-slate-500">
                                <span>📍 {{ $listing->location->city ?? ($listing->location->state ?? 'Nigeria') }}</span>
                                <span class="font-bold text-amber-800">Salvage Unit</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="rounded-2xl border border-amber-200 bg-white p-12 text-center space-y-3">
                <span class="text-5xl block">🛠️</span>
                <h3 class="text-base font-bold text-slate-900">No scrap or salvage units found</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Try clearing your filters or changing categories to find salvage units for parts harvesting.</p>
                <div class="pt-2">
                    <button type="button" wire:click="resetFilters" class="px-4 py-2 rounded-xl bg-amber-50 text-amber-900 font-bold text-xs hover:bg-amber-100 transition cursor-pointer">Reset Filters</button>
                </div>
            </div>
        @endif
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
            @include('livewire.marketplace.listings.partials.category-scraps-filter-content', ['isMobile' => true])
        </div>
    </aside>
</div>
