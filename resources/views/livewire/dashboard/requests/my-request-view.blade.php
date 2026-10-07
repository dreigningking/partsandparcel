<div class="space-y-6">

  <!-- TOP NAV & HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1.5 flex-wrap">
        <a href="{{ route('myrequests') }}" class="text-xs font-bold text-pp-600 hover:text-pp-700 flex items-center gap-1.5 transition">
          <i class="fas fa-arrow-left text-[10px]"></i>
          <span>Back to My Requests</span>
        </a>
        <span class="text-slate-300">·</span>
        <span class="font-mono text-xs font-black text-slate-500">Ref: #REQ-{{ $discussion?->id ?? '8402' }}</span>
        <span class="text-slate-300">·</span>
        <span class="text-xs text-slate-400 font-medium">Posted {{ $postedTime }}</span>
      </div>

      <h1 class="text-2xl sm:text-3xl font-black text-slate-950 flex items-center gap-3 flex-wrap tracking-tight">
        <span>{{ $title }}</span>
        
        @if ($status === 'open')
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[11px] border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>OPEN FOR PROPOSALS</span>
          </span>
        @elseif (in_array($status, ['resolved', 'fulfilled']))
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[11px] border border-blue-200">
            <i class="fas fa-check-circle text-[11px]"></i>
            <span>FULFILLED</span>
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[11px] border border-slate-200">
            <i class="fas fa-lock text-[10px]"></i>
            <span>CLOSED</span>
          </span>
        @endif
      </h1>
    </div>

    <!-- HEADER ACTION BUTTONS -->
    <div class="flex items-center gap-2 flex-wrap">
      @if ($status === 'open')
        <button
          type="button"
          wire:click="markFulfilled"
          class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-800 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
        >
          <i class="fas fa-check text-[10px]"></i>
          <span>Mark Fulfilled</span>
        </button>

        <button
          type="button"
          wire:click="closeRequest"
          class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
        >
          <i class="fas fa-lock text-[10px]"></i>
          <span>Close Request</span>
        </button>
      @else
        <button
          type="button"
          wire:click="reopenRequest"
          class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
        >
          <i class="fas fa-rotate-left text-[10px]"></i>
          <span>Reopen Request</span>
        </button>
      @endif

      <a
        href="{{ route('community.request', ['id' => $discussion?->id ?? 1]) }}"
        target="_blank"
        class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition flex items-center gap-1.5"
      >
        <span>View Public Hub Page</span>
        <i class="fas fa-external-link-alt text-[10px]"></i>
      </a>
    </div>
  </div>

  <!-- FLASH NOTIFICATIONS -->
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

  @if (session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- TWO COLUMN LAYOUT -->
  <div class="grid lg:grid-cols-12 gap-6 items-start">
    
    <!-- LEFT MAIN COLUMN: Request Details + Offers Desk + Responses -->
    <div class="lg:col-span-8 space-y-6">

      <!-- REQUEST SPECIFICATIONS CARD -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-6 space-y-4 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-file-lines text-pp-600"></i>
            <span>Request Details &amp; Specifications</span>
          </h2>
          <span class="text-xs text-slate-400 font-bold">Category: {{ $categoryName }}</span>
        </div>

        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-normal whitespace-pre-line">
          {{ $body }}
        </p>

        <!-- Parameter Chips -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Target Budget</span>
            <span class="text-xs font-black text-pp-700">{{ $budget }}</span>
          </div>

          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Location</span>
            <span class="text-xs font-bold text-slate-800">{{ $locationText }}</span>
          </div>

          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Device / Model</span>
            <span class="text-xs font-bold text-slate-800">{{ $deviceInfo ?: 'Standard Spec' }}</span>
          </div>

          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Fulfillment</span>
            <span class="text-xs font-bold text-slate-800">{{ ucfirst($fulfillment) }}</span>
          </div>
        </div>

        <!-- Attached Media Gallery (if any) -->
        @if (!empty($mediaUrls))
          <div class="pt-3 border-t border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
              <i class="fas fa-camera text-slate-400"></i>
              <span>Attached Reference Photos ({{ count($mediaUrls) }})</span>
            </h3>
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
              @foreach ($mediaUrls as $url)
                <a href="{{ $url }}" target="_blank" class="shrink-0 group">
                  <img
                    src="{{ $url }}"
                    alt="Exhibit photo"
                    class="w-24 h-24 rounded-2xl object-cover border border-slate-200 group-hover:border-pp-500 transition shadow-2xs"
                  />
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <!-- RECEIVED PRIVATE VENDOR OFFERS DESK -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-6 space-y-5 shadow-soft">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
          <div>
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-handshake text-emerald-600"></i>
              <span>Received Vendor Quotes ({{ count($offers) }} {{ Str::plural('Offer', count($offers)) }})</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Review verified seller quotes, inspect terms, or accept an offer to generate an escrow-secured invoice.</p>
          </div>
          <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-extrabold text-[11px] border border-emerald-200 shrink-0">
            100% Escrow Protected
          </span>
        </div>

        <div class="space-y-4">
          @forelse ($offers as $off)
            @php
              $isPending = ($off['status'] === 'pending');
              $isAccepted = ($off['status'] === 'accepted');
              $isCountered = ($off['status'] === 'countered');
              $isDeclined = ($off['status'] === 'declined');
            @endphp

            <div class="bg-slate-50/70 hover:bg-slate-50 border rounded-2xl p-5 space-y-3.5 transition {{ $isAccepted ? 'border-emerald-300 ring-2 ring-emerald-100 bg-emerald-50/20' : ($isPending ? 'border-slate-200/90 hover:border-slate-300' : 'border-slate-200 opacity-80') }}">
              
              <!-- Vendor & Price Header -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-pp-100 text-pp-700 font-black grid place-items-center text-sm shadow-2xs shrink-0">
                    {{ strtoupper(substr($off['vendor_name'], 0, 1)) }}
                  </div>
                  <div>
                    <div class="flex items-center gap-1.5">
                      <span class="font-extrabold text-sm text-slate-900">{{ $off['vendor_name'] }}</span>
                      @if ($off['vendor_verified'] ?? false)
                        <i class="fas fa-check-circle text-sky-500 text-xs" title="Verified Vendor"></i>
                      @endif
                    </div>
                    <span class="text-[11px] text-slate-500 flex items-center gap-1">
                      <i class="fas fa-map-marker-alt text-[10px] text-slate-400"></i>
                      <span>{{ $off['vendor_city'] }}</span>
                      <span class="text-slate-300">·</span>
                      <span>{{ $off['time'] }}</span>
                    </span>
                  </div>
                </div>

                <div class="text-left sm:text-right">
                  <span class="text-xl font-black text-slate-950 block">₦{{ number_format($off['price'], 2) }}</span>
                  <div class="flex items-center sm:justify-end gap-1.5 text-[10px] font-extrabold">
                    <span class="px-2 py-0.5 rounded-full {{ $isAccepted ? 'bg-emerald-600 text-white' : ($isPending ? 'bg-amber-100 text-amber-900' : ($isCountered ? 'bg-purple-100 text-purple-900' : 'bg-slate-200 text-slate-700')) }} uppercase">
                      {{ $off['status'] }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Offer Item Details -->
              @if (!empty($off['items']))
                <div class="bg-white rounded-xl border border-slate-200/80 p-3 space-y-1.5 text-xs">
                  @foreach ($off['items'] as $item)
                    <div class="flex items-center justify-between gap-2 text-slate-700">
                      <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-pp-500"></span>
                        <span class="font-bold text-slate-900">{{ $item['description'] }}</span>
                        @if ($item['quantity'] > 1)
                          <span class="text-slate-400 font-bold">(x{{ $item['quantity'] }})</span>
                        @endif
                      </div>
                      <span class="font-extrabold text-slate-900">₦{{ number_format($item['price'], 2) }}</span>
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Guarantee & Fulfillment Chips -->
              <div class="flex items-center gap-2 flex-wrap text-[11px]">
                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold border border-emerald-100 flex items-center gap-1">
                  <i class="fas fa-shield-alt text-emerald-600 text-[10px]"></i>
                  <span>Warranty: {{ $off['warranty'] }}</span>
                </span>

                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200/70 flex items-center gap-1">
                  <i class="fas fa-truck text-slate-500 text-[10px]"></i>
                  <span>{{ $off['delivery'] }}</span>
                </span>

                @if (!empty($off['terms']))
                  <p class="text-[11px] text-slate-500 italic flex-1 min-w-[200px]">
                    "{{ $off['terms'] }}"
                  </p>
                @endif
              </div>

              <!-- Offer Action Buttons -->
              <div class="border-t border-slate-200/80 pt-3 flex flex-wrap items-center justify-between gap-2">
                <span class="font-mono text-[10px] text-slate-400 font-bold">#OFF-{{ $off['id'] }}</span>

                <div class="flex items-center gap-2">
                  <a
                    href="{{ route('offers.view', ['offer_id' => 'OFF-' . $off['id']]) }}"
                    class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition inline-flex items-center gap-1"
                  >
                    <span>Negotiation Thread</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                  </a>

                  @if ($isPending)
                    <button
                      type="button"
                      wire:click="declineOffer({{ $off['id'] }})"
                      wire:confirm="Are you sure you want to decline this offer?"
                      class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition cursor-pointer"
                    >
                      Decline
                    </button>

                    <button
                      type="button"
                      wire:click="acceptOffer({{ $off['id'] }})"
                      wire:confirm="Accept this quote from {{ $off['vendor_name'] }} for ₦{{ number_format($off['price']) }}? An escrow-secured invoice will be generated."
                      class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-1.5"
                    >
                      <i class="fas fa-check text-[10px]"></i>
                      <span>Accept &amp; Checkout</span>
                    </button>
                  @elseif ($isAccepted)
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 font-black text-xs flex items-center gap-1">
                      <i class="fas fa-check-circle"></i>
                      <span>Accepted Quote</span>
                    </span>
                  @endif
                </div>
              </div>

            </div>
          @empty
            <div class="p-8 text-center bg-slate-50/70 border border-slate-200/80 rounded-2xl space-y-2">
              <div class="w-12 h-12 rounded-2xl bg-white text-slate-400 grid place-items-center text-lg mx-auto shadow-2xs border border-slate-100">
                <i class="fas fa-inbox"></i>
              </div>
              <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">No Private Quotes Yet</h3>
              <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Verified vendors are reviewing your specifications. As soon as a quote is submitted, it will appear here with pricing, warranty, and delivery terms.
              </p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- COMMUNITY INQUIRIES & RESPONSES THREAD -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-6 space-y-4 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-comments text-pp-600"></i>
            <span>Discussion &amp; Inquiries ({{ count($responses) }} {{ Str::plural('Reply', count($responses)) }})</span>
          </h2>
          <span class="text-xs text-slate-400 font-medium">Public Community Thread</span>
        </div>

        <!-- Replies Stream -->
        <div class="space-y-3">
          @forelse ($responses as $resp)
            <div class="bg-slate-50/80 border border-slate-100 rounded-2xl p-4 space-y-2 {{ $resp['is_requester'] ? 'border-pp-200 bg-pp-50/20' : '' }}">
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-slate-900">{{ $resp['user_name'] }}</span>
                  @if ($resp['is_requester'])
                    <span class="px-2 py-0.2 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">Requester</span>
                  @else
                    <span class="text-[10px] text-slate-400">({{ $resp['user_city'] }})</span>
                  @endif
                </div>
                <span class="text-slate-400 text-[10px]">{{ $resp['time'] }}</span>
              </div>
              <p class="text-xs text-slate-700 leading-relaxed">
                {{ $resp['body'] }}
              </p>
            </div>
          @empty
            <p class="text-xs text-slate-400 italic py-2">
              No comments have been posted to this discussion thread yet.
            </p>
          @endforelse
        </div>

        <!-- Requester Reply Box -->
        <div class="pt-3 border-t border-slate-100 space-y-2">
          <label for="replyInput" class="text-xs font-bold text-slate-700 block">Post a clarification or reply to sellers:</label>
          <div class="flex gap-2">
            <input
              id="replyInput"
              type="text"
              wire:model="replyText"
              wire:keydown.enter="postReply"
              placeholder="e.g. Yes, model is 2019, 65W charger also needed..."
              class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500/20 focus:bg-white transition"
            />
            <button
              type="button"
              wire:click="postReply"
              class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer shrink-0"
            >
              Post Reply
            </button>
          </div>
          @error('replyText')
            <span class="text-rose-600 text-xs font-bold">{{ $message }}</span>
          @enderror
        </div>

      </div>

    </div>

    <!-- RIGHT SIDEBAR: Escrow Protection & Summary Metrics -->
    <div class="lg:col-span-4 space-y-5">

      <!-- ESCROW SECURITY CARD -->
      <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 space-y-4 shadow-xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-white/10 grid place-items-center text-lg text-emerald-400">
            <i class="fas fa-shield-halved"></i>
          </div>
          <div>
            <h3 class="font-extrabold text-sm tracking-tight">Parts &amp; Parcel Escrow</h3>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">100% Guaranteed</span>
          </div>
        </div>

        <p class="text-xs text-slate-300 leading-relaxed font-normal">
          When you accept any quote on this page, payment is securely held in platform escrow. The seller only receives funds once you confirm the delivered item matches specifications and passes testing.
        </p>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-3.5 space-y-2 text-xs">
          <div class="flex items-center justify-between text-slate-300">
            <span>Inspection Window:</span>
            <span class="font-extrabold text-white">48 - 72 Hours</span>
          </div>
          <div class="flex items-center justify-between text-slate-300">
            <span>Warranty Enforcement:</span>
            <span class="font-extrabold text-emerald-400">Binding on Seller</span>
          </div>
          <div class="flex items-center justify-between text-slate-300">
            <span>Dispute Resolution:</span>
            <span class="font-extrabold text-white">Full Escrow Refund</span>
          </div>
        </div>
      </div>

      <!-- QUOTE METRICS CARD -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-5 space-y-4 shadow-soft">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">
          Request Overview
        </h3>

        <div class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
          <div class="py-2.5 flex items-center justify-between">
            <span class="text-slate-500">Case Reference:</span>
            <span class="font-mono font-black text-slate-900">#REQ-{{ $discussion?->id ?? '8402' }}</span>
          </div>

          <div class="py-2.5 flex items-center justify-between">
            <span class="text-slate-500">Quotes Received:</span>
            <span class="font-black text-pp-700">{{ count($offers) }} {{ Str::plural('Quote', count($offers)) }}</span>
          </div>

          @if (!empty($offers))
            @php
              $minPrice = collect($offers)->min('price');
            @endphp
            @if ($minPrice > 0)
              <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-500">Lowest Quote:</span>
                <span class="font-black text-emerald-700">₦{{ number_format($minPrice, 2) }}</span>
              </div>
            @endif
          @endif

          <div class="py-2.5 flex items-center justify-between">
            <span class="text-slate-500">Target Budget:</span>
            <span class="font-black text-slate-900">{{ $budget }}</span>
          </div>

          <div class="py-2.5 flex items-center justify-between">
            <span class="text-slate-500">Current Status:</span>
            <span class="font-extrabold uppercase {{ $status === 'open' ? 'text-emerald-700' : 'text-slate-600' }}">
              {{ $status }}
            </span>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>