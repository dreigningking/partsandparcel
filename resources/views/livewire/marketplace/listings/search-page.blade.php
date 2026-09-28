<div>
    @push('styles')
      <style>
        .tab-item.active {
          border-bottom-color: #4634b7;
          color: #4634b7;
          background-color: rgba(245, 243, 255, 0.7);
        }
        .tab-item.active .tab-badge {
          background-color: #4634b7;
          color: #ffffff;
        }
      </style>
    @endpush

    <!-- SEARCH BREADCRUMB & CONTEXT HEADER -->
    <div class="bg-white border-b border-slate-200 pt-6 pb-0">
      <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
          <a href="{{ route('welcome') }}" class="hover:text-slate-700">Home</a>
          <span>/</span>
          <a href="{{ route('category') }}" class="hover:text-slate-700">Marketplace</a>
          <span>/</span>
          <span class="text-slate-900 font-bold">Search Results</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950" id="search-heading">
              @if($searchTerm !== '')
                Search Results for "<span class="text-pp-600">{{ $searchTerm }}</span>"
              @else
                Search Marketplace
              @endif
            </h1>
            <p class="text-xs text-slate-500 mt-1">Showing matching items, device models, spare parts, scrap units, and community discussions.</p>
          </div>

          <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 font-medium">Total Found:</span>
            <span class="px-2.5 py-1 rounded-full bg-pp-50 text-pp-700 font-bold border border-pp-200">{{ $totalItemsCount + $totalDiscussionsCount }} Results</span>
          </div>
        </div>

        <!-- SEARCH INPUT IN PAGE FOR RE-SEARCHING -->
        <div class="mt-4 max-w-xl">
          <form wire:submit.prevent="$refresh" class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-10 shadow-2xs focus-within:border-pp-500 focus-within:ring-2 focus-within:ring-pp-500/20 bg-white">
            <input type="text" wire:model.live.debounce.300ms="q" class="flex-1 h-full px-4 text-xs font-medium text-slate-800 placeholder:text-slate-400 outline-none" placeholder="Filter or re-enter search term..." />
            <button type="submit" class="h-full px-4 bg-pp-600 hover:bg-pp-700 text-white font-bold text-xs flex items-center gap-1.5 transition">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
              <span>Search</span>
            </button>
          </form>
        </div>

        <!-- 4 INTENT TABS (CONNECTED TAB NAV BAR WITH URL AWARENESS) -->
        <div class="mt-6 border-b border-slate-200">
          <nav class="-mb-px flex space-x-2 sm:space-x-4 overflow-x-auto no-scrollbar" aria-label="Tabs">
            
            <!-- TAB 1: ALL RESULTS -->
            <button wire:click="switchTab('all')" class="tab-item {{ $tab === 'all' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'all' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">🔍</span>
              <span>All Results</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $tab === 'all' ? 'bg-pp-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">{{ $totalItemsCount + $totalDiscussionsCount }}</span>
            </button>

            <!-- TAB 2: COMPLETE DEVICES & ITEMS -->
            <button wire:click="switchTab('devices')" class="tab-item {{ $tab === 'devices' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'devices' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">💻</span>
              <span>Devices &amp; Items</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab === 'devices' ? 'bg-pp-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">{{ $totalItemsCount }}</span>
            </button>

            <!-- TAB 3: SCRAP & SALVAGE -->
            <button wire:click="switchTab('scrap')" class="tab-item {{ $tab === 'scrap' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'scrap' ? 'border-amber-600 text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-500 hover:text-amber-800 hover:border-amber-400' }}">
              <span class="text-base sm:text-lg">🛠️</span>
              <span>Scrap &amp; Salvage</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab === 'scrap' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-100 text-amber-900' }}">{{ $items->where('condition_status', 'scrap')->count() }}</span>
            </button>

            <!-- TAB 4: COMMUNITY DISCUSSIONS -->
            <button wire:click="switchTab('community')" class="tab-item {{ $tab === 'community' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'community' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">💬</span>
              <span>Community Discussions</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab === 'community' ? 'bg-pp-600 text-white shadow-xs' : 'bg-purple-100 text-purple-800' }}">{{ $totalDiscussionsCount }}</span>
            </button>

          </nav>
        </div>

      </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      
      @if($totalItemsCount === 0 && $totalDiscussionsCount === 0)
        <!-- EMPTY STATE WHEN NO RESULTS MATCH -->
        <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center max-w-2xl mx-auto shadow-sm space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center mx-auto text-3xl">🔍</div>
          <h2 class="text-xl font-extrabold text-slate-900">No results found for "{{ $searchTerm }}"</h2>
          <p class="text-xs text-slate-500 leading-relaxed max-w-md mx-auto">
            We couldn't find any devices, items, scrap parts, or community discussions matching your query.
          </p>
          <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('category') }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">Browse Marketplace Categories</a>
            <a href="{{ route('community') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-extrabold text-xs transition">Post a Request in Community</a>
          </div>
        </div>
      @else

        <!-- ITEMS / DEVICES SECTION -->
        @if(($tab === 'all' || $tab === 'devices' || $tab === 'scrap') && count($items) > 0)
          <div>
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <span>💻</span> Marketplace Items &amp; Devices ({{ count($items) }})
              </h2>
              <a href="{{ route('category') }}" class="text-xs font-bold text-pp-600 hover:underline">View All Categories →</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
              @foreach($items as $item)
                @php
                  $price = $item->listing?->price ?? 0;
                  $deviceModelName = $item->deviceModel?->name ?? 'Device';
                  $brandName = $item->deviceModel?->brand?->name ?? '';
                  $categoryName = $item->deviceModel?->category?->name ?? 'Electronics';
                  $locationName = $item->location?->city ?? 'Nigeria';
                @endphp
                <a href="{{ $item->listing ? route('listing-details', $item->listing) : '#' }}" class="group rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                  <div>
                    <div class="relative product-img h-44 grid place-items-center p-4 bg-slate-50">
                      <span class="absolute top-3 left-3 text-[10px] font-extrabold px-2 py-0.5 rounded {{ $item->condition_status === 'scrap' ? 'bg-amber-600 text-white' : 'bg-slate-900 text-white' }}">
                        {{ strtoupper($item->condition_status ?? 'AVAILABLE') }}
                      </span>
                      <span class="text-5xl group-hover:scale-105 transition">💻</span>
                    </div>
                    <div class="p-4">
                      @if($brandName)
                        <span class="text-[11px] font-bold text-pp-600">{{ $brandName }}</span>
                      @endif
                      <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-pp-600 mt-0.5 truncate">{{ $item->name ?? $deviceModelName }}</h3>
                      <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $item->description ?? "Category: {$categoryName}" }}</p>
                    </div>
                  </div>

                  <div class="p-4 pt-0">
                    <div class="mt-2 flex items-baseline justify-between">
                      <span class="text-lg font-extrabold text-slate-900">
                        @if($price > 0) ₦{{ number_format($price) }} @else Contact Seller @endif
                      </span>
                      <span class="text-[10px] font-bold text-emerald-600">Verified</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                      <span>📍 {{ $locationName }}</span>
                      <span class="font-semibold text-slate-700 capitalize">{{ $item->condition_status }}</span>
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        <!-- COMMUNITY DISCUSSIONS SECTION -->
        @if(($tab === 'all' || $tab === 'community') && count($discussions) > 0)
          <div>
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <span>💬</span> Community Discussions &amp; Sourcing Requests ({{ count($discussions) }})
              </h2>
              <a href="{{ route('community') }}" class="text-xs font-bold text-pp-600 hover:underline">Explore Community →</a>
            </div>

            <div class="space-y-4">
              @foreach($discussions as $disc)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:border-pp-300 transition duration-200 shadow-xs space-y-2">
                  <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>Posted by {{ $disc->user?->name ?? 'Community Member' }}</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-extrabold border border-purple-200">
                      {{ $disc->responses->count() }} Responses
                    </span>
                  </div>
                  <h3 class="text-base font-extrabold text-slate-900 hover:text-pp-600">
                    <a href="{{ route('community.request', ['id' => $disc->id]) }}">{{ $disc->title }}</a>
                  </h3>
                  <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $disc->body }}</p>
                  <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Topic: {{ $disc->category?->name ?? 'General Discussion' }}</span>
                    <a href="{{ route('community.request', ['id' => $disc->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition">View Discussion</a>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      @endif
    </div>
</div>
