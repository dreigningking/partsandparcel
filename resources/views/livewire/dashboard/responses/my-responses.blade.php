<div class="flex flex-col gap-6">

  <!-- HEADER & TABS -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-950">My Community Hub Responses</h1>
      <p class="text-xs text-slate-500 mt-0.5">Manage your submitted proposals and negotiated offers for buyer community requests.</p>
    </div>

    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold shadow-2xs flex-wrap">
      <button wire:click="$set('activeTab', 'all')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        All ({{ $counts['all'] }})
      </button>
      <button wire:click="$set('activeTab', 'active')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'active' ? 'bg-pp-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Active Proposals ({{ $counts['active'] }})
      </button>
      <button wire:click="$set('activeTab', 'accepted')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'accepted' ? 'bg-emerald-600 text-white' : 'text-emerald-700 hover:bg-emerald-50' }}">
        Accepted ({{ $counts['accepted'] }})
      </button>
      <button wire:click="$set('activeTab', 'declined')" class="px-3 py-1.5 rounded-lg cursor-pointer transition {{ $activeTab === 'declined' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
        Declined ({{ $counts['declined'] }})
      </button>
    </div>
  </div>

  <!-- RESPONSES STREAM -->
  <div class="space-y-4">
    @forelse ($userResponses as $resp)
      @php
        $latestOffer = $resp->offers->last();
        $isAccepted = $latestOffer && ($latestOffer->status === 'accepted');
        $isCountered = $latestOffer && ($latestOffer->status === 'countered');
        $hasOffer = (bool) $latestOffer;
        $author = $resp->discussion?->user?->business_name ?: $resp->discussion?->user?->name ?: 'Requester';
      @endphp

      <div class="bg-white rounded-3xl border-2 {{ $hasOffer ? ($isAccepted ? 'border-emerald-400 bg-emerald-50/20' : 'border-pp-500') : 'border-slate-200' }} p-6 space-y-4 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">
              RESPONSE #RESP-{{ $resp->id }}
            </span>
            <span class="text-slate-300">·</span>
            <span class="font-bold text-slate-900">Requester: {{ $author }}</span>
            <span class="text-slate-300">·</span>
            <span class="text-slate-400">Responded {{ $resp->created_at->diffForHumans() }}</span>
          </div>

          <div>
            @if ($isAccepted)
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[11px]">
                <i class="fas fa-check-circle"></i> ACCEPTED &amp; RESERVED
              </span>
            @elseif ($isCountered)
              <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[11px]">
                COUNTER-OFFER IN PROGRESS
              </span>
            @elseif ($hasOffer)
              <span class="px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[11px]">
                OFFER SUBMITTED ({{ strtoupper($latestOffer->status) }})
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]">
                COMMUNITY ADVICE / COMMENT
              </span>
            @endif
          </div>
        </div>

        <div class="grid sm:grid-cols-12 gap-4 items-center">
          <div class="sm:col-span-6 space-y-1">
            <h3 class="text-base font-extrabold text-slate-900">
              {{ $resp->discussion?->title ?? 'Community Request' }}
            </h3>
            <p class="text-xs text-slate-600 italic">"{{ Str::limit($resp->body, 120) }}"</p>
            @if ($hasOffer)
              <div class="text-xs text-slate-500 pt-1">
                Fulfillment: <strong>{{ $latestOffer->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}</strong> · Warranty: <strong>{{ $latestOffer->maxWarrantyDays() ? "{$latestOffer->maxWarrantyDays()} Days" : 'Standard' }}</strong>
              </div>
            @endif
          </div>

          <div class="sm:col-span-3 text-left sm:text-right space-y-0.5">
            @if ($hasOffer)
              <span class="text-xs text-slate-400 font-bold block uppercase">Proposed Quote:</span>
              <span class="text-2xl font-black text-slate-950">₦{{ number_format($latestOffer->total()) }}</span>
              <span class="text-[10px] text-pp-700 font-bold block">Status: {{ ucfirst($latestOffer->status) }}</span>
            @else
              <span class="text-xs text-slate-400 font-bold block uppercase">Response Type</span>
              <span class="text-sm font-bold text-slate-700">Community Comment</span>
            @endif
          </div>

          <div class="sm:col-span-3 flex flex-col gap-2">
            @if ($hasOffer)
              <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $latestOffer->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs text-center shadow-xs transition block">
                View Negotiation &amp; Thread →
              </a>
            @else
              <a href="{{ route('community.request', ['id' => $resp->discussion_id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs text-center shadow-xs transition block">
                View Request Page →
              </a>
            @endif
          </div>
        </div>
      </div>
    @empty
      <!-- EMPTY STATE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-soft">
        <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto">
          <i class="fas fa-reply-all"></i>
        </div>
        <div class="space-y-1">
          <h3 class="font-extrabold text-slate-900 text-base">No Responses Found</h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto">
            You haven't responded to any community requests in this tab yet. Explore community requests to supply spare parts, devices, or repair services!
          </p>
        </div>
        <div class="pt-2">
          <a href="{{ route('community') }}" class="py-2.5 px-5 rounded-xl bg-pp-600 text-white font-bold text-xs hover:bg-pp-700 transition inline-flex items-center gap-2">
            <i class="fas fa-search"></i>
            <span>Browse Open Requests</span>
          </a>
        </div>
      </div>
    @endforelse
  </div>

</div>