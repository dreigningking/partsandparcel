@php
    $itemType = $listing->item?->item_type ?? 'whole';
    $catName = strtoupper($listing->item?->deviceModel?->category?->name ?? 'DEVICE');
    $isScrap = $itemType === 'scrap';
    $isPart = $itemType === 'part';

    // Badge styling & label
    if ($isScrap) {
        $badgeClass = 'bg-amber-600 text-white';
        $badgeLabel = 'SCRAP';
        $cardBorderClass = 'border-amber-300 bg-amber-50/20';
        $sellerTextClass = 'text-amber-700';
        $titleHoverClass = 'group-hover:text-amber-700';
        $subtextClass = 'text-amber-700 font-semibold';
        $stockClass = 'text-amber-700 font-bold';
        $dividerClass = 'border-amber-100';
    } elseif ($isPart) {
        $badgeClass = 'bg-emerald-600 text-white';
        $badgeLabel = 'PART';
        $cardBorderClass = 'border-slate-200 bg-white';
        $sellerTextClass = 'text-pp-600';
        $titleHoverClass = 'group-hover:text-pp-600';
        $subtextClass = 'text-slate-500';
        $stockClass = 'text-emerald-600 font-semibold';
        $dividerClass = 'border-slate-100';
    } else {
        $badgeClass = 'bg-slate-900 text-white';
        $badgeLabel = in_array($catName, ['VEHICLE', 'CAR', 'AUTO']) ? 'VEHICLE' : (strlen($catName) <= 8 ? $catName : 'DEVICE');
        $cardBorderClass = 'border-slate-200 bg-white';
        $sellerTextClass = 'text-pp-600';
        $titleHoverClass = 'group-hover:text-pp-600';
        $subtextClass = 'text-slate-500';
        $stockClass = 'text-emerald-600 font-semibold';
        $dividerClass = 'border-slate-100';
    }

    // Emoji icon fallback based on category or item type
    $fallbackEmoji = '💻';
    if (str_contains(strtolower($catName), 'vehicle') || str_contains(strtolower($catName), 'car') || str_contains(strtolower($catName), 'auto')) {
        $fallbackEmoji = '🚗';
    } elseif ($isPart || str_contains(strtolower($catName), 'battery')) {
        $fallbackEmoji = '🔋';
    } elseif (str_contains(strtolower($catName), 'phone') || str_contains(strtolower($catName), 'tablet')) {
        $fallbackEmoji = '📱';
    }

    $sellerName = $listing->seller?->business_name ?: ($listing->seller?->name ?? 'Verified Seller');
    $locationObj = $listing->item?->location ?? $listing->seller?->primaryLocation;
    $city = $locationObj?->city;
    $stateName = $locationObj?->state?->name ?? ($locationObj?->state ?: 'Nigeria');
    $locationLabel = $city ? "{$city}, {$stateName}" : $stateName;

    $condition = $listing->item?->condition_status ? ucwords(str_replace('_', ' ', $listing->item->condition_status)) : ($isScrap ? 'Damaged' : 'Used');
    $currencySymbol = $currentCurrencySymbol ?? ($listing->seller?->country?->currency_symbol ?? '₦');
    $hasPromo = $isPromoted || (bool) $listing->activePromotion;
@endphp

<a href="{{ route('listing-details', $listing->id) }}" class="group rounded-2xl border {{ $cardBorderClass }} overflow-hidden hover:shadow-card hover:-translate-y-1 transition duration-200 flex flex-col justify-between relative">
  <div>
    <!-- MEDIA / IMAGE SECTION -->
    <div class="relative product-img h-44 grid place-items-center p-4 bg-slate-50/50 overflow-hidden">
      <!-- CATEGORY BADGE -->
      <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded {{ $badgeClass }} uppercase tracking-wider">
          {{ $badgeLabel }}
        </span>
        @if ($hasPromo)
          <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-pp-500/90 text-white tracking-widest uppercase shadow-xs">
            SPONSORED
          </span>
        @endif
      </div>

      <!-- IMAGE OR FALLBACK -->
      @if ($listing->primary_image)
        <img src="{{ $listing->primary_image->url ?? asset('storage/' . $listing->primary_image->path) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
      @else
        <span class="text-6xl group-hover:scale-105 transition">{{ $fallbackEmoji }}</span>
      @endif
    </div>

    <!-- CONTENT BODY -->
    <div class="p-4">
      <span class="text-[11px] font-bold {{ $sellerTextClass }} block truncate">
        {{ $sellerName }}
      </span>

      <h3 class="text-sm font-bold text-slate-900 truncate mt-0.5 {{ $titleHoverClass }}">
        {{ $listing->title }}
      </h3>

      <p class="text-[11px] {{ $subtextClass }} truncate mt-0.5">
        {{ $listing->item?->deviceModel?->name ?? ($listing->item?->description ?: 'Verified item') }}
      </p>

      <div class="mt-3 flex items-baseline justify-between">
        <span class="text-base font-extrabold text-slate-900">
          {{ $currencySymbol }}{{ number_format($listing->price, 2) }}
        </span>

        <span class="text-[10px] {{ $stockClass }}">
          @if ($isScrap)
            For Salvage
          @else
            {{ $listing->availableQuantity() }} available
          @endif
        </span>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="px-4 pb-3">
    <div class="pt-2 border-t {{ $dividerClass }} flex items-center justify-between text-[11px] text-slate-400">
      <span class="truncate max-w-[120px]" title="{{ $locationLabel }}">📍 {{ $locationLabel }}</span>
      <span class="text-slate-500 font-medium shrink-0">{{ $condition }}</span>
    </div>
  </div>
</a>
