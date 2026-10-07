<div class="space-y-6">
  
  <!-- PAGE HEADER & TOP FILTER TABS (All, Sent, Received) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Offers &amp; Negotiations Inbox</h1>
      <p class="text-xs text-slate-500 mt-0.5">Review, negotiate, and respond to buyer package offers &amp; community hub proposals.</p>
    </div>

    <!-- TOP FILTER TABS (Only All, Sent, Received) -->
    <div class="flex items-center gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button
        type="button"
        wire:click="setDirection('all')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'all' ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>All</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['all'] }}
        </span>
      </button>

      <button
        type="button"
        wire:click="setDirection('sent')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'sent' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>Sent</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'sent' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['sent'] }}
        </span>
      </button>

      <button
        type="button"
        wire:click="setDirection('received')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'received' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>Received</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'received' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['received'] }}
        </span>
      </button>
    </div>
  </div>

  <!-- SECONDARY FILTER LINE (Accepted, Pending, Countered, Declined, Expired, Cancelled) -->
  <div class="bg-slate-50/80 p-2 rounded-2xl border border-slate-200/80 flex items-center gap-1.5 overflow-x-auto text-xs font-bold scrollbar-none">
    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 pl-2 pr-1 shrink-0 flex items-center gap-1">
      <i class="fas fa-filter text-[10px]"></i> Status:
    </span>

    <button
      type="button"
      wire:click="setStatus('all')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'all' ? 'bg-white text-slate-900 border border-slate-300 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
    >
      <span>All Statuses</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-700 font-bold">{{ $statusCounts['all'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('pending')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-2xs font-extrabold' : 'text-amber-800 hover:bg-amber-50' }}"
    >
      <span>Pending</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900' }} font-bold">{{ $statusCounts['pending'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('countered')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'countered' ? 'bg-purple-600 text-white shadow-2xs font-extrabold' : 'text-purple-800 hover:bg-purple-50' }}"
    >
      <span>Countered</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'countered' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-900' }} font-bold">{{ $statusCounts['countered'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('accepted')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'accepted' ? 'bg-emerald-600 text-white shadow-2xs font-extrabold' : 'text-emerald-800 hover:bg-emerald-50' }}"
    >
      <span>Accepted</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'accepted' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-900' }} font-bold">{{ $statusCounts['accepted'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('declined')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'declined' ? 'bg-rose-600 text-white shadow-2xs font-extrabold' : 'text-rose-800 hover:bg-rose-50' }}"
    >
      <span>Declined</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'declined' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-900' }} font-bold">{{ $statusCounts['declined'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('expired')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'expired' ? 'bg-slate-700 text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-200/60' }}"
    >
      <span>Expired</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'expired' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-bold">{{ $statusCounts['expired'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('cancelled')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'cancelled' ? 'bg-slate-700 text-white shadow-2xs font-extrabold' : 'text-slate-600 hover:bg-slate-200/60' }}"
    >
      <span>Cancelled</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'cancelled' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} font-bold">{{ $statusCounts['cancelled'] }}</span>
    </button>
  </div>

  <!-- SEARCH BAR -->
  <div class="flex items-center gap-3 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-2xs">
    <i class="fas fa-search text-slate-400 pl-2"></i>
    <input
      type="text"
      wire:model.live.debounce.300ms="searchQuery"
      placeholder="Search offers & negotiations by product name, party name, or notes..."
      class="w-full text-xs text-slate-800 outline-none placeholder:text-slate-400 bg-transparent"
    />
    @if (!empty($searchQuery))
      <button wire:click="$set('searchQuery', '')" class="text-slate-400 hover:text-slate-600 pr-2 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    @endif
  </div>

  <!-- FLASH MESSAGE WRAPPER -->
  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <!-- OFFERS & NEGOTIATIONS STREAM -->
  <div class="space-y-4">
    @forelse ($userOffers as $offer)
      @php
        $user = auth()->user();
        $isSender = $user && ($user->id === $offer->sender_id);
        $isRecipient = $user && ($user->id === $offer->recipient_id);
        $otherParty = $isSender ? $offer->recipient : $offer->sender;
        $partyRole = $isSender ? 'Recipient' : 'Sender';
        $location = $otherParty?->primaryLocation?->city ? "{$otherParty->primaryLocation->city}, {$otherParty->primaryLocation->state}" : 'Nigeria';
        $itemCount = $offer->items->count();
        $primaryItem = $offer->items->first();
        $isAccepted = ($offer->status === 'accepted');
        $isCountered = ($offer->status === 'countered');
        $isDeclined = ($offer->status === 'declined');
        $isExpired = ($offer->status === 'expired');
        $isCancelled = ($offer->status === 'cancelled');
        $actionNeeded = $isRecipient && ($offer->status === 'pending');

        // Check if offer is part of multi-round negotiation
        $isNegotiationChain = (bool) ($offer->parent_id || $offer->counterOffers->isNotEmpty());
        $rootId = $offer->parent_id ?: $offer->id;
      @endphp

      <div class="bg-white rounded-3xl p-6 space-y-4 shadow-soft transition {{ $actionNeeded ? 'border-2 border-pp-500 ring-2 ring-pp-100' : ($isAccepted ? 'border-2 border-emerald-400 bg-emerald-50/10' : 'border border-slate-200 hover:border-slate-300') }}">
        
        <!-- HEADER ROW -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            @if ($offer->cart_id)
              <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] flex items-center gap-1">
                <i class="fas fa-shopping-cart text-pp-600"></i> SOURCE: BUYER CART
              </span>
            @elseif ($offer->discussion_id)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] flex items-center gap-1">
                <i class="fas fa-users text-emerald-600"></i> SOURCE: COMMUNITY HUB
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 font-extrabold text-[10px]">
                DIRECT PROPOSAL
              </span>
            @endif

            @if ($isNegotiationChain)
              <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-extrabold text-[10px] flex items-center gap-1">
                <i class="fas fa-handshake text-purple-600"></i> NEGOTIATION THREAD
              </span>
            @endif

            <span class="text-slate-300">·</span>
            <span class="font-bold text-slate-900">
              {{ $partyRole }}: {{ $otherParty?->business_name ?: $otherParty?->name ?: 'Marketplace User' }} ({{ $location }})
            </span>
            <span class="text-slate-300">·</span>
            <span class="text-slate-400">{{ $offer->created_at->diffForHumans() }}</span>
          </div>

          <!-- STATUS BADGE -->
          <div>
            @if ($isAccepted)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[11px] flex items-center gap-1">
                <i class="fas fa-check-circle"></i> ACCEPTED &amp; RESERVED
              </span>
            @elseif ($isCountered)
              <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-extrabold text-[11px] flex items-center gap-1">
                <i class="fas fa-rotate"></i> COUNTERED
              </span>
            @elseif ($isDeclined)
              <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[11px] flex items-center gap-1">
                <i class="fas fa-times-circle"></i> DECLINED
              </span>
            @elseif ($isExpired)
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[11px]">
                EXPIRED
              </span>
            @elseif ($isCancelled)
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[11px]">
                CANCELLED
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full {{ $actionNeeded ? 'bg-amber-100 text-amber-900 animate-pulse border border-amber-300' : 'bg-slate-100 text-slate-700' }} font-extrabold text-[11px]">
                {{ $actionNeeded ? 'ACTION REQUIRED' : 'PENDING RESPONSE' }}
              </span>
            @endif
          </div>
        </div>

        <!-- BODY ROW -->
        <div class="grid sm:grid-cols-12 gap-4 items-center">
          <div class="sm:col-span-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-xl shrink-0">
              💻
            </div>
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="text-sm font-extrabold text-slate-900">
                  {{ $primaryItem?->description ?? 'Custom Proposal' }}
                </h4>
                @if ($itemCount > 1)
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">+{{ $itemCount - 1 }} more items</span>
                @endif
              </div>
              
              <p class="text-xs text-slate-500">
                Fulfillment: <strong>{{ $offer->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}</strong> · Warranty: <strong>{{ $offer->maxWarrantyDays() ? "{$offer->maxWarrantyDays()} Days" : 'Standard' }}</strong>
              </p>

              @if ($offer->terms)
                <p class="text-xs text-slate-600 italic">"{{ Str::limit($offer->terms, 90) }}"</p>
              @endif
            </div>
          </div>

          <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
            <span class="text-xs text-slate-400 block font-medium">Proposed Net Total:</span>
            <span class="text-2xl font-extrabold text-slate-950">₦{{ number_format($offer->total()) }}</span>
            @if ($offer->discount > 0)
              <span class="text-[10px] text-pp-700 font-bold block">Discounted (-₦{{ number_format($offer->discount) }})</span>
            @endif
          </div>

          <div class="sm:col-span-3 flex flex-col gap-2">
            <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $offer->id]) }}" class="w-full py-2.5 px-4 rounded-xl {{ $actionNeeded ? 'bg-pp-600 hover:bg-pp-700 text-white font-black' : 'bg-slate-900 hover:bg-slate-800 text-white font-extrabold' }} text-xs text-center shadow-xs transition block">
              View Negotiation &amp; Respond →
            </a>
            @if ($actionNeeded)
              <span class="text-[10px] text-amber-700 font-extrabold text-center">Your response is required</span>
            @endif
          </div>
        </div>

      </div>
    @empty
      <!-- EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto">
          <i class="fas fa-handshake"></i>
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-slate-900 text-base">No Offers or Negotiations Found</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            You don't have any offers in the <strong>{{ ucfirst($activeTab) }}</strong> category with status <strong>{{ ucfirst($statusFilter) }}</strong>.
          </p>
        </div>
        <div class="flex items-center justify-center gap-3 pt-2">
          @if ($statusFilter !== 'all' || $activeTab !== 'all' || !empty($searchQuery))
            <button
              type="button"
              wire:click="$set('statusFilter', 'all'); $set('activeTab', 'all'); $set('searchQuery', '')"
              class="py-2.5 px-5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition cursor-pointer"
            >
              Reset Filters
            </button>
          @endif
          <a href="{{ route('cart') }}" class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition">
            Go to Cart
          </a>
          <a href="{{ route('community') }}" class="py-2.5 px-5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
            Browse Community RFQs
          </a>
        </div>
      </div>
    @endforelse
  </div>

</div>