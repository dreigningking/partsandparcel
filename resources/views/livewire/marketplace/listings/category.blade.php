<div>
    @push('styles')
    <style>
      /* Connected Tab Active Styles */
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

    <!-- CATEGORY BREADCRUMB & CONTEXT HEADER -->
    <div class="bg-white border-b border-slate-200 pt-6 pb-0">
      <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
          <a href="{{ route('welcome') }}" class="hover:text-slate-700">Home</a>
          <span>/</span>
          <a href="{{ route('listing-details') }}" class="hover:text-slate-700">Electronics</a>
          <span>/</span>
          <span class="text-slate-900 font-bold">Laptops &amp; Computer Parts</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950" id="category-heading">
              @if($tab === 'parts')
                Parts &amp; Components Marketplace
              @elseif($tab === 'scrap')
                Scrap &amp; Salvage Marketplace
              @elseif($tab === 'requests' || $tab === 'community')
                Community Requests &amp; Sourcing
              @else
                Laptops &amp; Complete Devices Marketplace
              @endif
            </h1>
            <p class="text-xs text-slate-500 mt-1">Browse working laptops, replacement components, scrap units for salvage, or community requests.</p>
          </div>

          <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 font-medium">Verified Sellers:</span>
            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">1,240 Sellers Active</span>
          </div>
        </div>

        <!-- CONTEXTUAL 4 INTENT TABS (CONNECTED TAB NAV BAR WITH URL AWARENESS) -->
        <div class="mt-6 border-b border-slate-200">
          <nav class="-mb-px flex space-x-2 sm:space-x-4 overflow-x-auto no-scrollbar" aria-label="Tabs">
            
            <!-- TAB 1: COMPLETE DEVICES -->
            <button wire:click="switchTab('complete')" class="tab-item {{ $tab === 'complete' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'complete' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">💻</span>
              <span>Complete Laptops</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $tab === 'complete' ? 'bg-pp-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">142</span>
            </button>

            <!-- TAB 2: SPARE PARTS -->
            <button wire:click="switchTab('parts')" class="tab-item {{ $tab === 'parts' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'parts' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">⚙️</span>
              <span>Parts &amp; Components</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab === 'parts' ? 'bg-pp-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">380</span>
            </button>

            <!-- TAB 3: SCRAP & SALVAGE -->
            <button wire:click="switchTab('scrap')" class="tab-item {{ $tab === 'scrap' ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ $tab === 'scrap' ? 'border-amber-600 text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-500 hover:text-amber-800 hover:border-amber-400' }}">
              <span class="text-base sm:text-lg">🛠️</span>
              <span>Scrap &amp; Salvage</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab === 'scrap' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-100 text-amber-900' }}">95</span>
            </button>

            <!-- TAB 4: COMMUNITY REQUESTS -->
            <button wire:click="switchTab('requests')" class="tab-item {{ ($tab === 'requests' || $tab === 'community') ? 'active' : '' }} group relative inline-flex items-center gap-2 py-3.5 px-4 sm:px-6 font-bold text-xs sm:text-sm border-b-2 rounded-t-xl transition cursor-pointer {{ ($tab === 'requests' || $tab === 'community') ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">
              <span class="text-base sm:text-lg">💬</span>
              <span>Community Requests</span>
              <span class="tab-badge ml-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold {{ ($tab === 'requests' || $tab === 'community') ? 'bg-pp-600 text-white shadow-xs' : 'bg-purple-100 text-purple-800' }}">48</span>
            </button>

          </nav>
        </div>

      </div>
    </div>

    <!-- MAIN CONTENT AREA WITH SIDEBAR FILTERS & TAB COMPONENTS -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
      @if($tab === 'parts')
        <livewire:marketplace.listings.category-parts key="tab-parts" />
      @elseif($tab === 'scrap')
        <livewire:marketplace.listings.category-scraps key="tab-scraps" />
      @elseif($tab === 'requests' || $tab === 'community')
        <livewire:marketplace.listings.category-requests key="tab-requests" />
      @else
        <livewire:marketplace.listings.category-devices key="tab-devices" />
      @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleCategoryFilterDrawer(type) {
        const idMap = {
            'devices': ['categoryDevicesFilterDrawer', 'categoryDevicesFilterOverlay'],
            'parts': ['categoryPartsFilterDrawer', 'categoryPartsFilterOverlay'],
            'scraps': ['categoryScrapsFilterDrawer', 'categoryScrapsFilterOverlay'],
            'scrap': ['categoryScrapsFilterDrawer', 'categoryScrapsFilterOverlay'],
            'requests': ['categoryRequestsFilterDrawer', 'categoryRequestsFilterOverlay'],
            'community': ['categoryRequestsFilterDrawer', 'categoryRequestsFilterOverlay']
        };
        const ids = idMap[type];
        if (ids) {
            const drawer = document.getElementById(ids[0]);
            const overlay = document.getElementById(ids[1]);
            if (drawer) drawer.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        }
    }

    function closeCategoryFilterDrawer(type) {
        const idMap = {
            'devices': ['categoryDevicesFilterDrawer', 'categoryDevicesFilterOverlay'],
            'parts': ['categoryPartsFilterDrawer', 'categoryPartsFilterOverlay'],
            'scraps': ['categoryScrapsFilterDrawer', 'categoryScrapsFilterOverlay'],
            'scrap': ['categoryScrapsFilterDrawer', 'categoryScrapsFilterOverlay'],
            'requests': ['categoryRequestsFilterDrawer', 'categoryRequestsFilterOverlay'],
            'community': ['categoryRequestsFilterDrawer', 'categoryRequestsFilterOverlay']
        };
        const ids = idMap[type];
        if (ids) {
            const drawer = document.getElementById(ids[0]);
            const overlay = document.getElementById(ids[1]);
            if (drawer) drawer.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
    }
</script>
@endpush
