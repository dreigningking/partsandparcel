<div class="grid lg:grid-cols-12 gap-8 items-start">
    <!-- LEFT SIDEBAR FILTER (DESKTOP) -->
    <aside class="hidden lg:block lg:col-span-3 sticky top-24">
        @include('livewire.marketplace.listings.partials.category-requests-filter-content', ['isMobile' => false])
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="lg:col-span-9 space-y-6">
        <!-- MOBILE FILTER TRIGGER BUTTON -->
        <button onclick="toggleCategoryFilterDrawer('requests')" class="lg:hidden w-full py-3 px-4 mb-2 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
            <i class="fas fa-sliders-h text-pp-600"></i> Filter Category Requests
        </button>

        <!-- RESULTS BAR & SORT -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white px-5 py-3.5 rounded-2xl border border-slate-200 text-xs shadow-xs gap-3">
            <div class="text-center sm:text-left">
                <span class="font-bold text-slate-900">Showing 1-{{ $discussions->count() }} of {{ $totalCount }} Open Community Requests{{ $activeCategory ? ' in ' . $activeCategory->name : '' }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <a href="{{ route('community') }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs flex items-center gap-1.5 transition">
                    <span>+ Post New Request</span>
                </a>

                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-medium">Sort by:</span>
                    <select wire:model.live="sortBy" class="border border-slate-200 rounded-lg p-1.5 font-semibold text-slate-800 outline-none bg-slate-50 cursor-pointer">
                        <option value="relevance">Relevance</option>
                        <option value="newest">Newest First</option>
                        <option value="offers">Most Offers Received</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- COMMUNITY REQUESTS LIST -->
        @if($discussions->isNotEmpty())
            <div class="space-y-4">
                @foreach($discussions as $discussion)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 hover:border-pp-300 transition duration-200 shadow-xs space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Posted by {{ $discussion->user->name ?? 'Community Member' }} · {{ $discussion->location->city ?? ($discussion->location->state ?? 'Nigeria') }}</span>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold border border-emerald-200">
                                {{ $discussion->responses_count }} {{ Str::plural('Offer', $discussion->responses_count) }} Received
                            </span>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 hover:text-pp-600">
                            <a href="{{ route('community.request', ['id' => $discussion->id]) }}">{{ $discussion->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $discussion->body }}</p>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 bg-slate-100 px-3 py-1 rounded-full">Budget: {{ $discussion->budget ? '₦' . $discussion->budget : 'Open Budget' }}</span>
                            <a href="{{ route('community.request', ['id' => $discussion->id]) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition">View &amp; Submit Offer</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center space-y-3">
                <span class="text-5xl block">💬</span>
                <h3 class="text-base font-bold text-slate-900">No community requests found</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Can't find what you are looking for? Post a request and verified sellers will submit offers to you directly.</p>
                <div class="pt-2 flex justify-center gap-3">
                    <button type="button" wire:click="resetFilters" class="px-4 py-2 rounded-xl bg-pp-50 text-pp-700 font-bold text-xs hover:bg-pp-100 transition cursor-pointer">Reset Filters</button>
                    <a href="{{ route('community') }}" class="px-4 py-2 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition">+ Post Request</a>
                </div>
            </div>
        @endif
    </main>

    <!-- MOBILE FILTER DRAWER (SLIDES FROM RIGHT INSTANTLY) -->
    <div id="categoryRequestsFilterOverlay" onclick="closeCategoryFilterDrawer('requests')" class="overlay fixed inset-0 bg-slate-950/40 z-[150]"></div>
    <aside id="categoryRequestsFilterDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-80 max-w-[85vw] bg-white z-[160] p-5 overflow-y-auto custom-scrollbar shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 shrink-0">
            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-pp-600"></i> Filters
            </h3>
            <button onclick="closeCategoryFilterDrawer('requests')" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-lg font-bold transition cursor-pointer" aria-label="Close filters">×</button>
        </div>
        <div class="flex-1">
            @include('livewire.marketplace.listings.partials.category-requests-filter-content', ['isMobile' => true])
        </div>
    </aside>
</div>
