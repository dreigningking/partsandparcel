<div class="min-h-screen bg-slate-50/60 pb-20">

  <!-- PROFILE COVER & HEADER BANNER -->
  <div class="relative bg-gradient-to-r from-pp-900 via-pp-800 to-slate-900 text-white pt-12 pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden shadow-md">
    <!-- Subtle background pattern overlay -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-pp-200/80 mb-6">
        <a href="{{ route('welcome') }}" class="hover:text-white transition">Marketplace</a>
        <span>/</span>
        <span class="text-white font-bold">Seller Profile</span>
        <span>/</span>
        <span class="text-pp-300 truncate">{{ $user->business_name ?: $user->name }}</span>
      </nav>
    </div>
  </div>

  <!-- MAIN CONTAINER (Negative margin to overlap header banner) -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 space-y-8">
    
    <!-- USER DETAILS CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-soft">
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
        
        <!-- LEFT: AVATAR & BASIC DETAILS -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
          
          <!-- AVATAR -->
          <div class="relative shrink-0">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-pp-100 text-pp-700 border-4 border-white shadow-card overflow-hidden grid place-items-center">
              @if ($user->avatar)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
              @else
                @php
                  $initials = collect(explode(' ', $user->name))->map(fn($seg) => strtoupper(substr($seg, 0, 1)))->take(2)->join('');
                @endphp
                <span class="text-3xl font-black text-pp-700 tracking-wider">{{ $initials ?: 'U' }}</span>
              @endif
            </div>

            @if ($user->is_verified)
              <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-emerald-500 text-white grid place-items-center border-2 border-white shadow-xs text-xs" title="Verified Merchant">
                <i class="fas fa-check"></i>
              </span>
            @endif
          </div>

          <!-- NAMES & BADGES -->
          <div class="space-y-2">
            <div>
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                  {{ $user->business_name ?: $user->name }}
                </h1>
                
                @if ($user->is_verified)
                  <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold flex items-center gap-1">
                    <i class="fas fa-shield-alt text-emerald-600"></i> Verified Seller
                  </span>
                @endif
              </div>

              @if ($user->business_name && $user->name)
                <p class="text-xs font-bold text-slate-500 mt-0.5">Operated by {{ $user->name }}</p>
              @endif
            </div>

            <!-- META INFO -->
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 sm:gap-4 text-xs text-slate-500 font-medium">
              <span class="flex items-center gap-1.5">
                <i class="fas fa-calendar-alt text-slate-400"></i>
                Member since {{ $user->created_at->format('M Y') }}
              </span>

              @if ($user->country)
                <span class="flex items-center gap-1.5">
                  <i class="fas fa-map-marker-alt text-slate-400"></i>
                  {{ $user->country->name }}
                </span>
              @endif

              @if ($user->primaryLocation)
                <span class="flex items-center gap-1.5">
                  <i class="fas fa-store text-slate-400"></i>
                  {{ $user->primaryLocation->city }}, {{ $user->primaryLocation->state }}
                </span>
              @endif
            </div>

            <!-- BIO -->
            @if ($user->bio)
              <p class="text-xs text-slate-600 max-w-2xl leading-relaxed pt-1">
                {{ $user->bio }}
              </p>
            @endif
          </div>
        </div>

        <!-- RIGHT: SELLER SUMMARY STATS & ACTION BUTTONS -->
        <div class="flex flex-col sm:flex-row md:flex-col items-center md:items-end justify-between gap-4 shrink-0 border-t md:border-t-0 pt-4 md:pt-0 border-slate-100">
          
          <!-- QUICK STATS PILLS -->
          <div class="flex items-center gap-3 text-center">
            <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-100">
              <div class="text-lg font-black text-slate-900">{{ $listingsCount }}</div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Listings</div>
            </div>

            <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-100">
              <div class="text-lg font-black text-slate-900">{{ $servicesCount }}</div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Services</div>
            </div>

            <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-100">
              <div class="text-lg font-black text-amber-500 flex items-center justify-center gap-1">
                <span>{{ $averageRating ? number_format($averageRating, 1) : '—' }}</span>
                <i class="fas fa-star text-xs"></i>
              </div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $totalReviewsCount }} Reviews</div>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-2.5 w-full sm:w-auto">
            @auth
              @if (auth()->id() !== $user->id)
                <button type="button" wire:click="$dispatch('open-conversation', { id: {{ $user->id }} })" class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-2">
                  <i class="fas fa-comment-dots"></i> Message Seller
                </button>
              @else
                <a href="{{ route('profile') }}" class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center justify-center gap-2">
                  <i class="fas fa-pencil-alt"></i> Edit Your Profile
                </a>
              @endif
            @else
              <a href="{{ route('login') }}" class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2">
                <i class="fas fa-comment-dots"></i> Sign in to Message
              </a>
            @endauth

            <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('Seller profile link copied to clipboard!');" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer" title="Copy Store Link">
              <i class="fas fa-share-alt"></i>
            </button>
          </div>

        </div>

      </div>
    </div>

    <!-- HORIZONTAL TABS NAVIGATION -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1.5 shadow-2xs">
      <div class="flex items-center gap-2 overflow-x-auto">
        
        <!-- TAB 1: LISTINGS -->
        <button type="button" wire:click="setTab('listings')" class="flex-1 min-w-[140px] py-3 px-4 rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer {{ $tab === 'listings' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
          <i class="fas fa-boxes"></i>
          <span>Listings</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'listings' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
            {{ $listingsCount }}
          </span>
        </button>

        <!-- TAB 2: SERVICES -->
        <button type="button" wire:click="setTab('services')" class="flex-1 min-w-[140px] py-3 px-4 rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer {{ $tab === 'services' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
          <i class="fas fa-tools"></i>
          <span>Services &amp; Jobs</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'services' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
            {{ $servicesCount }}
          </span>
        </button>

        <!-- TAB 3: REVIEWS -->
        <button type="button" wire:click="setTab('reviews')" class="flex-1 min-w-[140px] py-3 px-4 rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer {{ $tab === 'reviews' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
          <i class="fas fa-star"></i>
          <span>Reviews &amp; Ratings</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'reviews' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
            {{ $totalReviewsCount }}
          </span>
        </button>

      </div>
    </div>

    <!-- TAB CONTENT AREA -->
    <div class="space-y-6">

      <!-- ============================================== -->
      <!-- TAB 1: LISTINGS CONTENT                         -->
      <!-- ============================================== -->
      @if ($tab === 'listings')
        @if ($listings && $listings->count() > 0)
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach ($listings as $listing)
              @php
                $item = $listing->item;
                $primaryImg = $listing->primary_image_url ?: ($item?->primary_image_url ?: asset('images/placeholder-part.png'));
                $deviceModel = $item?->deviceModel;
                $brand = $deviceModel?->brand;
              @endphp
              <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-card hover:border-pp-300 transition duration-200 flex flex-col justify-between">
                <div>
                  <!-- PRODUCT IMAGE -->
                  <a href="{{ route('listing-details', $listing->id) }}" class="relative block aspect-4/3 bg-slate-100 overflow-hidden">
                    <img src="{{ $primaryImg }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    
                    @if ($listing->warranty_period_days)
                      <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-black shadow-xs">
                        {{ $listing->warranty_period_days }}d Warranty
                      </span>
                    @endif

                    @if ($item?->condition)
                      <span class="absolute bottom-2.5 left-2.5 px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold">
                        {{ ucfirst($item->condition) }}
                      </span>
                    @endif
                  </a>

                  <!-- PRODUCT DETAILS -->
                  <div class="p-4 space-y-2">
                    @if ($brand || $deviceModel)
                      <div class="text-[10px] font-bold text-pp-600 uppercase tracking-wider truncate">
                        {{ $brand?->name }} {{ $deviceModel?->name }}
                      </div>
                    @endif

                    <a href="{{ route('listing-details', $listing->id) }}" class="font-extrabold text-xs text-slate-900 hover:text-pp-600 transition line-clamp-2 block" title="{{ $listing->title }}">
                      {{ $listing->title }}
                    </a>

                    <div class="pt-1 flex items-baseline justify-between">
                      <span class="text-base font-black text-slate-950">
                        ₦{{ number_format($listing->price, 2) }}
                      </span>

                      @if ($listing->quantity > 0)
                        <span class="text-[10px] font-bold text-emerald-600">
                          In Stock ({{ $listing->quantity }})
                        </span>
                      @else
                        <span class="text-[10px] font-bold text-slate-400">Sold Out</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="p-4 pt-0">
                  <a href="{{ route('listing-details', $listing->id) }}" class="w-full py-2 rounded-xl bg-slate-50 hover:bg-pp-50 hover:text-pp-700 text-slate-700 font-bold text-xs transition border border-slate-200 flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>View Listing</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                  </a>
                </div>
              </div>
            @endforeach
          </div>

          <div class="pt-4">
            {{ $listings->links() }}
          </div>
        @else
          <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-4 shadow-2xs">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 text-slate-400 grid place-items-center text-2xl">
              <i class="fas fa-box-open"></i>
            </div>
            <div>
              <h3 class="font-extrabold text-slate-900 text-sm">No Active Listings Available</h3>
              <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                {{ $user->business_name ?: $user->name }} currently does not have any active inventory listings published on the marketplace.
              </p>
            </div>
          </div>
        @endif
      @endif

      <!-- ============================================== -->
      <!-- TAB 2: SERVICES & SERVICE JOBS CONTENT         -->
      <!-- ============================================== -->
      @if ($tab === 'services')
        @if ($services && $services->count() > 0)
          <div class="grid md:grid-cols-2 gap-5">
            @foreach ($services as $job)
              <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4 shadow-2xs hover:shadow-card transition flex flex-col justify-between">
                <div class="space-y-3">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <h4 class="font-black text-sm text-slate-900">{{ $job->title }}</h4>
                      @if ($job->item)
                        <span class="text-xs text-pp-600 font-bold">{{ $job->item->name }}</span>
                      @elseif ($job->external_item_description)
                        <span class="text-xs text-slate-500 font-medium">{{ $job->external_item_description }}</span>
                      @endif
                    </div>

                    @php
                      $statusClasses = [
                        'completed' => 'bg-emerald-100 text-emerald-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'scheduled' => 'bg-amber-100 text-amber-800',
                        'pending' => 'bg-slate-100 text-slate-800',
                        'cancelled' => 'bg-rose-100 text-rose-800',
                      ];
                      $badgeClass = $statusClasses[$job->status] ?? 'bg-slate-100 text-slate-800';
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">
                      {{ str_replace('_', ' ', $job->status) }}
                    </span>
                  </div>

                  @if ($job->description)
                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                      {{ $job->description }}
                    </p>
                  @endif

                  <!-- WARRANTY & TIMELINE DETAILS -->
                  <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px] text-slate-500">
                    <div>
                      <span class="font-bold text-slate-400 block text-[10px] uppercase">Warranty</span>
                      @if ($job->warranty_period_days)
                        <span class="font-extrabold text-emerald-700 flex items-center gap-1">
                          <i class="fas fa-shield-alt"></i> {{ $job->warranty_period_days }} Days Warranty
                        </span>
                      @else
                        <span>No warranty specified</span>
                      @endif
                    </div>

                    <div>
                      <span class="font-bold text-slate-400 block text-[10px] uppercase">Service Date</span>
                      <span class="font-bold text-slate-700">
                        {{ $job->completed_at ? $job->completed_at->format('M d, Y') : ($job->scheduled_at ? $job->scheduled_at->format('M d, Y') : $job->created_at->format('M d, Y')) }}
                      </span>
                    </div>
                  </div>

                  <!-- ATTACHED REVIEW IF PRESENT -->
                  @if ($job->review)
                    <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200/60 space-y-1 text-xs">
                      <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800 text-[11px]">Customer Feedback</span>
                        <div class="flex items-center text-amber-500 text-[10px]">
                          @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $job->review->rating ? '' : 'text-slate-300' }}"></i>
                          @endfor
                        </div>
                      </div>
                      <p class="text-[11px] text-slate-600 italic">"{{ $job->review->review }}"</p>
                    </div>
                  @endif
                </div>

                @if ($job->location)
                  <div class="pt-2 text-[10px] text-slate-400 flex items-center gap-1 border-t border-slate-100">
                    <i class="fas fa-map-pin text-slate-400"></i> Workshop: {{ $job->location->address }}, {{ $job->location->city }}
                  </div>
                @endif
              </div>
            @endforeach
          </div>

          <div class="pt-4">
            {{ $services->links() }}
          </div>
        @else
          <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-4 shadow-2xs">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 text-slate-400 grid place-items-center text-2xl">
              <i class="fas fa-wrench"></i>
            </div>
            <div>
              <h3 class="font-extrabold text-slate-900 text-sm">No Service Jobs on Record</h3>
              <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                {{ $user->business_name ?: $user->name }} has not registered completed service maintenance jobs yet.
              </p>
            </div>
          </div>
        @endif
      @endif

      <!-- ============================================== -->
      <!-- TAB 3: REVIEWS CONTENT                         -->
      <!-- ============================================== -->
      @if ($tab === 'reviews')
        <div class="space-y-6">
          
          <!-- RATING SUMMARY SCORECARD -->
          <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-soft">
            <div class="grid md:grid-cols-12 gap-8 items-center">
              
              <!-- LEFT: BIG AVERAGE RATING -->
              <div class="md:col-span-4 text-center md:border-r md:border-slate-100 pr-0 md:pr-8 space-y-2">
                <div class="text-5xl font-black text-slate-950 tracking-tight">
                  {{ $averageRating ? number_format($averageRating, 1) : '0.0' }}
                </div>

                <div class="flex items-center justify-center gap-1 text-amber-400 text-lg">
                  @for ($i = 1; $i <= 5; $i++)
                    @if ($averageRating && $i <= floor($averageRating))
                      <i class="fas fa-star"></i>
                    @elseif ($averageRating && ($i - $averageRating) < 0.8 && ($i - $averageRating) > 0)
                      <i class="fas fa-star-half-alt"></i>
                    @else
                      <i class="fas fa-star text-slate-200"></i>
                    @endif
                  @endfor
                </div>

                <p class="text-xs font-bold text-slate-500">
                  Based on {{ $totalReviewsCount }} verified customer {{ Str::plural('review', $totalReviewsCount) }}
                </p>
              </div>

              <!-- RIGHT: STAR DISTRIBUTION BREAKDOWN -->
              <div class="md:col-span-8 space-y-2">
                @for ($star = 5; $star >= 1; $star--)
                  @php
                    $count = $starCounts[$star] ?? 0;
                    $percent = $totalReviewsCount > 0 ? round(($count / $totalReviewsCount) * 100) : 0;
                  @endphp
                  <div class="flex items-center gap-3 text-xs">
                    <span class="w-12 font-bold text-slate-600 flex items-center gap-1">
                      {{ $star }} <i class="fas fa-star text-amber-400 text-[10px]"></i>
                    </span>

                    <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                      <div class="h-full bg-amber-400 rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                    </div>

                    <span class="w-12 text-right font-semibold text-slate-400 text-[11px]">
                      {{ $count }} ({{ $percent }}%)
                    </span>
                  </div>
                @endfor
              </div>

            </div>
          </div>

          <!-- REVIEW SUB-FILTER PILLS -->
          <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button type="button" wire:click="setReviewFilter('all')" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $reviewFilter === 'all' ? 'bg-pp-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
              All Reviews ({{ $totalReviewsCount }})
            </button>

            <button type="button" wire:click="setReviewFilter('listings')" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $reviewFilter === 'listings' ? 'bg-pp-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
              <i class="fas fa-box text-[10px] mr-1"></i> Listing Reviews ({{ $listingReviewsCount }})
            </button>

            <button type="button" wire:click="setReviewFilter('services')" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer {{ $reviewFilter === 'services' ? 'bg-pp-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
              <i class="fas fa-tools text-[10px] mr-1"></i> Service Reviews ({{ $serviceReviewsCount }})
            </button>
          </div>

          <!-- REVIEWS LIST -->
          @if ($reviews && count($reviews) > 0)
            <div class="space-y-4">
              @foreach ($reviews as $rev)
                @php
                  $reviewer = $rev->reviewer;
                  $revInitials = $reviewer ? collect(explode(' ', $reviewer->name))->map(fn($seg) => strtoupper(substr($seg, 0, 1)))->take(2)->join('') : 'U';
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3 shadow-2xs">
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-black text-xs grid place-items-center shrink-0">
                        @if ($reviewer?->avatar)
                          <img src="{{ $reviewer->avatar_url }}" class="w-full h-full object-cover rounded-full">
                        @else
                          {{ $revInitials }}
                        @endif
                      </div>

                      <div>
                        <h5 class="font-extrabold text-xs text-slate-900">{{ $reviewer?->name ?? 'Verified Buyer' }}</h5>
                        <span class="text-[10px] text-slate-400">{{ $rev->created_at->diffForHumans() }}</span>
                      </div>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                      <div class="flex items-center text-amber-400 text-xs">
                        @for ($s = 1; $s <= 5; $s++)
                          <i class="fas fa-star {{ $s <= $rev->rating ? '' : 'text-slate-200' }}"></i>
                        @endfor
                      </div>

                      <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $rev->type === 'service' ? 'bg-blue-100 text-blue-800' : 'bg-pp-100 text-pp-800' }}">
                        {{ $rev->type === 'service' ? 'Service Review' : 'Listing Purchase' }}
                      </span>
                    </div>
                  </div>

                  @if ($rev->target_title)
                    <div class="text-[11px] font-bold text-pp-600 bg-slate-50 px-3 py-1 rounded-lg inline-block">
                      For: {{ $rev->target_title }}
                    </div>
                  @endif

                  <p class="text-xs text-slate-700 leading-relaxed">
                    {{ $rev->comment }}
                  </p>
                </div>
              @endforeach
            </div>

            @if (method_exists($reviews, 'links'))
              <div class="pt-4">
                {{ $reviews->links() }}
              </div>
            @endif
          @else
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-4 shadow-2xs">
              <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 text-slate-400 grid place-items-center text-2xl">
                <i class="fas fa-star-half-alt"></i>
              </div>
              <div>
                <h3 class="font-extrabold text-slate-900 text-sm">No Customer Reviews Yet</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                  Verified customer reviews for orders and completed repair services will appear here.
                </p>
              </div>
            </div>
          @endif

        </div>
      @endif

    </div>

  </div>

</div>
