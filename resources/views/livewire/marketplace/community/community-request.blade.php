<div class="flex flex-col gap-6">

  <!-- TOP BREADCRUMB & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1 text-xs text-slate-500 font-medium">
        <a href="/" class="hover:text-pp-600 transition">Community Hub</a>
        <span>/</span>
        <a href="#" class="hover:text-pp-600 transition">Motherboards</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Request #REQ-8402</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950">Looking for tested HP EliteBook 840 G5 Motherboard (Core i5 8th Gen)</h1>
      <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap">
        <span><i class="fas fa-user text-pp-600 mr-1"></i> Posted by <strong>TechSam</strong></span>
        <span>·</span>
        <span><i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> Ikeja, Lagos</span>
        <span>·</span>
        <span><i class="fas fa-clock text-slate-400 mr-1"></i> 2 hours ago</span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center gap-1.5">
        <i class="fas fa-circle text-[8px] text-emerald-600"></i> Open for Proposals
      </span>
    </div>
  </div>

  <div class="grid lg:grid-cols-12 gap-8">
    
    <!-- LEFT: MAIN REQUEST DETAILS & COMMUNITY RESPONSES -->
    <div class="lg:col-span-8 space-y-6">

      <!-- REQUEST DESCRIPTION CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-file-alt text-pp-600"></i> Detailed Request Specifications
        </h3>

        <div class="text-sm text-slate-700 leading-relaxed space-y-3">
          <p>Hi everyone! My HP EliteBook 840 G5 motherboard shorted after a power surge. I am looking for a working original replacement motherboard (Core i5 8th Generation, non-vPro preferred).</p>
          <p>Must be clean with no prior board-level repairs or jumpers. I am located at Computer Village Ikeja and willing to come test at your shop before payment.</p>
        </div>

        <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 font-bold block uppercase text-[10px]">Target Budget</span>
            <span class="text-base font-extrabold text-pp-700">₦70,000 - ₦90,000</span>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 font-bold block uppercase text-[10px]">Fulfillment Preference</span>
            <span class="text-sm font-bold text-slate-800">Self-Pickup in Ikeja</span>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 font-bold block uppercase text-[10px]">Condition Desired</span>
            <span class="text-sm font-bold text-slate-800">Clean Pull (Tested)</span>
          </div>
        </div>
      </div>

      <!-- COMMUNITY RESPONSES SECTION (PUBLIC VISIBILITY) -->
      <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-comments text-pp-600"></i> Community Responses ({{ count($responses) }})
          </h3>
          <span class="text-xs text-slate-500 font-medium">Public discussions stream</span>
        </div>

        <!-- RESPONSES STREAM -->
        <div class="space-y-4">
          @foreach ($responses as $resp)
            <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
              
              <!-- RESPONSE HEADER -->
              <div class="flex items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-extrabold text-slate-900 flex items-center gap-1.5 text-sm">
                    <i class="fas fa-user-circle text-pp-600"></i> {{ $resp['author'] }}
                  </span>
                  @if (!empty($resp['verified']))
                    <span class="px-2 py-0.5 rounded-full bg-pp-50 text-pp-700 font-bold text-[10px] border border-pp-100">✓ Verified</span>
                  @endif
                  <span class="text-slate-300">·</span>
                  <span class="text-slate-500">{{ $resp['location'] }}</span>
                </div>
                <span class="text-slate-400 text-xs">{{ $resp['time'] }}</span>
              </div>

              <!-- PUBLIC RESPONSE TEXT -->
              <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                {{ $resp['text'] }}
              </p>

              <!-- PRIVATE OFFER CARD (ONLY VISIBLE TO REQUEST OWNER) -->
              @if ($isOwner && !empty($resp['negotiation']))
                @php
                  $latestRound = end($resp['negotiation']);
                @endphp
                <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-handshake text-pp-600 text-base"></i>
                    <div>
                      <span class="font-bold text-slate-900 block">Private Offer Proposal: <strong class="text-pp-700 text-sm">{{ $latestRound['price'] }}</strong></span>
                      <span class="text-[11px] text-slate-500">{{ $latestRound['warranty'] }} · {{ $latestRound['delivery'] }}</span>
                    </div>
                  </div>

                  <!-- VIEW OFFERS BUTTON: DESKTOP DRAWER VS MOBILE ROUTE -->
                  <button wire:click="openOffer({{ $resp['id'] }})" onclick="if (window.innerWidth < 768) { window.location.href = '{{ route('offers.view', ['id' => 'OFF-9021']) }}'; }" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-2xs transition cursor-pointer text-center shrink-0">
                    View Offers ({{ count($resp['negotiation']) }})
                  </button>
                </div>
              @endif

              <!-- RESPONSE FOOTER ACTIONS -->
              <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                <button wire:click="openVendorChat('{{ $resp['author'] }}')" class="px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition flex items-center gap-1.5 cursor-pointer">
                  <i class="fas fa-envelope text-pp-600"></i> Message Vendor
                </button>

                <button class="text-slate-400 hover:text-rose-600 transition flex items-center gap-1 text-[11px]">
                  <i class="fas fa-flag"></i> Report
                </button>
              </div>

            </div>
          @endforeach
        </div>

      </div>

    </div>

    <!-- RIGHT: SIDEBAR WIDGETS -->
    <div class="lg:col-span-4 space-y-6">
      
      <!-- SIDEBAR WIDGET 1: REQUEST STATUS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-info-circle text-pp-600"></i> Request Status
        </h3>

        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between items-center text-slate-600">
            <span>Status</span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] uppercase border border-emerald-100">Open</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Activity</span>
            <span class="font-bold text-slate-800">5 Responses · 48 Views</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Posted</span>
            <span class="font-semibold text-slate-700">2 hours ago</span>
          </div>
        </div>
      </div>

      <!-- SIDEBAR WIDGET 2: REQUESTER PROFILE -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-user text-pp-600"></i> Requester Profile
        </h3>

        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-pp-100 text-pp-700 font-extrabold text-sm flex items-center justify-center shrink-0">TS</div>
          <div>
            <div class="text-xs font-bold text-slate-900">TechSam <span class="text-[10px] text-pp-600 font-semibold">✓ Verified</span></div>
            <div class="text-[11px] text-slate-500 mt-0.5"><i class="fas fa-map-marker-alt text-[10px]"></i> Computer Village, Ikeja</div>
          </div>
        </div>

        <button wire:click="openVendorChat('TechSam')" class="w-full py-2.5 px-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5">
          <i class="fas fa-envelope"></i> Message Requester
        </button>
      </div>

    </div>

  </div>

</div>