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
      @if ($isOwner && $status === 'open')
        <button
          type="button"
          wire:click="openEditModal"
          class="px-3.5 py-2 rounded-xl bg-pp-50 border border-pp-200 hover:bg-pp-100 text-pp-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-2xs"
        >
          <i class="fas fa-pen-to-square text-[11px]"></i>
          <span>Edit Request</span>
        </button>
      @endif

      @if ($status === 'open')
        <button
          type="button"
          wire:click="closeRequest"
          wire:confirm="Are you sure you want to close this request? It will stop receiving new offers."
          class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-2xs"
        >
          <i class="fas fa-lock text-[10px]"></i>
          <span>Close Request</span>
        </button>
      @else
        <button
          type="button"
          wire:click="reopenRequest"
          class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-2xs"
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
          <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400 font-bold">Category: {{ $categoryName }}</span>
            @if ($isOwner && $status === 'open')
              <button
                type="button"
                wire:click="openEditModal"
                class="text-xs font-extrabold text-pp-600 hover:underline flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-pencil text-[10px]"></i> Edit
              </button>
            @endif
          </div>
        </div>

        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-normal whitespace-pre-line">
          {{ $body }}
        </p>

        <!-- Specifications & Parameters Grid (Roomy 2-Column Display) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
          
          <!-- Target Budget -->
          <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/70 hover:bg-white hover:shadow-2xs transition">
            <div class="w-10 h-10 rounded-xl bg-pp-100 text-pp-700 flex items-center justify-center shrink-0">
              <i class="fas fa-wallet text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Target Budget</span>
              <span class="text-sm font-black text-pp-700 break-words block">{{ $budget }}</span>
            </div>
          </div>

          <!-- Device / Model -->
          <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/70 hover:bg-white hover:shadow-2xs transition">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i class="fas fa-microchip text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Device &amp; Model</span>
              <span class="text-sm font-bold text-slate-900 break-words block">{{ $deviceInfo ?: 'Standard Specification' }}</span>
            </div>
          </div>

          <!-- Location -->
          <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/70 hover:bg-white hover:shadow-2xs transition">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
              <i class="fas fa-location-dot text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Location</span>
              <span class="text-xs sm:text-sm font-bold text-slate-800 break-words block">{{ $locationText }}</span>
            </div>
          </div>

          <!-- Fulfillment Preference -->
          <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/70 hover:bg-white hover:shadow-2xs transition">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
              <i class="fas fa-truck-fast text-sm"></i>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Fulfillment Preference</span>
              <span class="text-xs sm:text-sm font-bold text-slate-800 break-words block">{{ ucfirst($fulfillment) }}</span>
            </div>
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

      <!-- COMMUNITY INQUIRIES & RESPONSES THREAD (LAST 5 RESPONSES) -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-6 space-y-4 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-comments text-pp-600"></i>
            <span>Discussion &amp; Inquiries (Last {{ count($responses) }} {{ Str::plural('Response', count($responses)) }})</span>
          </h2>
          <span class="text-xs text-slate-400 font-medium">Showing most recent 5 responses</span>
        </div>

        <!-- Replies Stream -->
        <div class="space-y-3">
          @forelse ($responses as $resp)
            <div class="bg-slate-50/80 border border-slate-100 rounded-2xl p-4 space-y-3 {{ $resp['is_requester'] ? 'border-pp-200 bg-pp-50/20' : '' }}">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-bold text-slate-900">{{ $resp['user_name'] }}</span>
                  @if ($resp['is_requester'])
                    <span class="px-2 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">Requester</span>
                  @else
                    <span class="text-[10px] text-slate-400">({{ $resp['user_city'] }})</span>
                  @endif

                  <!-- Offer Indicator Badge -->
                  @if ($resp['has_offer'] ?? false)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                      <i class="fas fa-handshake text-[9px]"></i>
                      <span>Offer Attached @if(!empty($resp['offer_price'])) · ₦{{ number_format($resp['offer_price']) }} @endif</span>
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold">
                      <i class="fas fa-clock text-[9px]"></i>
                      <span>No offer attached</span>
                    </span>
                  @endif
                </div>

                <div class="flex items-center gap-2.5">
                  <span class="text-slate-400 text-[10px]">{{ $resp['time'] }}</span>
                  @if ($resp['has_offer'] ?? false)
                    <button
                      type="button"
                      wire:click="openQuickViewOffer({{ $resp['id'] }})"
                      @click="$dispatch('open-quick-view-offer', { response_id: {{ $resp['id'] }} })"
                      class="px-3.5 py-1.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-2xs transition cursor-pointer flex items-center gap-1.5 shrink-0"
                      title="Open quick view drawer for this offer"
                    >
                      <i class="fas fa-eye text-[11px]"></i>
                      <span>View Offer</span>
                    </button>
                  @endif
                </div>
              </div>

              <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $resp['body'] }}
              </p>
            </div>
          @empty
            <p class="text-xs text-slate-400 italic py-2">
              No comments have been posted to this discussion thread yet.
            </p>
          @endforelse
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
          When you accept any offer on this page, payment is securely held in platform escrow. The seller only receives funds once you confirm the delivered item matches specifications and passes testing.
        </p>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-3.5 space-y-2 text-xs">
          <div class="flex items-center justify-between text-slate-300">
            <span>Inspection Window:</span>
            <span class="font-extrabold text-white">48 - 72 Hours</span>
          </div>
          <div class="flex items-center justify-between text-slate-300">
            <span>Dispute Resolution:</span>
            <span class="font-extrabold text-white">Full Escrow Refund</span>
          </div>
        </div>
      </div>

      <!-- OFFER METRICS CARD -->
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
            <span class="text-slate-500">Offers Received:</span>
            <span class="font-black text-pp-700">{{ count($offers) }} {{ Str::plural('Offer', count($offers)) }}</span>
          </div>

          @if (!empty($offers))
            @php
              $minPrice = collect($offers)->min('price');
            @endphp
            @if ($minPrice > 0)
              <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-500">Lowest Offer:</span>
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

  <!-- EDIT REQUEST MODAL DIALOG -->
  @if ($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeEditModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
          <!-- MODAL HEADER -->
          <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-base font-extrabold">
                <i class="fas fa-pen-to-square"></i>
              </div>
              <div>
                <h3 class="text-base font-extrabold text-slate-900">Edit Request Specifications</h3>
                <p class="text-xs text-slate-500 mt-0.5">Responders and watchers will be notified of your changes.</p>
              </div>
            </div>
            <button type="button" wire:click="closeEditModal" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
              <i class="fas fa-times text-sm"></i>
            </button>
          </div>

          <!-- FORM CONTENT -->
          <form wire:submit.prevent="saveRequest" class="p-6 space-y-4 text-xs">
            <!-- Title -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Request Title <span class="text-rose-500">*</span></label>
              <input
                type="text"
                wire:model="editTitle"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                placeholder="e.g. Looking for HP EliteBook 840 G5 Motherboard"
              />
              @error('editTitle') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Description / Specifications -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Detailed Specifications &amp; Requirements <span class="text-rose-500">*</span></label>
              <textarea
                rows="4"
                wire:model="editBody"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition leading-relaxed"
                placeholder="Provide detailed model specifications, part numbers, condition requirements..."
              ></textarea>
              @error('editBody') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Budget & Urgency Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Target Budget</label>
                <input
                  type="text"
                  wire:model="editBudget"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                  placeholder="e.g. ₦70,000 - ₦90,000"
                />
                @error('editBudget') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Urgency</label>
                <select
                  wire:model="editUrgency"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                >
                  <option value="Flexible">Flexible</option>
                  <option value="Within 24 Hours">Within 24 Hours</option>
                  <option value="Within 3 Days">Within 3 Days</option>
                  <option value="Within 1 Week">Within 1 Week</option>
                </select>
                @error('editUrgency') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- Fulfillment & Category Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Fulfillment Preference</label>
                <select
                  wire:model="editFulfillment"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                >
                  <option value="Flexible">Flexible</option>
                  <option value="Buyer Pickup">Buyer Pickup</option>
                  <option value="Seller Delivery">Seller Delivery</option>
                  <option value="Platform Courier">Platform Courier</option>
                </select>
                @error('editFulfillment') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Category</label>
                <select
                  wire:model="editCategoryId"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                >
                  <option value="">Select Category (Optional)</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
                @error('editCategoryId') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- Brand & Model Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Brand</label>
                <select
                  wire:model.live="editBrandId"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                >
                  <option value="">Select Brand (Optional)</option>
                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
                @error('editBrandId') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Device Model</label>
                <select
                  wire:model="editModelId"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 focus:bg-white focus:border-pp-500 focus:outline-none transition"
                  @if(!$editBrandId) disabled @endif
                >
                  <option value="">Select Model (Optional)</option>
                  @foreach ($deviceModels as $mod)
                    <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                  @endforeach
                </select>
                @error('editModelId') <span class="text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- Notification Batching Notice -->
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-start gap-2.5">
              <i class="fas fa-info-circle text-amber-600 text-sm mt-0.5 shrink-0"></i>
              <div>
                <p class="font-extrabold">Smart Notification Batching:</p>
                <p class="text-amber-800 mt-0.5">Responders and watchers will receive email and in-app alerts about your updates. Notifications are automatically grouped so that rapid successive edits send a single consolidated alert.</p>
              </div>
            </div>

            <!-- MODAL ACTIONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                wire:click="closeEditModal"
                class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-1.5"
                wire:loading.attr="disabled"
              >
                <span wire:loading.remove>Save &amp; Notify Responders</span>
                <span wire:loading class="flex items-center gap-1.5">
                  <i class="fas fa-spinner fa-spin"></i> Saving...
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

</div>