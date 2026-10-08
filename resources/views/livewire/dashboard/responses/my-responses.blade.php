<div class="space-y-6">

  <!-- PAGE HEADER & PRIMARY ACTIONS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-black text-slate-950 tracking-tight">My Community Hub Responses</h1>
        <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[11px] font-extrabold">
          {{ $totalCount }} Total
        </span>
      </div>
      <p class="text-xs text-slate-500 mt-1">
        Track your submitted proposals, commercial quotes, and negotiation offers for buyer community requests.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('community') }}" class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition flex items-center gap-2">
        <i class="fas fa-search text-[11px]"></i>
        <span>Browse Open Requests</span>
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

  @if (session()->has('warning'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-600 text-base"></i>
        <span>{{ session('warning') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- FILTER TOOLBAR -->
  <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-soft space-y-3">
    
    <!-- LINE 1 (WEB): Search | Category | Request Type -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
      
      <!-- Search Input (takes 6 cols on md/lg) -->
      <div class="md:col-span-6 relative w-full">
        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input
          type="text"
          wire:model.live.debounce.300ms="search"
          placeholder="Search requests, response text, parts, or specifications..."
          class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        />
        @if (!empty($search))
          <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        @endif
      </div>

      <!-- Category Filter (takes 3 cols) -->
      <div class="md:col-span-3">
        <select
          wire:model.live="categoryFilter"
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        >
          <option value="all">All Categories</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Request Type Filter (takes 3 cols) -->
      <div class="md:col-span-3">
        <select
          wire:model.live="typeFilter"
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        >
          <option value="all">All Request Types</option>
          <option value="item">📦 Parts / Products</option>
          <option value="service">🛠️ Services / Repairs</option>
          <option value="advice">💡 Technical Advice</option>
        </select>
      </div>

    </div>

    <!-- LINE 2 (WEB): Request Status | Offers Status | Sort -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-2 border-t border-slate-100">
      
      <!-- Request Status Filter (takes 4 cols) -->
      <div class="sm:col-span-4">
        <select
          wire:model.live="requestStatusFilter"
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        >
          <option value="all">All Request Statuses</option>
          <option value="open">🟢 Open Proposals</option>
          <option value="fulfilled">✅ Fulfilled / Resolved</option>
          <option value="closed">🔒 Closed</option>
        </select>
      </div>

      <!-- Offers Status Filter (takes 4 cols) -->
      <div class="sm:col-span-4">
        <select
          wire:model.live="offerStatusFilter"
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        >
          <option value="all">All Offers Statuses</option>
          <option value="pending">🟡 Pending Review</option>
          <option value="countered">🔄 Counter-Offer In Progress</option>
          <option value="accepted">✅ Accepted &amp; Reserved</option>
          <option value="declined">❌ Declined</option>
          <option value="cancelled">🚫 Cancelled / Expired</option>
          <option value="none">💬 No Commercial Offer</option>
        </select>
      </div>

      <!-- Sort Filter & Reset Button (takes 4 cols) -->
      <div class="sm:col-span-4 flex items-center gap-2">
        <select
          id="sortBy"
          wire:model.live="sortBy"
          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
        >
          <option value="latest">Sort: Newest First</option>
          <option value="oldest">Sort: Oldest First</option>
          <option value="most_offers">Sort: Most Offers Attached</option>
        </select>

        @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all' || $requestStatusFilter !== 'all' || $offerStatusFilter !== 'all' || $sortBy !== 'latest')
          <button
            wire:click="resetFilters"
            class="text-xs text-rose-600 hover:text-rose-700 font-bold px-2 py-1 cursor-pointer flex items-center gap-1 shrink-0"
            title="Reset Filters"
          >
            <i class="fas fa-rotate-left text-[11px]"></i>
            <span class="hidden md:inline">Reset</span>
          </button>
        @endif
      </div>

    </div>

  </div>

  <!-- RESPONSES STREAM -->
  <div class="space-y-4">
    @forelse ($userResponses as $resp)
      @php
        $discussion = $resp->discussion;
        $buyer = $discussion?->user;
        $buyerName = $buyer?->business_name ?: $buyer?->name ?: 'Community Requester';
        $buyerLocation = $discussion?->location?->name ?: ($buyer?->primaryLocation?->state?->name ?: 'Nigeria');
        
        // Compute all offers involving buyer and seller on this discussion:
        $buyerId = $discussion?->user_id;
        $sellerId = $resp->user_id;

        $discussionOffers = $discussion?->offers?->filter(function ($off) use ($buyerId, $sellerId, $resp) {
            return $off->response_id === $resp->id
                || (
                    ($off->sender_id === $sellerId && $off->recipient_id === $buyerId)
                    || ($off->sender_id === $buyerId && $off->recipient_id === $sellerId)
                );
        }) ?? collect();

        $allInvolvedOffers = $discussionOffers->merge($resp->offers)->unique('id')->values();
        $offersCount = $allInvolvedOffers->count();
        $latestOffer = $allInvolvedOffers->sortByDesc('id')->first();

        $hasOffers = $offersCount > 0;
        $isAccepted = $latestOffer && ($latestOffer->status === 'accepted');
        $isCountered = $latestOffer && ($latestOffer->status === 'countered');
        $isPending = $latestOffer && ($latestOffer->status === 'pending');
        $isDeclined = $latestOffer && ($latestOffer->status === 'declined');
        $isCancelled = $latestOffer && in_array($latestOffer->status, ['cancelled', 'expired']);

        $isReqOpen = ($discussion?->status === 'open');
        $isReqFulfilled = in_array($discussion?->status, ['resolved', 'fulfilled']);
        $isReqClosed = ($discussion?->status === 'closed');
        $canEdit = ($offersCount === 0 && ! $isReqClosed);
      @endphp

      <div class="bg-white rounded-3xl border transition-all duration-200 p-5 sm:p-6 space-y-5 shadow-soft hover:shadow-md {{ $isAccepted ? 'border-emerald-300 ring-1 ring-emerald-100 bg-emerald-50/10' : ($isCountered ? 'border-amber-300 ring-1 ring-amber-100 bg-amber-50/10' : ($hasOffers ? 'border-pp-300 ring-1 ring-pp-100/40' : 'border-slate-200/90 hover:border-slate-300')) }}">
        
        <!-- 1. TOP META BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs border-b border-slate-100 pb-3.5">
          <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Type Pill -->
            @if ($discussion?->type === 'item')
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 font-bold text-[10px] border border-sky-200/60">
                <i class="fas fa-box-open text-[9px]"></i> Part / Product
              </span>
            @elseif ($discussion?->type === 'service')
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200/60">
                <i class="fas fa-wrench text-[9px]"></i> Service / Repair
              </span>
            @else
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-200/60">
                <i class="fas fa-comments text-[9px]"></i> Technical Advice
              </span>
            @endif

            <!-- Category Tag -->
            @if ($discussion?->category)
              <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                {{ $discussion->category->name }}
              </span>
            @endif

            <!-- Brand / Model Tags -->
            @if ($discussion?->brand || $discussion?->deviceModel)
              <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium text-[10px]">
                {{ $discussion->brand?->name }} {{ $discussion->deviceModel?->name }}
              </span>
            @endif

            <span class="text-slate-300 hidden sm:inline">·</span>

            <!-- Response Reference & Date -->
            <span class="font-mono text-slate-400 text-[11px] font-bold">#RESP-{{ $resp->id }}</span>
            <span class="text-slate-300">·</span>
            <span class="text-slate-400 font-medium text-[11px] flex items-center gap-1">
              <i class="far fa-clock text-[10px]"></i> Responded {{ $resp->created_at->diffForHumans() }}
            </span>
          </div>

          <!-- Right Status Badges (Request status & Offer status) -->
          <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Request Status Badge -->
            @if ($isReqOpen)
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[10px] border border-emerald-200/80">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>REQUEST OPEN</span>
              </span>
            @elseif ($isReqFulfilled)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[10px] border border-blue-200/80">
                <i class="fas fa-check-circle text-[9px]"></i>
                <span>REQUEST FULFILLED</span>
              </span>
            @elseif ($isReqClosed)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[10px] border border-slate-200">
                <i class="fas fa-lock text-[9px]"></i>
                <span>REQUEST CLOSED</span>
              </span>
            @endif

            <!-- Offer Status Badge -->
            @if ($isAccepted)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-black text-[10px] shadow-2xs">
                <i class="fas fa-check-circle text-[9px]"></i> ACCEPTED &amp; RESERVED
              </span>
            @elseif ($isCountered)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-black text-[10px] shadow-2xs">
                <i class="fas fa-sync text-[9px]"></i> COUNTER-OFFER
              </span>
            @elseif ($isPending)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-black text-[10px] shadow-2xs">
                <i class="fas fa-paper-plane text-[9px]"></i> OFFER SUBMITTED
              </span>
            @elseif ($isDeclined)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-black text-[10px] border border-rose-200">
                <i class="fas fa-times-circle text-[9px]"></i> OFFER DECLINED
              </span>
            @elseif ($isCancelled)
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-black text-[10px] border border-slate-200">
                OFFER CANCELLED
              </span>
            @else
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                COMMUNITY COMMENT
              </span>
            @endif

          </div>
        </div>

        <!-- 2. THE REQUEST PREVIEW (Truncated with ellipses) -->
        <div class="space-y-1.5 bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
          <div class="flex items-center justify-between text-xs text-slate-500 flex-wrap gap-2">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
              <i class="fas fa-bullhorn text-[10px]"></i> Community Request
            </span>
            <span class="text-[11px] font-medium text-slate-500">
              Requested by <strong class="text-slate-800 font-extrabold">{{ $buyerName }}</strong> · <i class="fas fa-map-marker-alt text-[10px] text-slate-400"></i> {{ $buyerLocation }}
            </span>
          </div>

          <!-- Request Title (links to public frontend request page) -->
          <div class="flex items-start justify-between gap-3 flex-wrap">
            <a
              href="{{ route('community.request', ['id' => $resp->discussion_id]) }}"
              class="group font-black text-slate-950 text-sm sm:text-base hover:text-pp-600 transition flex items-center gap-1.5"
              title="View public community request"
            >
              <span>{{ $discussion?->title ?? 'Community Request #' . $resp->discussion_id }}</span>
              <i class="fas fa-external-link-alt text-[11px] text-slate-400 group-hover:text-pp-600 transition"></i>
            </a>

            @if ($discussion?->budget)
              <span class="px-2.5 py-1 rounded-xl bg-slate-200/70 text-slate-800 font-black text-xs shrink-0">
                Budget: {{ $discussion->budget }}
              </span>
            @endif
          </div>

          <!-- Request Body (Truncated with ellipses) -->
          <p class="text-xs text-slate-600 leading-relaxed font-normal">
            {{ Str::limit($discussion?->body ?? 'No detailed specifications provided.', 160, '...') }}
          </p>
        </div>

        <!-- 3. SELLER'S RESPONSE (Full body, NOT truncated) -->
        <div class="space-y-2 bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
          <div class="flex items-center justify-between text-xs font-bold text-slate-500">
            <span class="flex items-center gap-1.5 text-pp-700 font-extrabold text-[11px] uppercase tracking-wider">
              <i class="fas fa-reply text-xs"></i> Your Submitted Response
            </span>
            <span class="text-[11px] text-slate-400 font-medium">
              {{ $resp->created_at->format('M d, Y · h:i A') }}
            </span>
          </div>

          <!-- Untruncated Response Text -->
          <p class="text-xs sm:text-sm text-slate-900 font-medium leading-relaxed whitespace-pre-line">
            {{ $resp->body }}
          </p>
        </div>

        <!-- 4. OFFERS SUMMARY & ACTION FOOTER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1 border-t border-slate-100">
          
          <!-- Offers Count & Commercial Terms -->
          <div class="flex items-center gap-3 flex-wrap text-xs">
            
            <!-- Number of offers involving buyer & seller on this discussion -->
            <div class="flex items-center gap-2">
              <span class="px-3 py-1.5 rounded-xl {{ $hasOffers ? 'bg-pp-100 text-pp-900 border border-pp-200' : 'bg-slate-100 text-slate-600' }} font-black text-xs flex items-center gap-1.5">
                <i class="fas fa-handshake text-xs {{ $hasOffers ? 'text-pp-700' : 'text-slate-400' }}"></i>
                <span>{{ $offersCount }} {{ Str::plural('Offer', $offersCount) }}</span>
                <span class="text-[10px] text-slate-500 font-normal hidden sm:inline">(Buyer &amp; Seller)</span>
              </span>
            </div>

            <!-- Quote & Terms (if an offer exists) -->
            @if ($hasOffers && $latestOffer)
              <div class="flex items-center gap-2 text-slate-700">
                <span class="text-slate-300">·</span>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Proposed Quote:</span>
                <span class="text-base font-black text-slate-950">₦{{ number_format($latestOffer->total()) }}</span>
                <span class="text-slate-300 hidden md:inline">·</span>
                <span class="text-slate-500 text-[11px] hidden md:inline">
                  {{ $latestOffer->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}
                  · {{ $latestOffer->maxWarrantyDays() ? "{$latestOffer->maxWarrantyDays()} Days Warranty" : 'Standard Warranty' }}
                </span>
              </div>
            @else
              <span class="text-slate-400 text-xs flex items-center gap-1">
                <i class="fas fa-info-circle text-[11px]"></i> No commercial offer attached to this response yet.
              </span>
            @endif

          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 self-end sm:self-auto shrink-0 flex-wrap">
            
            <!-- View Public Request Button (frontend only, since seller is not request author) -->
            <a
              href="{{ route('community.request', ['id' => $resp->discussion_id]) }}"
              class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5"
              title="View public request on community hub"
            >
              <i class="fas fa-globe text-[11px] text-slate-500"></i>
              <span>View Public Request</span>
            </a>

            <!-- Action: Inline edit (Allowed when no offer attached and discussion not closed) -->
            @if ($canEdit)
              <button
                type="button"
                wire:click="openEditModal({{ $resp->id }})"
                class="px-3.5 py-2.5 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs border border-pp-200 transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                title="Edit response text and optionally add commercial offer"
              >
                <i class="fas fa-edit text-xs"></i>
                <span>Edit Response</span>
              </button>
            @endif

            <!-- View Offers Button (Opens Offer Page) -->
            @if ($hasOffers && $latestOffer)
              <a
                href="{{ route('offers.view', ['offer_id' => 'OFF-' . $latestOffer->id]) }}"
                class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition flex items-center gap-2"
              >
                <i class="fas fa-comments text-xs"></i>
                <span>View Offers</span>
                <span class="px-1.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black">
                  {{ $offersCount }}
                </span>
                <i class="fas fa-arrow-right text-[10px]"></i>
              </a>
            @endif

          </div>

        </div>

      </div>

    @empty
      <!-- EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto shadow-2xs">
          <i class="fas fa-reply-all"></i>
        </div>
        <div class="space-y-1.5">
          <h3 class="font-black text-slate-950 text-lg">No Responses Found</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all' || $requestStatusFilter !== 'all' || $offerStatusFilter !== 'all')
              No community responses match your active filter criteria. Try clearing some filters or searching with different terms.
            @else
              You haven't responded to any community requests yet. Explore the community hub to supply spare parts, devices, or repair services!
            @endif
          </p>
        </div>
        <div class="pt-2 flex items-center justify-center gap-3">
          @if (!empty($search) || $typeFilter !== 'all' || $categoryFilter !== 'all' || $requestStatusFilter !== 'all' || $offerStatusFilter !== 'all')
            <button
              wire:click="resetFilters"
              class="py-2.5 px-5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition inline-flex items-center gap-2 cursor-pointer"
            >
              <i class="fas fa-rotate-left text-xs"></i>
              <span>Clear All Filters</span>
            </button>
          @endif
          <a
            href="{{ route('community') }}"
            class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition inline-flex items-center gap-2 shadow-xs"
          >
            <i class="fas fa-search text-xs"></i>
            <span>Browse Open Requests</span>
          </a>
        </div>
      </div>
    @endforelse
  </div>

  <!-- PAGINATION -->
  @if ($userResponses instanceof \Illuminate\Pagination\LengthAwarePaginator && $userResponses->hasPages())
    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-2xs">
      {{ $userResponses->links() }}
    </div>
  @endif

  <!-- EDIT RESPONSE & ATTACH OFFER MODAL -->
  @if ($showEditModal)
    <div
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
      x-data
      @keydown.escape.window="$wire.closeEditModal()"
    >
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <!-- MODAL HEADER -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-base sm:text-lg font-black text-slate-950">Edit Response</h3>
              <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 text-[10px] font-extrabold">
                #RESP-{{ $editingResponseId }}
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Modify your response message or attach an itemized commercial proposal.
            </p>
          </div>
          <button
            type="button"
            wire:click="closeEditModal"
            class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 grid place-items-center transition cursor-pointer shrink-0"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- MODAL BODY (SCROLLABLE) -->
        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-xs">
          
          <!-- Parent Request Info Banner -->
          @if ($editingResponse?->discussion)
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Target Request</span>
              <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $editingResponse->discussion->title }}</h4>
              @if ($editingResponse->discussion->budget)
                <span class="text-[11px] font-bold text-pp-700">Buyer Budget: {{ $editingResponse->discussion->budget }}</span>
              @endif
            </div>
          @endif

          <!-- Response Text Area -->
          <div class="space-y-1.5">
            <label class="font-extrabold text-slate-800 text-xs block">
              Response Details / Message <span class="text-rose-500">*</span>
            </label>
            <textarea
              wire:model="responseText"
              rows="4"
              class="w-full p-3.5 rounded-2xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600 focus:ring-2 focus:ring-pp-500/20 leading-relaxed transition"
              placeholder="Provide clear technical details regarding parts availability, testing, or installation..."
            ></textarea>
            @error('responseText')
              <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span>
            @enderror
          </div>

          <!-- TOGGLE ATTACH OFFER FORM -->
          <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-4">
            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 text-xs font-extrabold text-slate-900 cursor-pointer">
                <input
                  type="checkbox"
                  wire:click="toggleOfferComposer"
                  {{ $isOfferActive ? 'checked' : '' }}
                  class="rounded text-pp-600 focus:ring-pp-500 cursor-pointer"
                />
                <i class="fas fa-handshake text-pp-600"></i> Attach an Itemized Price Offer / Proposal
              </label>
              <span class="text-[10px] uppercase font-bold text-pp-700 bg-pp-100 px-2.5 py-0.5 rounded-full">
                Commercial Proposal
              </span>
            </div>

            @if ($isOfferActive)
              <div class="space-y-4 pt-3 border-t border-pp-200/60 text-xs">
                
                <!-- 1. PART / ITEM SOURCE -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                      <i class="fas fa-box text-pp-600"></i> Part or Hardware Item
                    </span>
                    <span class="text-[10px] text-slate-400">Select listing or enter description</span>
                  </div>

                  @if ($this->myListings->isNotEmpty())
                    <div>
                      <label class="text-[11px] font-bold text-slate-700 block mb-1">Select from Your Active Listings (Optional)</label>
                      <select wire:model.live="composerListingId" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                        <option value="">-- Custom Item / Enter Details Manually --</option>
                        @foreach ($this->myListings as $lst)
                          <option value="{{ $lst->id }}">{{ $lst->title }} (₦{{ number_format($lst->price) }})</option>
                        @endforeach
                      </select>
                    </div>
                  @endif

                  <div class="grid sm:grid-cols-3 gap-2.5">
                    <div class="sm:col-span-1">
                      <label class="text-[11px] font-bold text-slate-700 block mb-1">Item Title / Description</label>
                      <input type="text" wire:model="composerItemDescription" placeholder="e.g. Original Motherboard" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" />
                    </div>
                    <div>
                      <label class="text-[11px] font-bold text-slate-700 block mb-1">Item Price (₦)</label>
                      <input type="number" wire:model="composerItemPrice" placeholder="e.g. 75000" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                    </div>
                    <div>
                      <label class="text-[11px] font-bold text-slate-700 block mb-1">Item Warranty (Days)</label>
                      <input type="number" wire:model="composerItemWarranty" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" />
                    </div>
                  </div>
                </div>

                <!-- 2. SERVICE / LABOR LINE -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-3">
                  <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 font-bold text-slate-900 text-xs cursor-pointer">
                      <input type="checkbox" wire:model.live="composerIncludeService" class="rounded text-pp-600 focus:ring-pp-500 cursor-pointer" />
                      <i class="fas fa-wrench text-pp-600"></i> Include Repair / Installation Service
                    </label>
                    <span class="text-[10px] text-slate-400">Labor &amp; workmanship</span>
                  </div>

                  @if ($composerIncludeService)
                    <div class="grid sm:grid-cols-3 gap-2.5 pt-2 border-t border-slate-100">
                      <div class="sm:col-span-1">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Service Description</label>
                        <input type="text" wire:model="composerServiceDescription" placeholder="e.g. Installation & Testing" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" />
                      </div>
                      <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Service Fee (₦)</label>
                        <input type="number" wire:model="composerServicePrice" placeholder="e.g. 10000" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                      </div>
                      <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Workmanship Warranty (Days)</label>
                        <input type="number" wire:model="composerServiceWarranty" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" />
                      </div>
                    </div>
                  @endif
                </div>

                <!-- 3. DIRECTIONAL SHIPMENTS -->
                <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-3">
                  <span class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                    <i class="fas fa-truck-fast text-emerald-600"></i> Delivery &amp; Pickup Options
                  </span>

                  <div class="grid sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50 space-y-2">
                      <label class="flex items-center gap-2 font-bold text-slate-800 text-[11px] cursor-pointer">
                        <input type="checkbox" wire:model.live="composerIncludePickup" class="rounded text-pp-600 focus:ring-pp-500 cursor-pointer" />
                        <span>Pickup from Buyer (Customer device to Workshop)</span>
                      </label>
                      @if ($composerIncludePickup)
                        <div class="pt-1">
                          <label class="text-[10px] font-bold text-slate-600 block mb-0.5">Pickup Dispatch Fee (₦)</label>
                          <input type="number" wire:model="composerPickupFee" placeholder="e.g. 3500" class="w-full p-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                        </div>
                      @endif
                    </div>

                    <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50 space-y-2">
                      <label class="flex items-center gap-2 font-bold text-slate-800 text-[11px] cursor-pointer">
                        <input type="checkbox" wire:model.live="composerIncludeDelivery" class="rounded text-pp-600 focus:ring-pp-500 cursor-pointer" />
                        <span>Delivery to Buyer (Workshop to Customer)</span>
                      </label>
                      @if ($composerIncludeDelivery)
                        <div class="pt-1">
                          <label class="text-[10px] font-bold text-slate-600 block mb-0.5">Delivery Dispatch Fee (₦)</label>
                          <input type="number" wire:model="composerDeliveryFee" placeholder="e.g. 4000" class="w-full p-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                        </div>
                      @endif
                    </div>
                  </div>
                </div>

                <!-- 4. FULFILLMENT & CUSTOM NOTES -->
                <div class="grid sm:grid-cols-3 gap-3">
                  <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Fulfillment Method</label>
                    <select wire:model="composerOfferDelivery" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                      <option value="flexible">Flexible / Mutually Agreed</option>
                      <option value="seller_delivery">Seller Responsible Dispatch</option>
                      <option value="buyer_pickup">Buyer Pickup at Workshop</option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Custom Proposal Terms / Notes</label>
                    <input type="text" wire:model="composerOfferMessage" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" placeholder="e.g. Bench tested with 14-day replacement assurance." />
                  </div>
                </div>

              </div>
            @endif
          </div>

        </div>

        <!-- MODAL FOOTER -->
        <div class="p-4 sm:p-5 border-t border-slate-100 flex items-center justify-end gap-3 bg-slate-50/50">
          <button
            type="button"
            wire:click="closeEditModal"
            class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="saveEditedResponse"
            wire:loading.attr="disabled"
            class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <span wire:loading.remove wire:target="saveEditedResponse">
              <i class="fas fa-check text-xs mr-1"></i> Save Changes {{ $isOfferActive ? '& Submit Offer' : '' }}
            </span>
            <span wire:loading wire:target="saveEditedResponse" class="flex items-center gap-2">
              <i class="fas fa-spinner fa-spin text-xs"></i> Saving...
            </span>
          </button>
        </div>

      </div>
    </div>
  @endif

</div>