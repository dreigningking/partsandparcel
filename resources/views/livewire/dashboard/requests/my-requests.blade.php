<div class="space-y-6">

  <!-- PAGE HEADER & PRIMARY ACTIONS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-black text-slate-950 tracking-tight">My Community Requests</h1>
        <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[11px] font-extrabold">
          {{ $counts['all'] }} Total
        </span>
      </div>
      <p class="text-xs text-slate-500 mt-1">Track your open product, spare part &amp; service requests posted to the community hub.</p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('community') }}" class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition flex items-center gap-2">
        <i class="fas fa-plus text-[11px]"></i>
        <span>Post New Request</span>
      </a>
    </div>
  </div>

  <!-- FLASH NOTIFICATION -->
  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- STATUS TABS & FILTER TOOLBAR -->
  <div class="bg-white rounded-3xl border border-slate-200/90 p-3 sm:p-4 shadow-soft space-y-3">
    
    <!-- TOP TABS (Open, Fulfilled, Closed, All) -->
    <div class="flex items-center justify-between gap-3 flex-wrap border-b border-slate-100 pb-3">
      <div class="flex items-center gap-1.5 bg-slate-50 p-1.5 rounded-2xl border border-slate-200/80 text-xs font-bold overflow-x-auto scrollbar-none">
        
        <button
          type="button"
          wire:click="setTab('open')"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'open' ? 'bg-pp-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}"
        >
          <span class="w-2 h-2 rounded-full {{ $activeTab === 'open' ? 'bg-white animate-pulse' : 'bg-emerald-500' }}"></span>
          <span>Open Proposals</span>
          <span class="text-[10px] px-2 py-0.5 rounded-full {{ $activeTab === 'open' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-black">
            {{ $counts['open'] }}
          </span>
        </button>

        <button
          type="button"
          wire:click="setTab('fulfilled')"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'fulfilled' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}"
        >
          <i class="fas fa-check-circle text-[11px] {{ $activeTab === 'fulfilled' ? 'text-white' : 'text-emerald-600' }}"></i>
          <span>Fulfilled</span>
          <span class="text-[10px] px-2 py-0.5 rounded-full {{ $activeTab === 'fulfilled' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-black">
            {{ $counts['fulfilled'] }}
          </span>
        </button>

        <button
          type="button"
          wire:click="setTab('closed')"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'closed' ? 'bg-slate-900 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}"
        >
          <i class="fas fa-lock text-[10px] {{ $activeTab === 'closed' ? 'text-white' : 'text-slate-400' }}"></i>
          <span>Closed</span>
          <span class="text-[10px] px-2 py-0.5 rounded-full {{ $activeTab === 'closed' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-black">
            {{ $counts['closed'] }}
          </span>
        </button>

        <button
          type="button"
          wire:click="setTab('all')"
          class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'all' ? 'bg-slate-950 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}"
        >
          <span>All Requests</span>
          <span class="text-[10px] px-2 py-0.5 rounded-full {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-black">
            {{ $counts['all'] }}
          </span>
        </button>
      </div>

      <!-- SORT DROPDOWN -->
      <div class="flex items-center gap-2 text-xs">
        <label for="sortBy" class="text-slate-400 font-bold shrink-0">Sort by:</label>
        <select
          id="sortBy"
          wire:model.live="sortBy"
          class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20"
        >
          <option value="latest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="most_offers">Most Offers Received</option>
          <option value="most_responses">Most Comments</option>
        </select>
      </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="flex flex-col sm:flex-row items-center gap-3">
      
      <!-- Search Input -->
      <div class="relative flex-1 w-full">
        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input
          type="text"
          wire:model.live.debounce.300ms="search"
          placeholder="Search requests by title, part name, specifications, or budget..."
          class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        />
        @if (!empty($search))
          <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        @endif
      </div>

      <!-- Type Filter -->
      <div class="w-full sm:w-auto shrink-0">
        <select
          wire:model.live="typeFilter"
          class="w-full sm:w-auto bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20"
        >
          <option value="all">All Types</option>
          <option value="item">📦 Parts / Products</option>
          <option value="service">🛠️ Services / Repairs</option>
          <option value="advice">💡 Technical Advice</option>
        </select>
      </div>

      <!-- Category Filter -->
      @if ($categories->isNotEmpty())
        <div class="w-full sm:w-auto shrink-0">
          <select
            wire:model.live="categoryFilter"
            class="w-full sm:w-auto bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20"
          >
            <option value="all">All Categories</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <!-- Reset Filters Button -->
      @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all' || $sortBy !== 'latest')
        <button
          wire:click="resetFilters"
          class="text-xs text-rose-600 hover:text-rose-700 font-bold px-2 py-1 cursor-pointer flex items-center gap-1 shrink-0"
        >
          <i class="fas fa-rotate-left text-[10px]"></i>
          <span>Reset Filters</span>
        </button>
      @endif
    </div>

  </div>

  <!-- REQUEST CARDS STREAM -->
  <div class="space-y-4">
    @forelse ($userRequests as $req)
      @php
        $offersCount = $req->offers->count();
        $responsesCount = $req->responses->count();
        $budget = $req->budget ?: ($req->attachments['budget'] ?? 'Flexible');
        $location = $req->location_text ?? ($req->attachments['location'] ?? 'Nigeria');
        $isOpen = ($req->status === 'open');
        $isFulfilled = in_array($req->status, ['resolved', 'fulfilled']);
        $isClosed = ($req->status === 'closed');
        $modStatus = $req->latestModeration?->status ?? 'approved';
        $hasOffers = $offersCount > 0;
        $lowestPrice = $hasOffers ? $req->offers->min(fn($o) => $o->total()) : null;
        $primaryImg = $req->primary_image_url;
      @endphp

      <div class="bg-white rounded-3xl border transition-all duration-200 p-5 sm:p-6 space-y-4 shadow-soft hover:shadow-md {{ $hasOffers ? 'border-pp-300 ring-1 ring-pp-100/50' : 'border-slate-200/90 hover:border-slate-300' }}">
        
        <!-- TOP META BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs border-b border-slate-100 pb-3.5">
          <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Type Pill -->
            @if ($req->type === 'item')
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 font-bold text-[10px] border border-sky-200/60">
                <i class="fas fa-box-open text-[9px]"></i> Part / Product
              </span>
            @elseif ($req->type === 'service')
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200/60">
                <i class="fas fa-wrench text-[9px]"></i> Service / Repair
              </span>
            @else
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-200/60">
                <i class="fas fa-comments text-[9px]"></i> Technical Advice
              </span>
            @endif

            <!-- Category Tag -->
            @if ($req->category)
              <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                {{ $req->category->name }}
              </span>
            @endif

            <!-- Brand / Model Tags -->
            @if ($req->brand || $req->deviceModel)
              <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium text-[10px]">
                {{ $req->brand?->name }} {{ $req->deviceModel?->name }}
              </span>
            @endif

            <!-- Moderation Badge -->
            @if ($modStatus === 'pending')
              <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200 flex items-center gap-1">
                <i class="fas fa-hourglass-half text-[9px]"></i> Pending Moderation
              </span>
            @elseif ($modStatus === 'rejected')
              <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 font-bold text-[10px] border border-rose-200 flex items-center gap-1">
                <i class="fas fa-ban text-[9px]"></i> Content Rejected
              </span>
            @endif

            <span class="text-slate-300 hidden sm:inline">·</span>
            <span class="text-slate-400 font-medium text-[11px] flex items-center gap-1">
              <i class="far fa-clock text-[10px]"></i> {{ $req->created_at->diffForHumans() }}
            </span>
          </div>

          <!-- Right Status & Ref -->
          <div class="flex items-center gap-2">
            <span class="font-mono text-slate-400 text-[11px] font-bold">#REQ-{{ $req->id }}</span>
            <span class="text-slate-300">·</span>

            @if ($isOpen)
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[10px] border border-emerald-200/80">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>OPEN FOR OFFERS</span>
              </span>
            @elseif ($isFulfilled)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[10px] border border-blue-200/80">
                <i class="fas fa-check-circle text-[9px]"></i>
                <span>FULFILLED</span>
              </span>
            @elseif ($isClosed)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[10px] border border-slate-200">
                <i class="fas fa-lock text-[9px]"></i>
                <span>CLOSED</span>
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px]">
                {{ strtoupper($req->status) }}
              </span>
            @endif
          </div>
        </div>

        <!-- MAIN BODY (Thumbnail + Info + Offer Spotlight) -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
          
          <!-- Content Block (Thumbnail + Title & Specs) -->
          <div class="flex items-start gap-4 flex-1">
            
            <!-- Thumbnail Image / Icon -->
            @if ($primaryImg)
              <a href="{{ route('myrequest.view', ['id' => $req->id]) }}" class="shrink-0 group">
                <img
                  src="{{ $primaryImg }}"
                  alt="{{ $req->title }}"
                  class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-200 group-hover:border-pp-500 transition shadow-2xs"
                />
              </a>
            @else
              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200/80 grid place-items-center text-slate-400 text-xl sm:text-2xl shrink-0 shadow-2xs">
                @if ($req->type === 'service')
                  <i class="fas fa-wrench text-amber-500/70"></i>
                @elseif ($req->type === 'advice')
                  <i class="fas fa-lightbulb text-indigo-500/70"></i>
                @else
                  <i class="fas fa-gears text-pp-500/70"></i>
                @endif
              </div>
            @endif

            <!-- Title & Parameters -->
            <div class="space-y-1.5 flex-1 min-w-0">
              <a href="{{ route('myrequest.view', ['id' => $req->id]) }}" class="text-base sm:text-lg font-extrabold text-slate-900 hover:text-pp-600 transition line-clamp-1 block tracking-tight">
                {{ $req->title }}
              </a>

              <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                {{ $req->body }}
              </p>

              <!-- Parameter Chips -->
              <div class="flex items-center gap-2 flex-wrap pt-1 text-xs">
                
                <!-- Target Budget -->
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-pp-50 text-pp-800 font-extrabold text-[11px] border border-pp-100">
                  <i class="fas fa-tag text-[10px] text-pp-600"></i>
                  <span>Budget: {{ $budget }}</span>
                </span>

                <!-- Location -->
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-50 text-slate-600 font-bold text-[11px] border border-slate-200/80">
                  <i class="fas fa-map-marker-alt text-[10px] text-slate-400"></i>
                  <span>{{ $location }}</span>
                </span>

                <!-- Fulfillment -->
                @if (!empty($req->fulfillment) && $req->fulfillment !== 'Flexible')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-50 text-slate-600 font-medium text-[11px] border border-slate-200/80">
                    <i class="fas fa-truck text-[10px] text-slate-400"></i>
                    <span>{{ ucfirst($req->fulfillment) }}</span>
                  </span>
                @endif

                <!-- Urgency -->
                @if (!empty($req->urgency) && $req->urgency !== 'Flexible' && $req->urgency !== 'standard')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-700 font-extrabold text-[11px] border border-rose-100">
                    <i class="fas fa-bolt text-[10px] text-rose-500"></i>
                    <span>Urgent</span>
                  </span>
                @endif

              </div>
            </div>

          </div>

          <!-- Right Offer Spotlight Block -->
          <div class="w-full lg:w-auto shrink-0 flex flex-col sm:flex-row lg:flex-col items-stretch lg:items-end gap-2">
            @if ($hasOffers)
              <div class="bg-gradient-to-br from-emerald-50/90 to-emerald-100/50 border border-emerald-200/90 rounded-2xl p-3 sm:px-4 text-left lg:text-right min-w-[200px] space-y-1 shadow-2xs">
                <span class="text-[10px] uppercase font-black tracking-wider text-emerald-800 flex items-center lg:justify-end gap-1.5">
                  <i class="fas fa-handshake text-emerald-600"></i>
                  <span>{{ $offersCount }} Private {{ Str::plural('Offer', $offersCount) }} Received</span>
                </span>
                @if ($lowestPrice && $lowestPrice > 0)
                  <span class="text-base sm:text-lg font-black text-emerald-950 block">
                    From ₦{{ number_format($lowestPrice) }}
                  </span>
                @endif
                <span class="text-[10px] font-bold text-emerald-700 block">
                  Offers ready for review &amp; checkout
                </span>
              </div>
            @else
              <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-3 sm:px-4 text-left lg:text-right min-w-[190px] space-y-0.5">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">
                  Private Offers
                </span>
                <span class="text-xs font-bold text-slate-600 block">
                  Awaiting vendor proposals
                </span>
                <span class="text-[10px] text-slate-400 block">
                  {{ $responsesCount }} {{ Str::plural('discussion comment', $responsesCount) }}
                </span>
              </div>
            @endif
          </div>

        </div>

        <!-- FOOTER ACTION BAR -->
        <div class="border-t border-slate-100 pt-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          
          <!-- Left Activity Stats -->
          <div class="flex items-center gap-3 text-xs text-slate-500">
            <span class="flex items-center gap-1.5 font-bold text-slate-700">
              <i class="fas fa-comments text-slate-400"></i>
              <span>{{ $responsesCount }} {{ Str::plural('Reply', $responsesCount) }}</span>
            </span>
            <span class="text-slate-300">·</span>
            <span class="flex items-center gap-1.5 font-bold {{ $hasOffers ? 'text-pp-700 font-extrabold' : 'text-slate-500' }}">
              <i class="fas fa-file-invoice-dollar {{ $hasOffers ? 'text-pp-600' : 'text-slate-400' }}"></i>
              <span>{{ $offersCount }} {{ Str::plural('Vendor Offer', $offersCount) }}</span>
            </span>
          </div>

          <!-- Right Action Buttons -->
          <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Quick Lifecycle Status Actions -->
            @if ($isOpen)
              <button
                type="button"
                wire:click="markFulfilled({{ $req->id }})"
                class="px-3 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
                title="Mark this request as fulfilled"
              >
                <i class="fas fa-check text-[10px]"></i>
                <span>Mark Fulfilled</span>
              </button>

              <button
                type="button"
                wire:click="closeRequest({{ $req->id }})"
                class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
                title="Close request to stop receiving new proposals"
              >
                <i class="fas fa-lock text-[10px]"></i>
                <span>Close</span>
              </button>
            @elseif ($isClosed || $isFulfilled)
              <button
                type="button"
                wire:click="reopenRequest({{ $req->id }})"
                class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
                title="Reopen request for proposals"
              >
                <i class="fas fa-rotate-left text-[10px]"></i>
                <span>Reopen</span>
              </button>
            @endif

            <!-- Public Thread Link -->
            <a
              href="{{ route('community.request', ['id' => $req->id]) }}"
              target="_blank"
              class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1.5"
              title="Open public community thread"
            >
              <span>Public Thread</span>
              <i class="fas fa-external-link-alt text-[10px] text-slate-400"></i>
            </a>

            <!-- Primary Manage CTA -->
            <a
              href="{{ route('myrequest.view', ['id' => $req->id]) }}"
              class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition inline-flex items-center gap-1.5"
            >
              <span>Manage &amp; Offers</span>
              @if ($offersCount > 0)
                <span class="w-5 h-5 rounded-full bg-white text-pp-700 font-black text-[10px] grid place-items-center">
                  {{ $offersCount }}
                </span>
              @else
                <i class="fas fa-arrow-right text-[10px]"></i>
              @endif
            </a>

            <!-- Delete Action with confirmation -->
            <button
              type="button"
              wire:click="deleteRequest({{ $req->id }})"
              wire:confirm="Are you sure you want to delete this community request? All related responses and offers will also be removed."
              class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
              title="Delete request"
            >
              <i class="far fa-trash-alt text-xs"></i>
            </button>

          </div>

        </div>

      </div>
    @empty
      <!-- EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto shadow-2xs">
          <i class="fas fa-bullhorn"></i>
        </div>
        <div class="space-y-1.5">
          <h3 class="font-extrabold text-slate-900 text-base">
            @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all')
              No Requests Match Your Filters
            @elseif ($activeTab === 'fulfilled')
              No Fulfilled Requests Found
            @elseif ($activeTab === 'closed')
              No Closed Requests Found
            @else
              You Haven't Posted Any Requests Yet
            @endif
          </h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
            @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all')
              Try clearing search terms or resetting the filter options above to see all your posted requests.
            @else
              Can't find a specific spare part, component, or repair technician? Post what you're looking for to receive private offers from verified vendors across the country!
            @endif
          </p>
        </div>
        <div class="pt-2 flex items-center justify-center gap-3 flex-wrap">
          @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all')
            <button wire:click="resetFilters" class="py-2.5 px-4 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">
              Reset Filters
            </button>
          @endif
          <a href="{{ route('community') }}" class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-extrabold text-xs hover:bg-pp-700 transition inline-flex items-center gap-2 shadow-xs">
            <i class="fas fa-plus text-[11px]"></i>
            <span>Post a Community Request</span>
          </a>
        </div>
      </div>
    @endforelse
  </div>

  <!-- PAGINATION LINKS -->
  @if ($userRequests instanceof \Illuminate\Pagination\LengthAwarePaginator && $userRequests->hasPages())
    <div class="pt-2">
      {{ $userRequests->links() }}
    </div>
  @endif

</div>
