<section wire:init="loadFeaturedListings" class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-slate-200/60">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900">Featured Marketplace Listings</h2>
      <p class="text-xs text-slate-500 mt-0.5">
        @if ($stateName)
          Curated listings near {{ $stateName }}, {{ $countryName ?? 'Nigeria' }}
        @else
          Verified sellers in Lagos, Abuja, Port Harcourt &amp; Computer Village
        @endif
      </p>
    </div>
    <a href="{{ route('category') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
      <span>View all 50K+ listings</span>
      <span>→</span>
    </a>
  </div>

  @if (! $isLoaded)
    <!-- SKELETON PLACEHOLDERS (10 CARDS) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
      @for ($i = 0; $i < 10; $i++)
        <div class="rounded-2xl border border-slate-200/70 bg-white p-4 space-y-3 animate-pulse">
          <div class="h-40 bg-slate-100 rounded-xl w-full"></div>
          <div class="space-y-2 pt-1">
            <div class="h-3 bg-slate-100 rounded w-1/3"></div>
            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
            <div class="h-3 bg-slate-100 rounded w-1/2"></div>
            <div class="flex justify-between items-center pt-2">
              <div class="h-5 bg-slate-100 rounded w-1/3"></div>
              <div class="h-3 bg-slate-100 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      @endfor
    </div>
  @else
    @if (count($listings) > 0)
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
        @foreach ($listings as $listing)
          @include('livewire.components.promotions.promoted-card', [
            'listing' => $listing,
            'isPromoted' => (bool) $listing->activePromotion
          ])
        @endforeach
      </div>
    @else
      <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-8 text-center">
        <p class="text-sm font-semibold text-slate-500">No marketplace listings available for your location at this time.</p>
        <a href="{{ route('category') }}" class="mt-3 inline-block text-xs font-bold text-pp-600 hover:underline">
          Explore all marketplace categories →
        </a>
      </div>
    @endif
  @endif
</section>
