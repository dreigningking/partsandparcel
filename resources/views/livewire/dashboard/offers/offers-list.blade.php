<div class="space-y-6">
  
  <!-- PAGE HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">Offers &amp; Negotiations Inbox</h1>
      <p class="text-xs text-slate-500 mt-0.5">Review, negotiate, and respond to buyer package offers &amp; community hub proposals.</p>
    </div>

    <!-- FILTER TABS -->
    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs flex-wrap">
      <button wire:click="$set('activeTab', 'received')" class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $activeTab === 'received' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Received ({{ $counts['received'] }})
      </button>
      <button wire:click="$set('activeTab', 'sent')" class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $activeTab === 'sent' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Sent ({{ $counts['sent'] }})
      </button>
      <button wire:click="$set('activeTab', 'accepted')" class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $activeTab === 'accepted' ? 'bg-emerald-600 text-white' : 'text-emerald-700 hover:bg-emerald-50' }}">
        Accepted ({{ $counts['accepted'] }})
      </button>
      <button wire:click="$set('activeTab', 'declined')" class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $activeTab === 'declined' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Declined ({{ $counts['declined'] }})
      </button>
      <button wire:click="$set('activeTab', 'all')" class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $activeTab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        All ({{ $counts['all'] }})
      </button>
    </div>
  </div>

  <!-- SEARCH BAR -->
  <div class="flex items-center gap-3 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-2xs">
    <i class="fas fa-search text-slate-400 pl-2"></i>
    <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Search offers by item name, terms, or proposal notes..." class="w-full text-xs text-slate-800 outline-none placeholder:text-slate-400" />
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

  <!-- OFFERS LIST STREAM -->
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
        $actionNeeded = $isRecipient && ($offer->status === 'pending');
      @endphp

      <div class="bg-white rounded-3xl p-6 space-y-4 shadow-soft transition {{ $actionNeeded ? 'border-2 border-pp-500' : ($isAccepted ? 'border-2 border-emerald-400 bg-emerald-50/20' : 'border border-slate-200') }}">
        
        <!-- HEADER ROW -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            @if ($offer->cart_id)
              <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px] flex items-center gap-1">
                <i class="fas fa-shopping-cart text-pp-600"></i> SOURCE: BUYER CART PACKAGE
              </span>
            @elseif ($offer->discussion_id)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] flex items-center gap-1">
                <i class="fas fa-users text-emerald-600"></i> SOURCE: COMMUNITY HUB REQUEST
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 font-extrabold text-[10px]">
                DIRECT NEGOTIATION
              </span>
            @endif

            <span class="text-slate-300">·</span>
            <span class="font-bold text-slate-900">{{ $partyRole }}: {{ $otherParty?->business_name ?: $otherParty?->name ?: 'Marketplace User' }} ({{ $location }})</span>
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
              <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[11px]">
                COUNTERED
              </span>
            @elseif ($isDeclined)
              <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[11px]">
                DECLINED
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full {{ $actionNeeded ? 'bg-amber-100 text-amber-900 animate-pulse' : 'bg-slate-100 text-slate-700' }} font-extrabold text-[11px]">
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
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">+{{ $itemCount - 1 }} more items</span>
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
            <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $offer->id]) }}" class="w-full py-2.5 px-4 rounded-xl {{ $actionNeeded ? 'bg-pp-600 hover:bg-pp-700 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white' }} font-extrabold text-xs text-center shadow-xs transition block">
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
          <h3 class="font-extrabold text-slate-900 text-base">No Offers Found in this Tab</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            You don't have any offers in the "{{ ucfirst($activeTab) }}" category yet. Make custom package offers from your cart or respond to requests in the community hub!
          </p>
        </div>
        <div class="flex items-center justify-center gap-3 pt-2">
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