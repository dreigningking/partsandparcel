<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
  x-data="{
    activeMediaModal: false,
    currentIndex: 0,
    mediaItems: @js($mediaItems),
    copiedShare: false,
    openMedia(index) {
      this.currentIndex = index;
      this.activeMediaModal = true;
    },
    nextMedia() {
      if (this.currentIndex < this.mediaItems.length - 1) this.currentIndex++;
    },
    prevMedia() {
      if (this.currentIndex > 0) this.currentIndex--;
    },
    copyShareLink() {
      navigator.clipboard.writeText(window.location.href);
      this.copiedShare = true;
      setTimeout(() => { this.copiedShare = false; }, 2500);
    }
  }"
  @keydown.escape.window="activeMediaModal = false">

  <!-- BREADCRUMB -->
  <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
    <a href="{{ route('welcome') }}" class="hover:text-pp-600 transition">Home</a>
    <span>/</span>
    <a href="{{ route('community') }}" class="hover:text-pp-600 transition">Community Hub</a>
    @if($discussion?->category)
      <span>/</span>
      <a href="{{ route('community') }}?category={{ urlencode($discussion->category->name) }}" class="hover:text-pp-600 transition">{{ $discussion->category->name }}</a>
    @endif
    <span>/</span>
    <span class="text-slate-900 font-bold">Request #REQ-{{ $discussion?->id ?? '8402' }}</span>
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

  @if (session()->has('warning'))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-600 text-base"></i>
        <span>{{ session('warning') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 cursor-pointer"><i class="fas fa-times"></i></button>
    </div>
  @endif

  <div class="grid lg:grid-cols-12 gap-8 items-start">
    
    <!-- LEFT COLUMN: SOCIAL MEDIA POST CARD & RESPONSES -->
    <div class="lg:col-span-8 space-y-6">

      <!-- SOCIAL MEDIA POST CARD -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 space-y-5 shadow-soft">
        
        <!-- POST AUTHOR HEADER & CATEGORY BADGES -->
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
          
          @php
            $authorName = $discussion?->user?->name ?? 'TechSam';
            $authorAvatar = strtoupper(substr($authorName, 0, 2));
            $authorLocation = $discussion->location ? "{$discussion->location->city}, {$discussion->location->state->name}" : "{$discussion->user->primaryLocation->city}, {$discussion->user->primaryLocation->state->name}";
            $postedTime = $discussion ? $discussion->created_at->diffForHumans() : '2 hours ago';
            $categoryName = $discussion?->category?->name ?? 'Electronics';
            $brandName = $discussion?->brand?->name ?? '';
            $modelName = $discussion?->deviceModel?->name ?? '';
            $typeLabel = match ($discussion?->type) {
              'service' => 'Repair / Service',
              'advice' => 'Question / Advice',
              default => 'Product / Part',
            };
            $postTitle = $discussion?->title ?? 'Looking for HP EliteBook 840 G5 motherboard in Lagos';
            $postBody = $discussion?->body ?? "I need a clean, tested HP EliteBook 840 G5 motherboard without GPU issues.\nI am willing to pick up at Computer Village today. Instant payment guaranteed.";
            $budget = $discussion?->budget ?: ($discussion?->attachments['budget'] ?? '₦70,000 - ₦90,000');
            $fulfillment = $discussion?->fulfillment ?? ($discussion?->attachments['fulfillment'] ?? 'Pickup / Delivery');
            $urgency = $discussion?->urgency ?? ($discussion?->attachments['urgency'] ?? 'Flexible');
            $isVerified = (bool) ($discussion?->user?->is_verified ?? true);
            $offersCount = $discussion ? $discussion->offers->count() : 3;
            $repliesCount = count($responses);
            $viewsCount = $discussion?->attachments['views'] ?? 48;
          @endphp

          <!-- AUTHOR INFO (AVATAR, NAME, TIME, LOCATION) -->
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold text-sm grid place-items-center shrink-0 border-2 border-pp-200">
              {{ $authorAvatar }}
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-extrabold text-slate-950 text-sm">{{ $authorName }}</span>
                @if($isVerified)
                  <span class="px-2 py-0.5 rounded-full bg-pp-50 text-pp-700 font-bold text-[10px] border border-pp-100">
                    ✓ Verified Member
                  </span>
                @endif
              </div>
              <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5 flex-wrap">
                <span><i class="fas fa-map-marker-alt text-slate-400 mr-0.5"></i> {{ $authorLocation }}</span>
                <span>·</span>
                <span><i class="far fa-clock text-slate-400 mr-0.5"></i> {{ $postedTime }}</span>
              </div>
            </div>
          </div>

          <!-- CATEGORY TAG & REQUEST TYPE BADGE -->
          <div class="flex flex-col items-end gap-1.5 shrink-0">
            <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold text-[10px] uppercase tracking-wider border border-pp-100">
              <i class="fas fa-microchip mr-1"></i> {{ $typeLabel }}
            </span>
            <span class="text-[10px] text-slate-400 font-semibold">{{ $categoryName }}</span>
          </div>
        </div>

        <!-- POST TITLE & BODY CONTENT -->
        <div class="space-y-3">
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-950 leading-snug">
            {{ $postTitle }}
          </h1>

          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
            {{ $postBody }}
          </p>
        </div>

        <!-- EMBEDDED SPECIFICATION PILLS GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Target Budget</span>
            <span class="font-extrabold text-pp-700 text-sm">{{ $budget }}</span>
          </div>

          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Preferred Fulfilment</span>
            <span class="font-bold text-slate-800">{{ $fulfillment }}</span>
          </div>

          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Brand &amp; Model</span>
            <span class="font-bold text-slate-800">
              @if($brandName)
                {{ $brandName }} @if($modelName) · {{ $modelName }} @endif
              @else
                Any Compatible
              @endif
            </span>
          </div>

          <div class="space-y-0.5">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Timeline / Urgency</span>
            <span class="font-bold {{ $urgency === 'Urgent (Today)' ? 'text-amber-600' : 'text-slate-800' }}">
              @if($urgency === 'Urgent (Today)') <i class="fas fa-bolt text-amber-500 text-[10px]"></i> @endif {{ $urgency }}
            </span>
          </div>
        </div>

        <!-- DYNAMIC MEDIA ATTACHMENTS THUMBNAILS (PHOTO, VIDEO, PDF) -->
        <template x-if="mediaItems && mediaItems.length > 0">
          <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
              Attached Media &amp; Documents (<span x-text="mediaItems.length"></span>):
            </span>
            <div class="flex items-center gap-3 overflow-x-auto pb-1">
              <template x-for="(item, index) in mediaItems" :key="index">
                <div @click="openMedia(index)" class="w-16 h-16 rounded-2xl border-2 border-slate-200 hover:border-pp-600 transition bg-slate-100 relative overflow-hidden shrink-0 cursor-pointer shadow-2xs group" :title="item.title">
                  <template x-if="item.type === 'image'">
                    <div class="w-full h-full relative">
                      <img :src="item.src" :alt="item.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" />
                      <span class="absolute bottom-1 right-1 bg-slate-950/70 text-white text-[9px] font-extrabold px-1 rounded"><i class="fas fa-image"></i></span>
                    </div>
                  </template>
                  <template x-if="item.type === 'video'">
                    <div class="w-full h-full bg-slate-900 flex flex-col items-center justify-center text-white relative">
                      <div class="w-7 h-7 rounded-full bg-pp-600 text-white grid place-items-center text-xs group-hover:scale-110 transition">
                        <i class="fas fa-play ml-0.5"></i>
                      </div>
                      <span class="absolute bottom-1 right-1 bg-slate-950/80 text-amber-400 text-[8px] font-extrabold px-1 rounded">VID</span>
                    </div>
                  </template>
                  <template x-if="item.type === 'pdf' || item.type === 'document'">
                    <div class="w-full h-full bg-rose-50 flex flex-col items-center justify-center p-1 text-center relative">
                      <i class="fas fa-file-pdf text-rose-600 text-xl group-hover:scale-110 transition"></i>
                      <span class="text-[8px] font-extrabold text-rose-800 uppercase mt-0.5 truncate w-full" x-text="item.title"></span>
                      <span class="absolute bottom-1 right-1 bg-rose-600 text-white text-[8px] font-extrabold px-1 rounded">DOC</span>
                    </div>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </template>

        <!-- SOCIAL ENGAGEMENT & ACTION BAR -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-100 text-xs">
          
          <div class="flex items-center gap-4 text-slate-600">
            <!-- ENGAGEMENT STATS -->
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-handshake text-pp-600"></i> <strong class="text-slate-900">{{ $offersCount }}</strong> Offers
            </span>
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-comment-dots text-pp-600"></i> <strong class="text-slate-900">{{ $repliesCount }}</strong> Replies
            </span>
            <span class="flex items-center gap-1 font-semibold text-slate-500">
              <i class="fas fa-eye text-slate-400"></i> <strong class="text-slate-900">{{ $viewsCount }}</strong> Views
            </span> 
          </div>

          <div class="flex items-center gap-3.5 flex-wrap">
            <!-- WATCHLIST BUTTON -->
            <button wire:click="toggleWatch" class="flex items-center gap-1.5 font-bold transition cursor-pointer {{ $isWatched ? 'text-pp-600' : 'text-slate-600 hover:text-pp-600' }}" title="{{ $isWatched ? 'Stop watching this discussion' : 'Watch this discussion for updates' }}">
              <i class="fas {{ $isWatched ? 'fa-eye text-pp-600' : 'fa-eye text-slate-400' }}"></i>
              <span>{{ $isWatched ? 'Watching' : 'Watch' }} ({{ $watchersCount }})</span>
            </button>

            @if($isOwner)
              <!-- EDIT DISCUSSION BUTTON -->
              <button wire:click="openEditModal" class="flex items-center gap-1.5 font-extrabold text-pp-700 hover:text-pp-800 transition cursor-pointer">
                <i class="fas fa-edit text-pp-600"></i> Edit Request
              </button>
            @endif

            <!-- SHARE BUTTON -->
            <button @click="copyShareLink()" class="flex items-center gap-1.5 font-bold hover:text-pp-600 transition cursor-pointer">
              <i class="fas fa-share-alt text-slate-400 text-sm"></i>
              <span x-text="copiedShare ? 'Copied Link!' : 'Share'"></span>
            </button>

            <!-- REPORT BUTTON -->
            <button wire:click="openReportModal('discussion', {{ $discussion?->id ?? 0 }})" class="font-bold transition flex items-center gap-1 text-[11px] cursor-pointer {{ $isDiscussionReported ? 'text-amber-600' : 'text-slate-400 hover:text-rose-600' }}">
              <i class="fas {{ $isDiscussionReported ? 'fa-flag-checkered text-amber-600' : 'fa-flag' }}"></i>
              <span>{{ $isDiscussionReported ? 'Reported' : 'Report' }}</span>
            </button>
          </div>
        </div>

      </div>

      <!-- RESPONSE COMPOSER / AUTHOR BANNER / GUEST NOTICE -->
      @guest
        <!-- GUEST PROMPT -->
        <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center space-y-4 shadow-soft">
          <div class="w-14 h-14 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-xl font-bold mx-auto border border-pp-100">
            <i class="fas fa-lock"></i>
          </div>
          <div class="space-y-1">
            <h3 class="font-extrabold text-slate-900 text-base">Sign in to Join the Discussion</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
              You must be logged in to submit a response, share technical advice, or attach a commercial offer.
            </p>
          </div>
          <div class="flex items-center justify-center gap-3 pt-1">
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
              Log In
            </a>
            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-extrabold text-xs transition">
              Create Account
            </a>
          </div>
        </div>
      @elseif($isOwner)
        <!-- AUTHOR NOTICE CARD -->
        <div class="bg-gradient-to-r from-pp-50 via-white to-pp-50/40 rounded-3xl border border-pp-200 p-6 space-y-4 shadow-soft">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white grid place-items-center text-base shrink-0 shadow-2xs">
                <i class="fas fa-user-check"></i>
              </div>
              <div>
                <h3 class="text-sm font-extrabold text-slate-950">Author Management Control</h3>
                <p class="text-xs text-slate-500">You are the author of this community request.</p>
              </div>
            </div>
            <button wire:click="openEditModal" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer shrink-0">
              <i class="fas fa-edit"></i> Edit Request
            </button>
          </div>
          <p class="text-xs text-slate-600 leading-relaxed border-t border-pp-100 pt-3">
            Discussion authors cannot submit responses to their own request. You can edit the request specifications at any time, review vendor proposals below, or open quick view offers.
          </p>
        </div>
      @else
        <!-- RESPONSE COMPOSER CARD -->
        <div id="composerCard" class="bg-white rounded-3xl border-2 border-pp-500 p-6 space-y-4 shadow-soft">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-pen text-pp-600"></i> Write a Response
            </h3>
            <span class="text-xs text-slate-400 font-medium">
              Community responses remaining today: <strong class="text-slate-900">{{ $dailyResponsesRemaining }}</strong> of {{ $dailyResponseLimit }}
            </span>
          </div>

          @if($dailyResponsesRemaining <= 0)
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                <span>You have reached your daily response limit. Upgrade your subscription plan for higher quotas.</span>
              </div>
              <a href="{{ route('subscriptions') }}" class="px-3.5 py-1.5 rounded-lg bg-amber-600 text-white text-xs hover:bg-amber-700 transition">Upgrade</a>
            </div>
          @else
            <div class="space-y-3 text-xs">
              <div>
                <textarea wire:model="responseText" rows="3" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 outline-none focus:border-pp-600" placeholder="Write details about your available parts or repair service offer..."></textarea>
              </div>

              <!-- TOGGLE ATTACH OFFER FORM -->
              <div class="p-4 rounded-2xl bg-pp-50/70 border border-pp-200 space-y-4">
                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 text-xs font-extrabold text-slate-900 cursor-pointer">
                    <input type="checkbox" wire:click="toggleOfferComposer" {{ $isOfferActive ? 'checked' : '' }} class="rounded text-pp-600 focus:ring-pp-500" />
                    <i class="fas fa-handshake text-pp-600"></i> Attach an Itemized Price Offer / Proposal to this response
                  </label>
                  <span class="text-[10px] uppercase font-bold text-pp-700 bg-pp-100 px-2.5 py-0.5 rounded-full">Commercial Proposal</span>
                </div>

                @if ($isOfferActive)
                  <div class="space-y-4 pt-3 border-t border-pp-200/60 text-xs">
                    
                    <!-- 1. PART / ITEM SOURCE -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-3">
                      <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                          <i class="fas fa-box text-pp-600"></i> Part or Hardware Item
                        </span>
                        <span class="text-[10px] text-slate-400">Select listing or type description</span>
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
                          <input type="checkbox" wire:model.live="composerIncludeService" class="rounded text-pp-600 focus:ring-pp-500" />
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

                    <!-- 3. DIRECTIONAL SHIPMENTS (PICKUP & DELIVERY) -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80 space-y-3">
                      <span class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                        <i class="fas fa-truck-fast text-emerald-600"></i> Directional Delivery &amp; Pickup Options
                      </span>

                      <div class="grid sm:grid-cols-2 gap-3 pt-1">
                        <!-- Pickup Shipment (Buyer to Seller/Technician) -->
                        <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50 space-y-2">
                          <label class="flex items-center gap-2 font-bold text-slate-800 text-[11px] cursor-pointer">
                            <input type="checkbox" wire:model.live="composerIncludePickup" class="rounded text-pp-600 focus:ring-pp-500" />
                            <span>Pickup from Buyer (Customer device to Technician)</span>
                          </label>
                          @if ($composerIncludePickup)
                            <div class="pt-1">
                              <label class="text-[10px] font-bold text-slate-600 block mb-0.5">Pickup Dispatch Fee (₦)</label>
                              <input type="number" wire:model="composerPickupFee" placeholder="e.g. 3500" class="w-full p-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 outline-none focus:border-pp-600" />
                            </div>
                          @endif
                        </div>

                        <!-- Delivery Shipment (Seller/Technician to Buyer) -->
                        <div class="p-2.5 rounded-lg border border-slate-100 bg-slate-50 space-y-2">
                          <label class="flex items-center gap-2 font-bold text-slate-800 text-[11px] cursor-pointer">
                            <input type="checkbox" wire:model.live="composerIncludeDelivery" class="rounded text-pp-600 focus:ring-pp-500" />
                            <span>Delivery to Buyer (Technician to Customer)</span>
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
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">General Fulfillment Method</label>
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

              <div class="flex items-center justify-end pt-2">
                <button wire:click="submitResponse" class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                  <i class="fas fa-paper-plane"></i>
                  <span>Send Response {{ $isOfferActive ? '& Offer' : '' }}</span>
                </button>
              </div>
            </div>
          @endif
        </div>
      @endguest

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
                  <button wire:click="openVendorConversation({{ $resp['id'] }})" class="flex items-center gap-1.5 font-extrabold text-pp-700 hover:underline transition cursor-pointer">
                    <i class="fas fa-envelope text-pp-600 text-xs"></i> Message Vendor
                  </button>
                </div>

                <!-- RIGHT SIDE: HELPFUL & REPORT -->
                <div class="flex items-center gap-4">
                  <button class="flex items-center gap-1 font-semibold hover:text-pp-600 transition cursor-pointer">
                    <i class="far fa-thumbs-up text-slate-400 text-xs"></i> Helpful
                  </button>

                  @php
                    $isRespReported = in_array($resp['id'], $reportedResponseIds ?? []);
                  @endphp
                  <button wire:click="openReportModal('response', {{ $resp['id'] }})" class="font-semibold transition flex items-center gap-1 text-[11px] cursor-pointer {{ $isRespReported ? 'text-amber-600' : 'text-slate-400 hover:text-rose-600' }}">
                    <i class="fas {{ $isRespReported ? 'fa-flag-checkered text-amber-600' : 'fa-flag' }}"></i>
                    <span>{{ $isRespReported ? 'Reported' : 'Report' }}</span>
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
            <span class="px-2.5 py-0.5 rounded-full {{ ($discussion?->status ?? 'open') === 'open' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-100 text-slate-700 border-slate-200' }} font-bold text-[10px] uppercase border">
              ● {{ ucfirst($discussion?->status ?? 'Open') }}
            </span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Target Budget</span>
            <span class="font-extrabold text-pp-700">{{ $budget }}</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Location</span>
            <span class="font-bold text-slate-800">{{ $authorLocation }}</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Request Type</span>
            <span class="font-bold text-slate-800">{{ $typeLabel }}</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Category</span>
            <span class="font-bold text-slate-800">{{ $categoryName }}</span>
          </div>

          @if($brandName)
            <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
              <span>Brand &amp; Model</span>
              <span class="font-bold text-slate-800">{{ $brandName }} @if($modelName) · {{ $modelName }} @endif</span>
            </div>
          @endif

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Fulfilment</span>
            <span class="font-bold text-slate-800">{{ $fulfillment }}</span>
          </div>

          <div class="flex justify-between border-b border-slate-100 pb-2 text-slate-600">
            <span>Urgency</span>
            <span class="font-bold {{ $urgency === 'Urgent (Today)' ? 'text-amber-600' : 'text-slate-800' }}">{{ $urgency }}</span>
          </div>

          <div class="flex justify-between items-center border-b border-slate-100 pb-2 text-slate-600">
            <span>Activity</span>
            <span class="font-bold text-slate-800">{{ $offersCount }} Offers · {{ $repliesCount }} Replies · {{ $viewsCount }} Views</span>
          </div>

          <div class="flex justify-between items-center border-b border-slate-100 pb-2 text-slate-600">
            <span>Posted</span>
            <span class="font-semibold text-slate-700">{{ $postedTime }}</span>
          </div>

          <div class="flex justify-between items-center text-slate-600">
            <span>Last activity</span>
            <span class="font-semibold text-slate-700">{{ $discussion ? $discussion->updated_at->diffForHumans() : 'Just now' }}</span>
          </div>
        </div>
      </div>

      <!-- WIDGET: SIMILAR REQUESTS -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-soft">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fas fa-link text-pp-600"></i> Similar Requests
        </h3>

        <div class="space-y-3 text-xs">
          @forelse($similarRequests as $sim)
            <a href="{{ route('community.request', ['id' => $sim->id]) }}" class="block p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 hover:border-pp-300 hover:bg-pp-50/40 transition cursor-pointer">
              <h4 class="font-bold text-slate-900 leading-snug line-clamp-2">{{ $sim->title }}</h4>
              <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <span><i class="fas fa-map-marker-alt text-[10px]"></i> {{ $sim->location ? $sim->location->city : ($sim->attachments['location'] ?? 'Nigeria') }}</span>
                <span class="font-extrabold text-pp-700">{{ $sim->budget ?: ($sim->attachments['budget'] ?? 'Flexible') }}</span>
              </div>
            </a>
          @empty
            <p class="text-xs text-slate-400 py-2">No other related requests found.</p>
          @endforelse
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
        <span class="px-2.5 py-0.5 rounded-full bg-pp-600 text-white font-extrabold text-[10px] uppercase" x-text="mediaItems[currentIndex]?.badge"></span>
        <h3 class="text-sm sm:text-base font-extrabold text-white truncate max-w-md" x-text="mediaItems[currentIndex]?.title"></h3>
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
      <template x-if="mediaItems[currentIndex]?.type === 'image'">
        <img :src="mediaItems[currentIndex]?.src" :alt="mediaItems[currentIndex]?.title" class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl border border-slate-800" />
      </template>

      <!-- VIDEO TYPE -->
      <template x-if="mediaItems[currentIndex]?.type === 'video'">
        <div class="w-full max-w-3xl aspect-video rounded-2xl overflow-hidden bg-black shadow-2xl border border-slate-800 flex items-center justify-center">
          <video controls autoplay :src="mediaItems[currentIndex]?.src" class="w-full h-full object-contain"></video>
        </div>
      </template>

      <!-- PDF TYPE -->
      <template x-if="mediaItems[currentIndex]?.type === 'pdf' || mediaItems[currentIndex]?.type === 'document'">
        <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl max-w-md w-full text-center space-y-4 shadow-2xl">
          <div class="w-16 h-16 rounded-3xl bg-rose-500/20 text-rose-500 grid place-items-center text-3xl mx-auto">
            <i class="fas fa-file-pdf"></i>
          </div>
          <div class="space-y-1">
            <h4 class="text-base font-extrabold text-white" x-text="mediaItems[currentIndex]?.title"></h4>
            <p class="text-xs text-slate-400" x-text="`Document · ${mediaItems[currentIndex]?.size || 'Ready for download'}`"></p>
          </div>
          <a :href="mediaItems[currentIndex]?.src" download class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition">
            <i class="fas fa-download"></i> Download Document
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

  <!-- EDIT REQUEST MODAL -->
  @if($showEditModal)
    <div class="fixed inset-0 z-[150] overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-lg">
              <i class="fas fa-edit"></i>
            </div>
            <div>
              <h3 class="text-lg font-extrabold text-slate-900">Edit Discussion Request</h3>
              <p class="text-xs text-slate-500">Update your community request details</p>
            </div>
          </div>
          <button wire:click="closeEditModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 grid place-items-center transition cursor-pointer">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <form wire:submit.prevent="updateDiscussion" class="space-y-4 text-xs">
          <!-- TITLE -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Request Title *</label>
            <input type="text" wire:model.defer="editTitle" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold" placeholder="e.g. Need OEM iPhone 13 Pro Max Display Screen">
            @error('editTitle') <span class="text-rose-500 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- DESCRIPTION / BODY -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Details &amp; Specifications *</label>
            <textarea rows="4" wire:model.defer="editBody" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs" placeholder="Describe the item condition, exact part numbers, specific requirements..."></textarea>
            @error('editBody') <span class="text-rose-500 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
          </div>

          <!-- GRID ROW 1: BUDGET & STATUS -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Target Budget (₦)</label>
              <input type="number" step="0.01" wire:model.defer="editBudget" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold" placeholder="Leave empty for Flexible">
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Request Status</label>
              <select wire:model.defer="editStatus" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold">
                <option value="open">Open (Accepting Offers)</option>
                <option value="closed">Closed</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>

          <!-- GRID ROW 2: CATEGORY & BRAND -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Category</label>
              <select wire:model.live="editCategoryId" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold">
                <option value="">Select Category</option>
                @foreach($allCategories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Brand</label>
              <select wire:model.defer="editBrandId" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold">
                <option value="">Select Brand</option>
                @foreach($allBrands as $brand)
                  <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- GRID ROW 3: URGENCY & FULFILLMENT -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Urgency</label>
              <select wire:model.defer="editUrgency" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold">
                <option value="standard">Flexible</option>
                <option value="urgent">Urgent (Today)</option>
                <option value="within_48h">Within 24–48 hours</option>
                <option value="this_week">This week</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Fulfilment Preference</label>
              <select wire:model.defer="editFulfillment" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:border-pp-500 focus:ring-1 focus:ring-pp-500 outline-none text-xs font-semibold">
                <option value="flexible">Pickup / Delivery</option>
                <option value="buyer_pickup">Buyer pickup</option>
                <option value="seller_delivery">Seller delivery</option>
                <option value="shop_pickup">Pickup in Shop</option>
              </select>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold shadow-sm transition cursor-pointer flex items-center gap-1.5">
              <i class="fas fa-check"></i>
              <span>Save Changes</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif

</main>