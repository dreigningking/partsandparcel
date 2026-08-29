<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
  x-data="{
    activeMediaModal: false,
    currentIndex: 0,
    mediaItems: [
      { type: 'image', title: 'Motherboard Front (Clean Pull)', src: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80', badge: 'Photo' },
      { type: 'image', title: 'Motherboard Back & Serial Tag', src: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=800&q=80', badge: 'Photo' },
      { type: 'video', title: 'Power Boot Test Video (0:15)', src: 'https://www.w3schools.com/html/mov_bbb.mp4', badge: 'Video' },
      { type: 'pdf', title: 'Diagnostic Report & Specs.pdf', size: '1.4 MB', badge: 'PDF Doc' }
    ],
    openMedia(index) {
      this.currentIndex = index;
      this.activeMediaModal = true;
    },
    nextMedia() {
      if (this.currentIndex < this.mediaItems.length - 1) this.currentIndex++;
    },
    prevMedia() {
      if (this.currentIndex > 0) this.currentIndex--;
    }
  }"
  @keydown.escape.window="activeMediaModal = false">

  <!-- BREADCRUMB -->
  <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
    <a href="/" class="hover:text-pp-600 transition">Home</a>
    <span>/</span>
    <a href="#" class="hover:text-pp-600 transition">Community Hub</a>
    <span>/</span>
    <a href="#" class="hover:text-pp-600 transition">Electronics</a>
    <span>/</span>
    <span class="text-slate-900 font-bold">Request #REQ-8402</span>
  </div>

  @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('message') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <div class="grid lg:grid-cols-12 gap-8 items-start">
    
    <!-- LEFT COLUMN: SOCIAL MEDIA POST CARD & RESPONSES -->
    <div class="lg:col-span-8 space-y-6">

      <!-- SOCIAL MEDIA POST CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 space-y-5 shadow-soft">
        
        <!-- POST AUTHOR HEADER & CATEGORY BADGES -->
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
          
          <!-- AUTHOR INFO (AVATAR, NAME, TIME, LOCATION) -->
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold text-sm grid place-items-center shrink-0 border-2 border-pp-200">
              TS
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-extrabold text-slate-950 text-sm">TechSam</span>
                <span class="px-2 py-0.5 rounded-full bg-pp-50 text-pp-700 font-bold text-[10px] border border-pp-100">
                  ✓ Verified Member
                </span>
              </div>
              <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5 flex-wrap">
                <span><i class="fas fa-map-marker-alt text-slate-400 mr-0.5"></i> Computer Village, Ikeja, Lagos</span>
                <span>·</span>
                <span><i class="far fa-clock text-slate-400 mr-0.5"></i> 2 hours ago</span>
              </div>
            </div>
          </div>

          <!-- CATEGORY TAG & REQUEST TYPE BADGE -->
          <div class="flex flex-col items-end gap-1.5 shrink-0">
            <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold text-[10px] uppercase tracking-wider border border-pp-100">
              <i class="fas fa-microchip mr-1"></i> Product / Part
            </span>
            <span class="text-[10px] text-slate-400 font-semibold">Electronics › Laptops</span>
          </div>
        </div>

        <!-- POST TITLE & BODY CONTENT -->
        <div class="space-y-3">
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-950 leading-snug">
            Looking for HP EliteBook 840 G5 motherboard in Lagos
          </h1>

          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
            I need a clean, tested HP EliteBook 840 G5 motherboard without GPU issues.
            I am willing to pick up at Computer Village today. Instant payment guaranteed.
          </p>
        </div>

        <!-- EMBEDDED SPECIFICATION PILLS GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Target Budget</span>
            <span class="font-extrabold text-pp-700 text-sm">₦70,000 - ₦90,000</span>
          </div>

          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Preferred Fulfilment</span>
            <span class="font-bold text-slate-800">Pickup in Shop</span>
          </div>

          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Condition Desired</span>
            <span class="font-bold text-slate-800">Used / Tested</span>
          </div>
        </div>

        <!-- COMPACT MEDIA ATTACHMENTS THUMBNAILS (PHOTO, VIDEO, PDF) -->
        <div class="space-y-2">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Attached Media &amp; Documents (4):</span>
          <div class="flex items-center gap-3 overflow-x-auto pb-1">
            
            <!-- THUMB 0: PHOTO 1 -->
            <div @click="openMedia(0)" class="w-16 h-16 rounded-2xl border-2 border-slate-200 hover:border-pp-600 transition bg-slate-100 relative overflow-hidden shrink-0 cursor-pointer shadow-2xs group" title="Motherboard Front Photo">
              <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=200&q=80" alt="Mobo" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" />
              <span class="absolute bottom-1 right-1 bg-slate-950/70 text-white text-[9px] font-extrabold px-1 rounded"><i class="fas fa-image"></i></span>
            </div>

            <!-- THUMB 1: PHOTO 2 -->
            <div @click="openMedia(1)" class="w-16 h-16 rounded-2xl border-2 border-slate-200 hover:border-pp-600 transition bg-slate-100 relative overflow-hidden shrink-0 cursor-pointer shadow-2xs group" title="Motherboard Back Photo">
              <img src="https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=200&q=80" alt="Mobo Back" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" />
              <span class="absolute bottom-1 right-1 bg-slate-950/70 text-white text-[9px] font-extrabold px-1 rounded"><i class="fas fa-image"></i></span>
            </div>

            <!-- THUMB 2: VIDEO -->
            <div @click="openMedia(2)" class="w-16 h-16 rounded-2xl border-2 border-slate-200 hover:border-pp-600 transition bg-slate-900 relative overflow-hidden shrink-0 cursor-pointer shadow-2xs group flex flex-col items-center justify-center text-white" title="Boot Test Video">
              <div class="w-7 h-7 rounded-full bg-pp-600 text-white grid place-items-center text-xs group-hover:scale-110 transition">
                <i class="fas fa-play ml-0.5"></i>
              </div>
              <span class="absolute bottom-1 right-1 bg-slate-950/80 text-amber-400 text-[8px] font-extrabold px-1 rounded">0:15</span>
            </div>

            <!-- THUMB 3: PDF -->
            <div @click="openMedia(3)" class="w-16 h-16 rounded-2xl border-2 border-slate-200 hover:border-pp-600 transition bg-rose-50 relative overflow-hidden shrink-0 cursor-pointer shadow-2xs group flex flex-col items-center justify-center p-1 text-center" title="Diagnostic Spec Sheet PDF">
              <i class="fas fa-file-pdf text-rose-600 text-xl group-hover:scale-110 transition"></i>
              <span class="text-[9px] font-extrabold text-rose-800 uppercase mt-0.5 truncate w-full">Diagnostic</span>
              <span class="absolute bottom-1 right-1 bg-rose-600 text-white text-[8px] font-extrabold px-1 rounded">PDF</span>
            </div>

          </div>
        </div>

        <!-- SOCIAL ENGAGEMENT & ACTION BAR -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs">
          
          <div class="flex items-center gap-4 text-slate-600">
            <!-- ENGAGEMENT STATS -->
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-handshake text-pp-600"></i> <strong class="text-slate-900">3</strong> Offers
            </span>
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-comment-dots text-pp-600"></i> <strong class="text-slate-900">7</strong> Replies
            </span>
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-eye text-slate-400"></i> <strong class="text-slate-900">48</strong> Views
            </span> 
          </div>

          <div class="flex items-center gap-4">
            <!-- FOLLOW BUTTON (CHANGED FROM SAVE REQUEST) -->
            <button class="flex items-center gap-1.5 font-bold hover:text-pp-600 transition cursor-pointer text-pp-700">
              <i class="fas fa-rss text-pp-600 text-xs"></i> Follow
            </button>

            <!-- SHARE BUTTON -->
            <button class="flex items-center gap-1.5 font-bold hover:text-pp-600 transition cursor-pointer">
              <i class="fas fa-share-alt text-slate-400 text-sm"></i> Share
            </button>

            <!-- REPORT BUTTON -->
            <button class="text-slate-400 hover:text-rose-600 font-bold transition flex items-center gap-1 text-[11px] cursor-pointer">
              <i class="fas fa-flag"></i> Report
            </button>
          </div>
        </div>

      </div>

      <!-- RESPONSE COMPOSER CARD -->
      <div id="composerCard" class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-pen text-pp-600"></i> Write a Response
          </h3>
          <span class="text-xs text-slate-400 font-medium">Community responses remaining: <strong>7</strong> of 10</span>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <textarea wire:model="responseText" rows="3" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" placeholder="Write details about your available parts or repair service offer..."></textarea>
          </div>

          <!-- TOGGLE ATTACH OFFER FORM -->
          <div class="p-3.5 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-3">
            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 text-xs font-extrabold text-slate-900 cursor-pointer">
                <input type="checkbox" wire:click="toggleOfferComposer" {{ $isOfferActive ? 'checked' : '' }} class="rounded text-pp-600 focus:ring-pp-500" />
                <i class="fas fa-handshake text-pp-600"></i> Attach a Private Price Offer / Proposal to this response
              </label>
              <span class="text-[10px] uppercase font-bold text-pp-700 bg-pp-100 px-2 py-0.5 rounded-full">Optional</span>
            </div>

            @if ($isOfferActive)
              <div class="space-y-3 pt-2 border-t border-pp-200/60">
                <div class="grid sm:grid-cols-3 gap-3">
                  <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Offered Price (₦) <span class="text-rose-500">*</span></label>
                    <input type="number" wire:model="composerOfferPrice" placeholder="e.g. 80000" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                  </div>

                  <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Warranty Term</label>
                    <select wire:model="composerOfferWarranty" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                      <option value="7 days">7-Day Warranty</option>
                      <option value="14 days">14-Day Warranty</option>
                      <option value="30 days">30-Day Warranty</option>
                    </select>
                  </div>

                  <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Fulfillment Option</label>
                    <select wire:model="composerOfferDelivery" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600">
                      <option value="Buyer pickup">Buyer pickup ("I'll collect this from the seller")</option>
                      <option value="Seller delivery">Seller delivery ("The seller will deliver this to me")</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="text-[11px] font-bold text-slate-700 block mb-1">Custom Offer Terms / Note</label>
                  <textarea wire:model="composerOfferMessage" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" placeholder="e.g. Original motherboard, clean condition. Tested working."></textarea>
                </div>
              </div>
            @endif
          </div>

          <div class="flex items-center justify-end pt-2">
            <button wire:click="submitResponse" class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
              <i class="fas fa-paper-plane"></i>
              <span>Send Response {{ $isOfferActive ? '& Offer' : '' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- COMMUNITY RESPONSES / COMMENTS STREAM -->
      <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
          <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-comments text-pp-600"></i> Community Responses ({{ count($responses) }})
          </h3>
        </div>

        <!-- SOCIAL MEDIA COMMENT CARDS -->
        <div class="space-y-3.5">
          @foreach ($responses as $resp)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 space-y-3 shadow-2xs hover:shadow-soft transition">
              
              <!-- COMMENT AUTHOR HEADER -->
              <div class="flex items-start justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                  <!-- AVATAR CIRCLE -->
                  <div class="w-9 h-9 rounded-full bg-pp-100 text-pp-800 font-extrabold text-xs grid place-items-center shrink-0 border border-pp-200">
                    {{ strtoupper(substr($resp['author'], 0, 2)) }}
                  </div>

                  <div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                      <span class="font-extrabold text-slate-900 text-sm">{{ $resp['author'] }}</span>
                      @if (!empty($resp['verified']))
                        <span class="px-2 py-0.2 rounded-full bg-pp-50 text-pp-700 font-bold text-[10px] border border-pp-100">✓ Verified</span>
                      @endif
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                      <span>{{ $resp['location'] }}</span>
                      <span>·</span>
                      <span>{{ $resp['time'] }}</span>
                    </div>
                  </div>
                </div>

                <!-- MORE OPTIONS -->
                <button class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer" title="Options">
                  <i class="fas fa-ellipsis-h text-xs"></i>
                </button>
              </div>

              <!-- COMMENT BODY TEXT (FLUSH WITHOUT PL-12 PADDING) -->
              <div class="space-y-2">
                <p class="text-xs sm:text-sm text-slate-800 leading-relaxed">
                  {{ $resp['text'] }}
                </p>

                <!-- EMBEDDED PRIVATE OFFER CARD (IF VISIBLE TO REQUEST OWNER) -->
                @if ($isOwner && !empty($resp['negotiation']))
                  @php
                    $latestRound = end($resp['negotiation']);
                  @endphp
                  <div class="mt-3 p-3.5 rounded-xl bg-pp-50/70 border border-pp-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5">
                      <div class="w-8 h-8 rounded-lg bg-pp-600 text-white grid place-items-center text-xs shrink-0">
                        <i class="fas fa-handshake"></i>
                      </div>
                      <div>
                        <span class="font-extrabold text-slate-900 block">Private Proposal: <strong class="text-pp-700 text-sm">{{ $latestRound['price'] }}</strong></span>
                        <span class="text-[11px] text-slate-500">{{ $latestRound['warranty'] }} · {{ $latestRound['delivery'] }}</span>
                      </div>
                    </div>

                    <!-- VIEW OFFERS BUTTON: DESKTOP DRAWER VS MOBILE ROUTE -->
                    <button wire:click="openOffer({{ $resp['id'] }})" @click="$dispatch('open-quick-view-offer', { response_id: {{ $resp['id'] }} })" onclick="if (window.innerWidth < 768) { window.location.href = '{{ route('offers.view', ['id' => 'OFF-9021']) }}'; }" class="px-3.5 py-1.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-2xs transition cursor-pointer text-center shrink-0">
                      View Offers ({{ count($resp['negotiation']) }})
                    </button>
                  </div>
                @endif
              </div>

              <!-- COMMENT FOOTER: MESSAGE VENDOR ON LEFT, HELPFUL & REPORT ON RIGHT -->
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <!-- LEFT SIDE: ONLY MESSAGE VENDOR -->
                <div>
                  <button wire:click="openVendorChat('{{ $resp['author'] }}')" class="flex items-center gap-1.5 font-extrabold text-pp-700 hover:underline transition cursor-pointer">
                    <i class="fas fa-envelope text-pp-600 text-xs"></i> Message Vendor
                  </button>
                </div>

                <!-- RIGHT SIDE: HELPFUL & REPORT -->
                <div class="flex items-center gap-4">
                  <button class="flex items-center gap-1 font-semibold hover:text-pp-600 transition cursor-pointer">
                    <i class="far fa-thumbs-up text-slate-400 text-xs"></i> Helpful
                  </button>

                  <button class="text-slate-400 hover:text-rose-600 font-semibold transition flex items-center gap-1 text-[11px] cursor-pointer">
                    <i class="fas fa-flag"></i> Report
                  </button>
                </div>
              </div>

            </div>
          @endforeach
        </div>

      </div>

    </div>

    <!-- RIGHT COLUMN: SIDEBAR WIDGETS -->
    <div class="lg:col-span-4 space-y-6">
      
      <!-- COMBINED WIDGET: REQUEST OVERVIEW & STATUS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-info-circle text-pp-600"></i> Request Overview &amp; Status
        </h3>

        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between items-center text-slate-600 border-b border-slate-100 pb-2">
            <span>Status</span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] uppercase border border-emerald-100">● Open</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Target Budget</span>
            <span class="font-extrabold text-pp-700">₦70,000 - ₦90,000</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Location</span>
            <span class="font-bold text-slate-800">Ikeja, Lagos</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Request Type</span>
            <span class="font-bold text-slate-800">Product / Part</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Fulfilment</span>
            <span class="font-bold text-slate-800">Pickup in Shop</span>
          </div>

          <div class="flex justify-between items-center border-b border-slate-100 pb-2 text-slate-600">
            <span>Activity</span>
            <span class="font-bold text-slate-800">7 Responses · 3 Negotiations · 48 Views</span>
          </div>

          <div class="flex justify-between items-center border-b border-slate-100 pb-2 text-slate-600">
            <span>Posted</span>
            <span class="font-semibold text-slate-700">2 hours ago</span>
          </div>

          <div class="flex justify-between items-center text-slate-600">
            <span>Last activity</span>
            <span class="font-semibold text-slate-700">12 minutes ago</span>
          </div>
        </div>
      </div>

      <!-- WIDGET: SIMILAR REQUESTS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-link text-pp-600"></i> Similar Requests
        </h3>

        <div class="space-y-3 text-xs">
          
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 hover:border-pp-300 transition cursor-pointer">
            <h4 class="font-bold text-slate-900 leading-snug">Need a working HP EliteBook 840 G5 battery in Lagos</h4>
            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
              <span><i class="fas fa-map-marker-alt"></i> Ikeja</span>
              <span class="font-extrabold text-pp-700">₦25,000</span>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 hover:border-pp-300 transition cursor-pointer">
            <h4 class="font-bold text-slate-900 leading-snug">Looking for Dell Latitude 5400 motherboard - tested</h4>
            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
              <span><i class="fas fa-map-marker-alt"></i> Computer Village</span>
              <span class="font-extrabold text-pp-700">₦50,000</span>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 hover:border-pp-300 transition cursor-pointer">
            <h4 class="font-bold text-slate-900 leading-snug">HP EliteBook 840 G5 screen replacement in Abuja</h4>
            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
              <span><i class="fas fa-map-marker-alt"></i> Abuja</span>
              <span class="font-extrabold text-pp-700">₦35,000</span>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>

  <!-- MEDIA LIGHTBOX MODAL OVERLAY -->
  <div x-show="activeMediaModal"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95"
       x-cloak
       class="fixed inset-0 z-[200] bg-slate-950/85 backdrop-blur-md flex flex-col justify-between p-4 sm:p-6 text-white select-none">
    
    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
      <div class="flex items-center gap-3">
        <span class="px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-extrabold text-[10px] uppercase" x-text="mediaItems[currentIndex].badge"></span>
        <h3 class="text-sm sm:text-base font-extrabold text-white truncate max-w-md" x-text="mediaItems[currentIndex].title"></h3>
      </div>
      
      <div class="flex items-center gap-3">
        <span class="text-xs font-bold text-slate-400" x-text="`${currentIndex + 1} of ${mediaItems.length}`"></span>
        <button @click="activeMediaModal = false" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white grid place-items-center transition cursor-pointer text-base">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- MEDIA VIEWER CONTAINER -->
    <div class="flex-1 flex items-center justify-center p-4 relative min-h-0">
      
      <!-- PREVIOUS BUTTON -->
      <button @click="prevMedia()" :disabled="currentIndex === 0" :class="currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-800 text-white cursor-pointer'" class="absolute left-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-700 grid place-items-center transition z-10">
        <i class="fas fa-chevron-left text-base"></i>
      </button>

      <!-- IMAGE TYPE -->
      <template x-if="mediaItems[currentIndex].type === 'image'">
        <img :src="mediaItems[currentIndex].src" :alt="mediaItems[currentIndex].title" class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl border border-slate-800" />
      </template>

      <!-- VIDEO TYPE -->
      <template x-if="mediaItems[currentIndex].type === 'video'">
        <div class="w-full max-w-3xl aspect-video rounded-2xl overflow-hidden bg-black shadow-2xl border border-slate-800 flex items-center justify-center">
          <video controls autoplay :src="mediaItems[currentIndex].src" class="w-full h-full object-contain"></video>
        </div>
      </template>

      <!-- PDF TYPE -->
      <template x-if="mediaItems[currentIndex].type === 'pdf'">
        <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl max-w-md w-full text-center space-y-4 shadow-2xl">
          <div class="w-16 h-16 rounded-3xl bg-rose-500/20 text-rose-500 grid place-items-center text-3xl mx-auto">
            <i class="fas fa-file-pdf"></i>
          </div>
          <div class="space-y-1">
            <h4 class="text-base font-extrabold text-white" x-text="mediaItems[currentIndex].title"></h4>
            <p class="text-xs text-slate-400" x-text="`PDF Document · ${mediaItems[currentIndex].size}`"></p>
          </div>
          <a href="#" download class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
            <i class="fas fa-download"></i> Download PDF Document
          </a>
        </div>
      </template>

      <!-- NEXT BUTTON -->
      <button @click="nextMedia()" :disabled="currentIndex === mediaItems.length - 1" :class="currentIndex === mediaItems.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-800 text-white cursor-pointer'" class="absolute right-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-700 grid place-items-center transition z-10">
        <i class="fas fa-chevron-right text-base"></i>
      </button>
    </div>

    <!-- MODAL FOOTER NAV SLIDES -->
    <div class="flex items-center justify-center gap-2 pt-3 border-t border-slate-800 overflow-x-auto">
      <template x-for="(item, idx) in mediaItems" :key="idx">
        <button @click="currentIndex = idx" :class="currentIndex === idx ? 'border-pp-500 ring-2 ring-pp-500/50 scale-105' : 'border-slate-800 opacity-60 hover:opacity-100'" class="w-12 h-12 rounded-xl border bg-slate-900 overflow-hidden shrink-0 transition flex items-center justify-center cursor-pointer">
          <template x-if="item.type === 'image'">
            <img :src="item.src" class="w-full h-full object-cover" />
          </template>
          <template x-if="item.type === 'video'">
            <i class="fas fa-video text-amber-400 text-xs"></i>
          </template>
          <template x-if="item.type === 'pdf'">
            <i class="fas fa-file-pdf text-rose-500 text-xs"></i>
          </template>
        </button>
      </template>
    </div>

  </div>

</main>