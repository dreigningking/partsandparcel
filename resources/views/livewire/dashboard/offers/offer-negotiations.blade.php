<div class="space-y-6">
  
  <!-- HEADER & DIRECTION FILTER (All, Sent, Received) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('offers') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Offers Inbox
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-purple-700">Negotiation Sessions</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950">Active Deal Negotiations</h1>
      <p class="text-xs text-slate-500 mt-0.5">Track multi-turn bargaining sessions, compare rounds, and review counter-offers.</p>
    </div>

    <!-- TOP FILTER TABS (Only All, Sent, Received) -->
    <div class="flex items-center gap-1.5 bg-white p-1.5 rounded-2xl border border-slate-200 text-xs font-bold shadow-2xs">
      <button
        type="button"
        wire:click="setDirection('all')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'all' ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>All Sessions</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['all'] }}
        </span>
      </button>

      <button
        type="button"
        wire:click="setDirection('sent')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'sent' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>Initiated by Me</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'sent' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['sent'] }}
        </span>
      </button>

      <button
        type="button"
        wire:click="setDirection('received')"
        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'received' ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}"
      >
        <span>Proposed to Me</span>
        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'received' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }} font-black">
          {{ $directionCounts['received'] }}
        </span>
      </button>
    </div>
  </div>

  <!-- SECONDARY FILTER LINE (Deal Statuses) -->
  <div class="bg-slate-50/80 p-2 rounded-2xl border border-slate-200/80 flex items-center gap-1.5 overflow-x-auto text-xs font-bold scrollbar-none">
    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 pl-2 pr-1 shrink-0 flex items-center gap-1">
      <i class="fas fa-filter text-[10px]"></i> Session State:
    </span>

    <button
      type="button"
      wire:click="setStatus('all')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'all' ? 'bg-white text-slate-900 border border-slate-300 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
    >
      <span>All Sessions</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-700 font-bold">{{ $statusCounts['all'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('active')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'active' ? 'bg-amber-500 text-white shadow-2xs font-extrabold' : 'text-amber-800 hover:bg-amber-50' }}"
    >
      <span>Active Bargaining</span>
      <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $statusFilter === 'active' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900' }} font-bold">{{ $statusCounts['active'] }}</span>
    </button>

    <button
      type="button"
      wire:click="setStatus('accepted')"
      class="px-3 py-1.5 rounded-xl transition cursor-pointer shrink-0 flex items-center gap-1.5 {{ $statusFilter === 'accepted' ? 'bg-emerald-600 text-white shadow-2xs font-extrabold' : 'text-emerald-800 hover:bg-emerald-50' }}"
    >
      <span>Accepted &amp; Closed</span>
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
  </div>

  <!-- SEARCH BAR -->
  <div class="flex items-center gap-3 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-2xs">
    <i class="fas fa-search text-slate-400 pl-2"></i>
    <input
      type="text"
      wire:model.live.debounce.300ms="searchQuery"
      placeholder="Search negotiation threads by counterparty name or item description..."
      class="w-full text-xs text-slate-800 outline-none placeholder:text-slate-400 bg-transparent"
    />
    @if (!empty($searchQuery))
      <button wire:click="$set('searchQuery', '')" class="text-slate-400 hover:text-slate-600 pr-2 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    @endif
  </div>

  <!-- NEGOTIATION THREADS LIST -->
  <div class="space-y-4">
    @forelse ($negotiationThreads as $thread)
      @php
        $latest = $thread->latest_offer;
        $isAccepted = ($thread->thread_status === 'accepted');
        $isDeclined = ($thread->thread_status === 'declined');
        $actionNeeded = $thread->action_required;
      @endphp

      <div class="bg-white rounded-3xl p-6 space-y-4 shadow-soft transition {{ $actionNeeded ? 'border-2 border-pp-500 ring-2 ring-pp-100' : ($isAccepted ? 'border-2 border-emerald-400 bg-emerald-50/10' : 'border border-slate-200 hover:border-slate-300') }}">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3 text-xs">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-extrabold text-[10px] flex items-center gap-1">
              <i class="fas fa-handshake"></i> SESSION #{{ $thread->root_id }} · {{ $thread->rounds_count }} {{ Str::plural('Round', $thread->rounds_count) }}
            </span>

            <span class="text-slate-300">·</span>
            <span class="font-bold text-slate-900">
              Counterparty: {{ $thread->other_party?->business_name ?: $thread->other_party?->name ?: 'Marketplace User' }}
            </span>
            <span class="text-slate-300">·</span>
            <span class="text-slate-400">Last activity {{ $thread->updated_at->diffForHumans() }}</span>
          </div>

          <div>
            @if ($isAccepted)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[11px] flex items-center gap-1">
                <i class="fas fa-check-circle"></i> DEAL ACCEPTED
              </span>
            @elseif ($isDeclined)
              <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[11px]">
                NEGOTIATION CLOSED
              </span>
            @elseif ($actionNeeded)
              <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-[11px] animate-pulse">
                YOUR TURN TO RESPOND
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">
                AWAITING COUNTERPARTY
              </span>
            @endif
          </div>
        </div>

        <div class="grid sm:grid-cols-12 gap-4 items-center">
          <div class="sm:col-span-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 grid place-items-center text-xl shrink-0">
              <i class="fas fa-comments"></i>
            </div>
            <div class="space-y-1">
              <h4 class="text-sm font-extrabold text-slate-900">
                {{ $thread->primary_item?->description ?? 'Custom Proposal Session' }}
              </h4>
              <p class="text-xs text-slate-500">
                Delivery: <strong>{{ $latest->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}</strong> · Warranty: <strong>{{ $latest->maxWarrantyDays() ? "{$latest->maxWarrantyDays()} Days" : 'Standard' }}</strong>
              </p>
              @if ($latest->terms)
                <p class="text-xs text-slate-600 italic">Latest Note: "{{ Str::limit($latest->terms, 85) }}"</p>
              @endif
            </div>
          </div>

          <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
            <span class="text-xs text-slate-400 block font-medium">Latest Proposal:</span>
            <span class="text-2xl font-extrabold text-slate-950">₦{{ number_format($thread->latest_total) }}</span>
            @if ($thread->original_total != $thread->latest_total)
              <span class="text-[10px] text-slate-500 font-bold block">
                Started at: ₦{{ number_format($thread->original_total) }}
              </span>
            @endif
          </div>

          <div class="sm:col-span-3 flex flex-col gap-2">
            <a
              href="{{ route('offers.view', ['offer_id' => 'OFF-' . $latest->id]) }}"
              class="w-full py-2.5 px-4 rounded-xl {{ $actionNeeded ? 'bg-pp-600 hover:bg-pp-700 text-white font-black' : 'bg-slate-900 hover:bg-slate-800 text-white font-extrabold' }} text-xs text-center shadow-xs transition block"
            >
              Enter Negotiation Room →
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-600 grid place-items-center text-2xl mx-auto">
          <i class="fas fa-handshake"></i>
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-slate-900 text-base">No Negotiation Sessions Found</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            You don't have any negotiations in this view. As soon as you make or receive an offer with counter-offers, your sessions will appear here!
          </p>
        </div>
        <div class="flex items-center justify-center gap-3 pt-2">
          <a href="{{ route('offers') }}" class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition">
            View All Offers
          </a>
        </div>
      </div>
    @endforelse
  </div>

</div>
