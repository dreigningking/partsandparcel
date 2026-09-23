<div class="flex flex-col gap-6">

  @php
    $discussion = $response?->discussion;
    $author = $discussion?->user?->business_name ?: $discussion?->user?->name ?: 'TechSam';
    $offer = $activeOffer;
    $offerTotal = $offer ? $offer->total() : 80000;
  @endphp

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('myresponses') }}" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1">
          <i class="fas fa-arrow-left text-[10px]"></i> Back to My Responses
        </a>
        <span class="text-slate-300">·</span>
        <span class="text-xs font-extrabold text-slate-500">Proposal #RESP-{{ $response?->id ?? '1092' }}</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-3 flex-wrap">
        <span>Proposal for Request #REQ-{{ $discussion?->id ?? '8402' }}</span>
        @if ($offer)
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $offer->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($offer->status === 'countered' ? 'bg-amber-100 text-amber-900' : 'bg-pp-100 text-pp-800') }}">
            OFFER STATUS: {{ strtoupper($offer->status) }}
          </span>
        @endif
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Community Request by {{ $author }}: "{{ $discussion?->title ?? 'Looking for HP EliteBook 840 G5 Motherboard' }}"
      </p>
    </div>

    @if ($offer)
      <div class="flex items-center gap-2">
        <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $offer->id]) }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-2">
          <i class="fas fa-comments"></i>
          <span>Open Full Negotiation Thread →</span>
        </a>
      </div>
    @endif
  </div>

  <!-- PROPOSAL & OFFER SUMMARY -->
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-soft">
    <div class="space-y-2 border-b border-slate-100 pb-4 text-xs">
      <span class="text-slate-400 font-bold uppercase text-[10px] block">Your Submitted Response Text</span>
      <p class="text-slate-800 text-sm font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
        "{{ $response?->body ?? 'Original motherboard, clean condition. Tested working.' }}"
      </p>
    </div>

    @if ($offer)
      <div class="grid sm:grid-cols-2 gap-6 border-b border-slate-100 pb-6 text-xs">
        <div class="space-y-1">
          <span class="text-slate-400 font-bold uppercase text-[10px]">Your Proposed Terms</span>
          <h3 class="text-2xl font-black text-slate-950">₦{{ number_format($offerTotal) }}</h3>
          <p class="text-slate-600">
            Warranty: {{ $offer->maxWarrantyDays() ? "{$offer->maxWarrantyDays()} Days" : 'Standard' }} · Fulfillment: {{ $offer->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup' }}
          </p>
        </div>

        <div class="space-y-1 text-left sm:text-right">
          <span class="text-slate-400 font-bold uppercase text-[10px]">Negotiation Status</span>
          <h3 class="text-xl font-bold {{ $offer->status === 'accepted' ? 'text-emerald-600' : ($offer->status === 'countered' ? 'text-amber-700' : 'text-slate-800') }}">
            {{ ucfirst($offer->status) }}
          </h3>
          <p class="text-slate-500">Last activity: {{ $offer->updated_at->diffForHumans() }}</p>
        </div>
      </div>

      <div class="flex items-center justify-between flex-wrap gap-4 text-xs">
        <span class="text-slate-500">
          <i class="fas fa-info-circle text-pp-600 mr-1"></i> Private negotiation visible only to you and {{ $author }}.
        </span>
        <a href="{{ route('offers.view', ['offer_id' => 'OFF-' . $offer->id]) }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition">
          View Details &amp; Counter-Offer →
        </a>
      </div>
    @else
      <div class="text-xs text-slate-500">
        This was submitted as a general community response without an attached commercial offer.
      </div>
    @endif
  </div>

</div>