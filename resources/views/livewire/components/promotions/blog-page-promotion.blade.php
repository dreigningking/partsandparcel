<div wire:init="loadFeaturedListing" class="w-full">
  @if (! $isLoaded)
    <!-- SKELETON LOADER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-soft animate-pulse">
      <div class="h-3 bg-slate-100 rounded w-1/2"></div>
      <div class="h-36 bg-slate-100 rounded-2xl w-full"></div>
      <div class="space-y-2">
        <div class="h-4 bg-slate-100 rounded w-3/4"></div>
        <div class="h-3 bg-slate-100 rounded w-1/3"></div>
      </div>
      <div class="h-8 bg-slate-100 rounded-xl w-full"></div>
    </div>
  @elseif ($listing)
    @php
      $itemType = $listing->item?->item_type ?? 'whole';
      $catName = strtoupper($listing->item?->deviceModel?->category?->name ?? 'DEVICE');
      $isScrap = $itemType === 'scrap';
      $isPart = $itemType === 'part';

      $badgeClass = $isScrap ? 'bg-amber-600 text-white' : ($isPart ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-white');
      $badgeLabel = $isScrap ? 'SCRAP' : ($isPart ? 'PART' : (strlen($catName) <= 8 ? $catName : 'DEVICE'));

      $fallbackEmoji = '💻';
      if (str_contains(strtolower($catName), 'vehicle') || str_contains(strtolower($catName), 'car')) {
          $fallbackEmoji = '🚗';
      } elseif ($isPart || str_contains(strtolower($catName), 'battery')) {
          $fallbackEmoji = '🔋';
      } elseif (str_contains(strtolower($catName), 'phone')) {
          $fallbackEmoji = '📱';
      }

      $sellerName = $listing->seller?->business_name ?: ($listing->seller?->name ?? 'Verified Seller');
      $locationObj = $listing->item?->location ?? $listing->seller?->primaryLocation;
      $city = $locationObj?->city;
      $stateName = $locationObj?->state?->name ?? 'Nigeria';
      $locationLabel = $city ? "{$city}, {$stateName}" : $stateName;
      $currencySymbol = $currentCurrencySymbol ?? ($listing->seller?->country?->currency_symbol ?? '₦');
    @endphp

    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-soft">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center justify-between">
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-pp-500 animate-pulse"></span>
          <span>Featured in Marketplace</span>
        </span>
        @if ($listing->activePromotion)
          <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-pp-100 text-pp-700 tracking-wider">SPONSORED</span>
        @endif
      </h3>

      <div class="relative rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 aspect-16/10 grid place-items-center">
        <span class="absolute top-2.5 left-2.5 text-[9px] font-extrabold px-2 py-0.5 rounded {{ $badgeClass }} uppercase tracking-wider z-10">
          {{ $badgeLabel }}
        </span>

        @if ($listing->primary_image)
          <img src="{{ $listing->primary_image->url ?? asset('storage/' . $listing->primary_image->path) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
        @else
          <span class="text-5xl">{{ $fallbackEmoji }}</span>
        @endif
      </div>

      <div class="space-y-1">
        <span class="text-[11px] font-bold text-pp-600 block truncate">{{ $sellerName }}</span>
        <h4 class="font-extrabold text-sm text-slate-900 leading-snug line-clamp-2">{{ $listing->title }}</h4>
        <p class="text-[11px] text-slate-500 truncate">
          {{ $listing->item?->deviceModel?->name ?? ($listing->item?->description ?: 'Verified item') }}
        </p>
      </div>

      <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-400 block font-medium">Price</span>
          <span class="text-base font-black text-slate-900">{{ $currencySymbol }}{{ number_format($listing->price, 2) }}</span>
        </div>
        <div class="text-right">
          <span class="text-[10px] text-slate-400 block">📍 {{ $locationLabel }}</span>
          <span class="text-[10px] font-bold text-emerald-600">
            @if ($isScrap) For Salvage @else {{ $listing->availableQuantity() }} in stock @endif
          </span>
        </div>
      </div>

      <a
        href="{{ route('listing-details', $listing->id) }}"
        class="block w-full py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition text-center"
      >
        View Listing Details →
      </a>
    </div>
  @endif
</div>
