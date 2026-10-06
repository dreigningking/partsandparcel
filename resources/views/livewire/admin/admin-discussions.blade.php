<div class="flex flex-col gap-6">

  <!-- ============================================================
       TOP HEADER & BREADCRUMB
  ============================================================ -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-extrabold text-pp-600 uppercase tracking-wider">Admin Control Center</span>
        <span class="text-slate-300">/</span>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Community Hub</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-950 flex items-center gap-2.5">
        <span>Discussions &amp; Requests Moderation</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold">
          {{ number_format($metrics['total']) }} Topics
        </span>
      </h1>
      <p class="text-xs text-slate-500 mt-0.5">
        Audit buyer requests, community troubleshooting threads, vendor quotes, and enforce forum quality guidelines.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('community') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-pp-300 hover:bg-pp-50 text-slate-700 font-extrabold text-xs shadow-2xs transition flex items-center gap-1.5">
        <i class="fas fa-external-link-alt text-slate-400"></i>
        <span>Live Community Hub</span>
      </a>
      <a href="{{ route('admin.moderations', ['type' => 'discussion']) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fas fa-gavel text-amber-400"></i>
        <span>Moderation Queue</span>
        @if($metrics['pending'] > 0)
          <span class="px-1.5 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black animate-pulse">
            {{ $metrics['pending'] }}
          </span>
        @endif
      </a>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  @if (session()->has('status'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
        <span>{{ session('status') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-2.5">
        <i class="fas fa-exclamation-triangle text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  @endif

  <!-- ============================================================
       EXECUTIVE METRIC CARDS
  ============================================================ -->
  <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
    
    <!-- 1. TOTAL TOPICS -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-pp-50 text-pp-600 border border-pp-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-comments"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Topics</span>
        <span class="text-lg font-black text-slate-950">{{ number_format($metrics['total']) }}</span>
      </div>
    </div>

    <!-- 2. APPROVED & LIVE -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-circle-check"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Approved &amp; Live</span>
        <span class="text-lg font-black text-emerald-600">{{ number_format($metrics['approved']) }}</span>
      </div>
    </div>

    <!-- 3. PENDING REVIEW -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3 {{ $metrics['pending'] > 0 ? 'ring-2 ring-amber-400/60 bg-amber-50/20' : '' }}">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pending Review</span>
        <span class="text-lg font-black text-amber-600">{{ number_format($metrics['pending']) }}</span>
      </div>
    </div>

    <!-- 4. OFFERS RECEIVED -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-handshake"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Vendor Offers</span>
        <span class="text-lg font-black text-blue-600">{{ number_format($metrics['offers']) }}</span>
      </div>
    </div>

    <!-- 5. FULFILLED / SOLVED -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center text-base shrink-0">
        <i class="fas fa-circle-check"></i>
      </div>
      <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Fulfilled Deals</span>
        <span class="text-lg font-black text-purple-600">{{ number_format($metrics['fulfilled']) }}</span>
      </div>
    </div>

  </div>

  <!-- ============================================================
       SEARCH & FILTER MATRIX
  ============================================================ -->
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
      
      <!-- 1. SEARCH INPUT -->
      <div class="lg:col-span-4 relative">
        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input
          type="text"
          wire:model.live.debounce.300ms="search"
          placeholder="Search topic title, body, author, phone, brand..."
          class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-pp-500 focus:ring-1 focus:ring-pp-500 transition"
        >
      </div>

      <!-- 2. INTENT TYPE FILTER -->
      <div class="lg:col-span-2">
        <select
          wire:model.live="typeFilter"
          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-pp-500 focus:ring-1 focus:ring-pp-500 transition"
        >
          <option value="">All Intent Types</option>
          <option value="item">Products &amp; Parts</option>
          <option value="service">Repairs &amp; Services</option>
          <option value="advice">Questions &amp; Advice</option>
        </select>
      </div>

      <!-- 3. CATEGORY FILTER -->
      <div class="lg:col-span-2">
        <select
          wire:model.live="categoryFilter"
          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-pp-500 focus:ring-1 focus:ring-pp-500 transition"
        >
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- 4. MODERATION STATUS FILTER -->
      <div class="lg:col-span-2">
        <select
          wire:model.live="moderationFilter"
          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-pp-500 focus:ring-1 focus:ring-pp-500 transition"
        >
          <option value="">All Moderation</option>
          <option value="pending">⏳ Pending Review</option>
          <option value="approved">✓ Approved / Live</option>
          <option value="rejected">✕ Rejected</option>
        </select>
      </div>

      <!-- 5. SORT ORDER -->
      <div class="lg:col-span-2">
        <select
          wire:model.live="sort"
          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-pp-500 focus:ring-1 focus:ring-pp-500 transition"
        >
          <option value="latest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="offers_desc">Most Offers</option>
          <option value="responses_desc">Most Replies</option>
          <option value="budget_desc">Highest Budget</option>
        </select>
      </div>

    </div>

    <!-- SECONDARY FILTER ROW: Status & Active Filter Badges -->
    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 text-xs">
      <div class="flex flex-wrap items-center gap-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Lifecycle:</span>
        <button
          wire:click="$set('statusFilter', '')"
          class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $statusFilter === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          All
        </button>
        <button
          wire:click="$set('statusFilter', 'open')"
          class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $statusFilter === 'open' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
        >
          Open
        </button>
        <button
          wire:click="$set('statusFilter', 'fulfilled')"
          class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $statusFilter === 'fulfilled' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}"
        >
          Fulfilled
        </button>
        <button
          wire:click="$set('statusFilter', 'closed')"
          class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $statusFilter === 'closed' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          Closed
        </button>
      </div>

      @if($search !== '' || $typeFilter !== '' || $categoryFilter !== '' || $moderationFilter !== '' || $statusFilter !== '' || $sort !== 'latest')
        <button
          wire:click="resetFilters"
          class="text-xs font-bold text-pp-600 hover:text-pp-800 flex items-center gap-1 cursor-pointer"
        >
          <i class="fas fa-rotate-left"></i>
          <span>Reset All Filters</span>
        </button>
      @endif
    </div>
  </div>

  <!-- ============================================================
       DATA TABLE CONTAINER
  ============================================================ -->
  <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
        <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
          <tr>
            <th class="py-3.5 px-4 min-w-[300px]">Request / Topic Details</th>
            <th class="py-3.5 px-4 whitespace-nowrap">Author / Member</th>
            <th class="py-3.5 px-4 whitespace-nowrap">Target Device &amp; Category</th>
            <th class="py-3.5 px-4 whitespace-nowrap">Budget &amp; Logistics</th>
            <th class="py-3.5 px-4 text-center whitespace-nowrap">Engagement</th>
            <th class="py-3.5 px-4 whitespace-nowrap">Moderation &amp; State</th>
            <th class="py-3.5 px-4 text-right whitespace-nowrap">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
          @forelse($discussions as $disc)
            @php
              $latestMod = $disc->latestModeration;
              $modStatus = $latestMod?->status ?? 'pending';
              $isPinned = $disc->is_pinned;
              $isLocked = $disc->is_locked;
              $typeLabel = match ($disc->type) {
                'service' => 'Repair / Service',
                'advice' => 'Question / Advice',
                default => 'Product / Part',
              };
              $typeBadgeClass = match ($disc->type) {
                'service' => 'bg-amber-50 text-amber-700 border-amber-200',
                'advice' => 'bg-purple-50 text-purple-700 border-purple-200',
                default => 'bg-blue-50 text-blue-700 border-blue-200',
              };
              $mediaCount = $disc->media->count();
              if ($mediaCount === 0 && !empty($disc->attachments['photos'])) {
                $mediaCount = count($disc->attachments['photos']);
              }
            @endphp
            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition group">
              
              <!-- 1. REQUEST / TOPIC DETAILS -->
              <td class="py-3.5 px-4 min-w-[300px]">
                <div class="flex items-start gap-2.5">
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 flex-wrap mb-1">
                      <span class="font-mono text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                        #REQ-{{ $disc->id }}
                      </span>
                      @if($isPinned)
                        <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 text-[9px] font-black" title="Pinned Topic">
                          <i class="fas fa-thumbtack text-[8px]"></i> PINNED
                        </span>
                      @endif
                      @if($isLocked)
                        <span class="px-1.5 py-0.2 rounded bg-slate-200 text-slate-700 text-[9px] font-black" title="Locked Thread">
                          <i class="fas fa-lock text-[8px]"></i> LOCKED
                        </span>
                      @endif
                      <span class="text-[10px] text-slate-400 font-semibold">
                        {{ $disc->created_at->diffForHumans() }}
                      </span>
                    </div>

                    <a
                      href="{{ route('admin.discussions.view', $disc->id) }}"
                      class="font-black text-slate-900 group-hover:text-pp-600 transition text-left line-clamp-2 leading-snug block"
                    >
                      {{ $disc->title }}
                    </a>

                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                      {{ Str::limit(strip_tags($disc->body), 80) }}
                    </p>

                    @if($mediaCount > 0)
                      <div class="mt-1 flex items-center gap-1 text-[10px] text-slate-400 font-bold">
                        <i class="fas fa-paperclip text-slate-400"></i>
                        <span>{{ $mediaCount }} {{ Str::plural('attachment', $mediaCount) }}</span>
                      </div>
                    @endif
                  </div>
                </div>
              </td>

              <!-- 2. AUTHOR / MEMBER -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-pp-100 text-pp-700 font-extrabold text-xs grid place-items-center shrink-0 border border-pp-200">
                    {{ strtoupper(substr($disc->user?->name ?? 'U', 0, 2)) }}
                  </div>
                  <div>
                    <div class="flex items-center gap-1.5">
                      <a href="{{ route('admin.users.show', $disc->user_id) }}" class="font-extrabold text-slate-900 hover:text-pp-600 transition">
                        {{ $disc->user?->name ?? 'Anonymous' }}
                      </a>
                      @if($disc->user?->is_verified)
                        <span class="text-pp-600 text-[10px]" title="Verified Member">✓</span>
                      @endif
                    </div>
                    <span class="text-[10px] text-slate-400 block">{{ $disc->user?->email }}</span>
                    <span class="text-[10px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                      <i class="fas fa-map-marker-alt text-slate-400 text-[9px]"></i>
                      {{ Str::limit($disc->location_text, 20) }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- 3. TARGET DEVICE & CATEGORY -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="space-y-1">
                  <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px] uppercase tracking-wider border {{ $typeBadgeClass }} inline-block">
                    {{ $typeLabel }}
                  </span>
                  <div class="text-[11px] font-bold text-slate-800">
                    @if($disc->brand || $disc->deviceModel)
                      <span>{{ $disc->brand?->name }}</span>
                      @if($disc->deviceModel)
                        <span class="text-slate-400">·</span>
                        <span>{{ $disc->deviceModel->name }}</span>
                      @endif
                    @else
                      <span class="text-slate-400 font-normal">General Specification</span>
                    @endif
                  </div>
                  @if($disc->category)
                    <span class="text-[10px] text-slate-500 font-medium block">
                      {{ $disc->category->name }}
                    </span>
                  @endif
                </div>
              </td>

              <!-- 4. BUDGET & LOGISTICS -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="space-y-1">
                  @if(!empty($disc->budget))
                    <div class="font-black text-slate-900 text-xs">
                      {{ is_numeric($disc->budget) ? '₦' . number_format((float) $disc->budget) : $disc->budget }}
                    </div>
                  @else
                    <div class="text-[11px] font-semibold text-slate-400 italic">
                      Open / Negotiable
                    </div>
                  @endif

                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold text-[10px]">
                      <i class="fas fa-truck text-[9px] text-slate-400 mr-0.5"></i>
                      {{ $disc->fulfillment }}
                    </span>
                    @if($disc->urgency && $disc->urgency !== 'Flexible')
                      <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold text-[10px]">
                        <i class="fas fa-bolt text-[9px] text-amber-500 mr-0.5"></i>
                        {{ $disc->urgency }}
                      </span>
                    @endif
                  </div>
                </div>
              </td>

              <!-- 5. ENGAGEMENT -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <div class="inline-flex flex-col items-center gap-1">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[10px]" title="Commercial Offers Received">
                      <i class="fas fa-handshake mr-1"></i> {{ $disc->offers_count }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]" title="Community Replies">
                      <i class="fas fa-comments mr-1"></i> {{ $disc->responses_count }}
                    </span>
                  </div>
                  @if(!empty($disc->attachments['views']))
                    <span class="text-[10px] text-slate-400">
                      {{ number_format($disc->attachments['views']) }} views
                    </span>
                  @endif
                </div>
              </td>

              <!-- 6. MODERATION & STATE -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="space-y-1">
                  <!-- Moderation Status -->
                  <div>
                    @if($modStatus === 'approved')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-extrabold text-[10px]">
                        <i class="fas fa-check-circle text-[9px]"></i> Approved
                      </span>
                    @elseif($modStatus === 'rejected')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-extrabold text-[10px]" title="{{ $latestMod?->reason }}">
                        <i class="fas fa-times-circle text-[9px]"></i> Rejected
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-extrabold text-[10px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                        Pending Review
                      </span>
                    @endif
                  </div>

                  <!-- Lifecycle Status -->
                  <div class="text-[10px]">
                    <span class="font-bold uppercase tracking-wider {{ $disc->status === 'open' ? 'text-emerald-600' : ($disc->status === 'fulfilled' ? 'text-purple-600' : 'text-slate-400') }}">
                      ● {{ $disc->status ?? 'open' }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- 7. ACTIONS -->
              <td class="py-3.5 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  
                  <!-- Full Review View Button -->
                  <a
                    href="{{ route('admin.discussions.view', $disc->id) }}"
                    class="px-2.5 py-1.5 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition flex items-center gap-1"
                    title="Inspect Full Discussion Details Page"
                  >
                    <i class="fas fa-eye text-[11px]"></i>
                    <span>Inspect</span>
                  </a>

                  <!-- Quick Approve Button -->
                  @if($modStatus !== 'approved')
                    <button
                      wire:click="approve({{ $disc->id }})"
                      class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition grid place-items-center cursor-pointer"
                      title="Quick Approve"
                    >
                      <i class="fas fa-check text-xs"></i>
                    </button>
                  @endif

                  <!-- Quick Reject Modal Trigger -->
                  @if($modStatus !== 'rejected')
                    <button
                      wire:click="openRejectModal({{ $disc->id }})"
                      class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 transition grid place-items-center cursor-pointer"
                      title="Reject with Reason"
                    >
                      <i class="fas fa-ban text-xs"></i>
                    </button>
                  @endif

                  <!-- Pin Toggle -->
                  <button
                    wire:click="togglePin({{ $disc->id }})"
                    class="w-7 h-7 rounded-lg {{ $isPinned ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 hover:bg-slate-200 text-slate-500' }} transition grid place-items-center cursor-pointer"
                    title="{{ $isPinned ? 'Unpin' : 'Pin to Top' }}"
                  >
                    <i class="fas fa-thumbtack text-[11px]"></i>
                  </button>

                  <!-- External Marketplace Link -->
                  <a
                    href="{{ route('community.request', ['id' => $disc->id]) }}"
                    target="_blank"
                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition grid place-items-center"
                    title="View on Community Hub"
                  >
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                  </a>

                  <!-- Delete -->
                  <button
                    wire:confirm="Are you sure you want to permanently delete this discussion request?"
                    wire:click="deleteDiscussion({{ $disc->id }})"
                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 transition grid place-items-center cursor-pointer"
                    title="Delete Request"
                  >
                    <i class="fas fa-trash-alt text-xs"></i>
                  </button>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-12 text-center text-slate-400">
                <i class="fas fa-inbox text-3xl text-slate-300 mb-2 block"></i>
                <p class="font-extrabold text-sm text-slate-700">No discussion requests found</p>
                <p class="text-xs text-slate-400 mt-0.5">Try relaxing your search keywords or active filters.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- TABLE PAGINATION FOOTER -->
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900">
      <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
        Showing <strong class="text-slate-900 dark:text-white">{{ $discussions->firstItem() ?? 0 }}</strong> to <strong class="text-slate-900 dark:text-white">{{ $discussions->lastItem() ?? 0 }}</strong> of <strong class="text-slate-900 dark:text-white">{{ $discussions->total() }}</strong> discussion requests
      </div>
      <div>
        {{ $discussions->links() }}
      </div>
    </div>
  </div>

  <!-- ============================================================
       FULL DISCUSSION & MODERATION REVIEW MODAL
  ============================================================ -->
  @if($selectedDiscussion)
    @php
      $modalMod = $selectedDiscussion->latestModeration;
      $modalModStatus = $modalMod?->status ?? 'pending';
      $modalIsPinned = $selectedDiscussion->is_pinned;
      $modalIsLocked = $selectedDiscussion->is_locked;
      $modalMedia = $selectedDiscussion->media;
    @endphp
    <div class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <!-- MODAL HEADER -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-4 bg-slate-50/50 shrink-0">
          <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <span class="font-mono text-xs font-black px-2 py-0.5 rounded-md bg-pp-100 text-pp-800">
                #REQ-{{ $selectedDiscussion->id }}
              </span>
              
              <!-- Moderation Badge -->
              @if($modalModStatus === 'approved')
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">
                  ✓ Approved
                </span>
              @elseif($modalModStatus === 'rejected')
                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px]">
                  ✕ Rejected
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px] animate-pulse">
                  ⏳ Pending Moderation
                </span>
              @endif

              <!-- Status Badge -->
              <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 font-extrabold text-[10px] uppercase">
                {{ $selectedDiscussion->status ?? 'open' }}
              </span>

              @if($modalIsPinned)
                <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-black text-[10px]">
                  <i class="fas fa-thumbtack text-[9px]"></i> Pinned
                </span>
              @endif

              @if($modalIsLocked)
                <span class="px-2 py-0.5 rounded-full bg-slate-300 text-slate-900 font-black text-[10px]">
                  <i class="fas fa-lock text-[9px]"></i> Locked
                </span>
              @endif
            </div>

            <h2 class="text-base sm:text-lg font-black text-slate-950 truncate leading-tight">
              {{ $selectedDiscussion->title }}
            </h2>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <a
              href="{{ route('community.request', ['id' => $selectedDiscussion->id]) }}"
              target="_blank"
              class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-pp-300 hover:bg-pp-50 text-slate-700 font-extrabold text-xs transition flex items-center gap-1.5"
            >
              <i class="fas fa-external-link-alt text-slate-400"></i>
              <span>Public View</span>
            </a>
            <button
              wire:click="closeDiscussion"
              class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 transition grid place-items-center cursor-pointer"
            >
              <i class="fas fa-times text-sm"></i>
            </button>
          </div>
        </div>

        <!-- MODAL NAVIGATION TABS -->
        <div class="px-6 border-b border-slate-200 bg-white shrink-0">
          <nav class="flex space-x-6">
            <button
              wire:click="setModalTab('details')"
              class="py-3 font-extrabold text-xs border-b-2 transition cursor-pointer {{ $activeModalTab === 'details' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
              <i class="fas fa-info-circle mr-1"></i> Request Details &amp; Media
            </button>
            <button
              wire:click="setModalTab('offers')"
              class="py-3 font-extrabold text-xs border-b-2 transition cursor-pointer {{ $activeModalTab === 'offers' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
              <i class="fas fa-handshake mr-1"></i> Vendor Offers ({{ $selectedDiscussion->offers->count() }})
            </button>
            <button
              wire:click="setModalTab('replies')"
              class="py-3 font-extrabold text-xs border-b-2 transition cursor-pointer {{ $activeModalTab === 'replies' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
              <i class="fas fa-comments mr-1"></i> Community Replies ({{ $selectedDiscussion->responses->count() }})
            </button>
            <button
              wire:click="setModalTab('audit')"
              class="py-3 font-extrabold text-xs border-b-2 transition cursor-pointer {{ $activeModalTab === 'audit' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
              <i class="fas fa-history mr-1"></i> Moderation Audit ({{ $selectedDiscussion->moderations->count() }})
            </button>
          </nav>
        </div>

        <!-- MODAL SCROLLABLE CONTENT BODY -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
          
          <!-- TAB 1: OVERVIEW & CONTENT -->
          @if($activeModalTab === 'details')
            
            <!-- REQUESTER SUMMARY CARD -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-pp-600 text-white font-black text-sm grid place-items-center shrink-0">
                  {{ strtoupper(substr($selectedDiscussion->user?->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="font-black text-slate-950 text-sm">{{ $selectedDiscussion->user?->name }}</span>
                    @if($selectedDiscussion->user?->is_verified)
                      <span class="px-2 py-0.2 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">✓ Verified</span>
                    @endif
                  </div>
                  <div class="text-slate-500 text-[11px] mt-0.5">
                    <span>{{ $selectedDiscussion->user?->email }}</span>
                    @if($selectedDiscussion->user?->phone)
                      <span class="mx-1">·</span>
                      <span>{{ $selectedDiscussion->user?->phone }}</span>
                    @endif
                  </div>
                  <div class="text-slate-400 text-[10px] mt-0.5 flex items-center gap-1">
                    <i class="fas fa-map-pin text-slate-400"></i>
                    <span>{{ $selectedDiscussion->location_text }}</span>
                    <span class="mx-1">·</span>
                    <span>Joined {{ $selectedDiscussion->user?->created_at?->format('M Y') }}</span>
                  </div>
                </div>
              </div>

              <div class="shrink-0 flex items-center gap-2">
                <a
                  href="{{ route('admin.users.show', $selectedDiscussion->user_id) }}"
                  target="_blank"
                  class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-pp-300 text-slate-700 font-extrabold text-xs transition flex items-center gap-1.5 shadow-2xs"
                >
                  <i class="fas fa-user-gear text-pp-600"></i>
                  <span>Manage User</span>
                </a>
              </div>
            </div>

            <!-- KEY SPECIFICATION METRICS GRID -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Intent Type</span>
                <span class="font-extrabold text-slate-900 mt-0.5 block">
                  {{ match($selectedDiscussion->type) { 'service' => 'Repair / Service', 'advice' => 'Question / Advice', default => 'Product / Part' } }}
                </span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Category &amp; Device</span>
                <span class="font-extrabold text-slate-900 mt-0.5 block truncate">
                  {{ $selectedDiscussion->category?->name ?? 'General' }}
                  @if($selectedDiscussion->brand) ({{ $selectedDiscussion->brand->name }}) @endif
                </span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Buyer Budget</span>
                <span class="font-extrabold text-slate-900 mt-0.5 block">
                  {{ !empty($selectedDiscussion->budget) ? (is_numeric($selectedDiscussion->budget) ? '₦' . number_format((float) $selectedDiscussion->budget) : $selectedDiscussion->budget) : 'Open / Flexible' }}
                </span>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Fulfillment / Urgency</span>
                <span class="font-extrabold text-slate-900 mt-0.5 block truncate">
                  {{ $selectedDiscussion->fulfillment }} · {{ $selectedDiscussion->urgency }}
                </span>
              </div>
            </div>

            <!-- FULL DISCUSSION BODY CONTENT -->
            <div class="space-y-2">
              <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider">Topic Content &amp; Specifications</h4>
              <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed whitespace-pre-line text-xs font-medium">
                {{ $selectedDiscussion->body }}
              </div>
            </div>

            <!-- ATTACHED MEDIA GALLERY -->
            @if($modalMedia->count() > 0 || !empty($selectedDiscussion->attachments['photos']))
              <div class="space-y-2">
                <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider flex items-center justify-between">
                  <span>Attached Media Files</span>
                  <span class="text-[10px] font-bold text-slate-400">{{ $modalMedia->count() }} Files</span>
                </h4>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  @foreach($modalMedia as $m)
                    <div class="relative group rounded-xl border border-slate-200 overflow-hidden bg-slate-100 aspect-square">
                      @if(str_contains($m->mime_type ?? '', 'image'))
                        <img src="{{ $m->getUrl() }}" alt="{{ $m->file_name }}" class="w-full h-full object-cover">
                        <a href="{{ $m->getUrl() }}" target="_blank" class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                          <i class="fas fa-search-plus"></i> View Full
                        </a>
                      @elseif(str_contains($m->mime_type ?? '', 'video'))
                        <div class="w-full h-full bg-slate-900 flex flex-col items-center justify-center text-white p-2 text-center">
                          <i class="fas fa-video text-2xl text-amber-400 mb-1"></i>
                          <span class="text-[9px] truncate w-full">{{ $m->file_name }}</span>
                          <a href="{{ $m->getUrl() }}" target="_blank" class="text-pp-400 underline text-[10px] mt-1">Play Video</a>
                        </div>
                      @else
                        <div class="w-full h-full bg-rose-50 flex flex-col items-center justify-center text-rose-800 p-2 text-center">
                          <i class="fas fa-file-pdf text-2xl text-rose-600 mb-1"></i>
                          <span class="text-[9px] font-bold truncate w-full">{{ $m->file_name }}</span>
                          <a href="{{ $m->getUrl() }}" target="_blank" class="text-rose-700 underline text-[10px] mt-1">Download Doc</a>
                        </div>
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

          @endif

          <!-- TAB 2: VENDOR OFFERS -->
          @if($activeModalTab === 'offers')
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                  Commercial Offers Submitted by Vendors ({{ $selectedDiscussion->offers->count() }})
                </h4>
              </div>

              @forelse($selectedDiscussion->offers as $offer)
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3">
                  <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2.5">
                      <div class="w-8 h-8 rounded-full bg-pp-50 text-pp-700 font-black text-xs grid place-items-center border border-pp-200">
                        {{ strtoupper(substr($offer->sender?->name ?? 'V', 0, 2)) }}
                      </div>
                      <div>
                        <div class="flex items-center gap-1.5">
                          <span class="font-black text-slate-900 text-xs">{{ $offer->sender?->name }}</span>
                          @if($offer->sender?->is_verified)
                            <span class="text-pp-600 text-[10px]">✓</span>
                          @endif
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $offer->created_at->diffForHumans() }}</span>
                      </div>
                    </div>

                    <div class="flex items-center gap-2">
                      <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $offer->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($offer->status === 'declined' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $offer->status }}
                      </span>
                      @if($offer->invoice)
                        <span class="px-2 py-0.5 rounded-full bg-pp-100 text-pp-800 font-extrabold text-[10px]">
                          Invoice #{{ $offer->invoice->id }}
                        </span>
                      @endif
                    </div>
                  </div>

                  <!-- Offer Items / Specifications -->
                  <div class="space-y-1.5">
                    @if($offer->items->count() > 0)
                      <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1">
                        @foreach($offer->items as $item)
                          <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $item->name ?? 'Item/Part' }} (x{{ $item->quantity }})</span>
                            <span class="font-black text-slate-950">₦{{ number_format($item->price) }}</span>
                          </div>
                        @endforeach
                      </div>
                    @endif

                    @if(!empty($offer->terms))
                      <p class="text-[11px] text-slate-600 italic">
                        "{{ $offer->terms }}"
                      </p>
                    @endif

                    <div class="flex items-center gap-3 text-[10px] text-slate-500 pt-1">
                      <span><i class="fas fa-truck text-slate-400 mr-1"></i> Delivery: {{ $offer->delivery_method ?? 'Pickup / Delivery' }}</span>
                      @if($offer->expires_at)
                        <span><i class="far fa-clock text-slate-400 mr-1"></i> Expires: {{ $offer->expires_at->format('M d, Y') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              @empty
                <div class="py-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-200">
                  <i class="fas fa-handshake-slash text-2xl text-slate-300 mb-2 block"></i>
                  <p class="font-extrabold text-xs text-slate-700">No vendor offers received yet</p>
                  <p class="text-[11px] text-slate-400">Merchants and technicians will submit structured quotes here.</p>
                </div>
              @endforelse
            </div>
          @endif

          <!-- TAB 3: COMMUNITY REPLIES -->
          @if($activeModalTab === 'replies')
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                  Community Replies &amp; Forum Advice ({{ $selectedDiscussion->responses->count() }})
                </h4>
              </div>

              @forelse($selectedDiscussion->responses as $reply)
                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-2">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-black text-[10px] grid place-items-center">
                        {{ strtoupper(substr($reply->user?->name ?? 'M', 0, 1)) }}
                      </div>
                      <span class="font-black text-slate-900 text-xs">{{ $reply->user?->name }}</span>
                      <span class="text-[10px] text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>

                    <button
                      wire:confirm="Remove this reply from the discussion?"
                      wire:click="deleteResponse({{ $reply->id }})"
                      class="text-rose-600 hover:text-rose-800 text-[10px] font-bold cursor-pointer"
                      title="Remove Reply"
                    >
                      <i class="fas fa-trash-alt mr-1"></i> Remove
                    </button>
                  </div>

                  <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line pl-8">
                    {{ $reply->body ?? $reply->content }}
                  </p>
                </div>
              @empty
                <div class="py-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-200">
                  <i class="fas fa-comment-slash text-2xl text-slate-300 mb-2 block"></i>
                  <p class="font-extrabold text-xs text-slate-700">No replies yet</p>
                  <p class="text-[11px] text-slate-400">Community answers and advice will appear here.</p>
                </div>
              @endforelse
            </div>
          @endif

          <!-- TAB 4: MODERATION AUDIT -->
          @if($activeModalTab === 'audit')
            <div class="space-y-3">
              <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                Moderation History &amp; Decision Trail
              </h4>

              @forelse($selectedDiscussion->moderations as $modLog)
                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-start justify-between gap-3">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $modLog->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($modLog->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $modLog->status }}
                      </span>
                      <span class="font-bold text-slate-800 text-xs">Action: {{ $modLog->action ?? 'moderated' }}</span>
                    </div>
                    @if(!empty($modLog->reason))
                      <p class="text-xs text-slate-600 mt-1 italic bg-slate-50 p-2 rounded-lg border border-slate-100">
                        "{{ $modLog->reason }}"
                      </p>
                    @endif
                    <div class="text-[10px] text-slate-400 mt-1">
                      Moderated by: <strong>{{ $modLog->moderator?->name ?? 'System / Admin' }}</strong> · {{ $modLog->created_at->format('M d, Y H:i') }}
                    </div>
                  </div>
                </div>
              @empty
                <div class="py-8 text-center text-slate-400 bg-slate-50 rounded-xl">
                  No previous audit actions recorded.
                </div>
              @endforelse
            </div>
          @endif

        </div>

        <!-- MODAL BOTTOM STICKY ADMIN ACTION BAR -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
          
          <div class="flex items-center gap-2 flex-wrap">
            <!-- Pin Button -->
            <button
              wire:click="togglePin({{ $selectedDiscussion->id }})"
              class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 font-extrabold text-xs text-slate-700 transition cursor-pointer flex items-center gap-1.5"
            >
              <i class="fas fa-thumbtack {{ $modalIsPinned ? 'text-amber-500' : 'text-slate-400' }}"></i>
              <span>{{ $modalIsPinned ? 'Unpin' : 'Pin to Top' }}</span>
            </button>

            <!-- Lock Button -->
            <button
              wire:click="toggleLock({{ $selectedDiscussion->id }})"
              class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 font-extrabold text-xs text-slate-700 transition cursor-pointer flex items-center gap-1.5"
            >
              <i class="fas fa-lock {{ $modalIsLocked ? 'text-rose-500' : 'text-slate-400' }}"></i>
              <span>{{ $modalIsLocked ? 'Unlock Thread' : 'Lock Thread' }}</span>
            </button>

            <!-- Change Lifecycle Status Dropdown -->
            <div class="flex items-center gap-1 text-xs">
              <span class="text-slate-400 font-bold text-[11px] uppercase">Lifecycle:</span>
              <select
                wire:change="changeStatus({{ $selectedDiscussion->id }}, $event.target.value)"
                class="px-2 py-1 rounded-lg border border-slate-200 bg-white font-bold text-xs text-slate-800 focus:outline-none"
              >
                <option value="open" {{ $selectedDiscussion->status === 'open' ? 'selected' : '' }}>Open</option>
                <option value="fulfilled" {{ $selectedDiscussion->status === 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                <option value="closed" {{ $selectedDiscussion->status === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-2">
            
            <!-- Quick Approve -->
            @if($modalModStatus !== 'approved')
              <button
                wire:click="approve({{ $selectedDiscussion->id }})"
                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer"
              >
                <i class="fas fa-check"></i>
                <span>Approve Request</span>
              </button>
            @endif

            <!-- Reject with Reason -->
            @if($modalModStatus !== 'rejected')
              <button
                wire:click="openRejectModal({{ $selectedDiscussion->id }})"
                class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer"
              >
                <i class="fas fa-ban"></i>
                <span>Reject</span>
              </button>
            @endif

            <!-- Delete -->
            <button
              wire:confirm="Permanently delete this entire discussion and its responses?"
              wire:click="deleteDiscussion({{ $selectedDiscussion->id }})"
              class="px-3 py-2 rounded-xl bg-slate-200 hover:bg-rose-600 hover:text-white text-slate-600 font-extrabold text-xs transition cursor-pointer"
              title="Delete Thread"
            >
              <i class="fas fa-trash-alt"></i>
            </button>

            <!-- Close Modal -->
            <button
              wire:click="closeDiscussion"
              class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-extrabold text-xs transition cursor-pointer"
            >
              Close
            </button>
          </div>

        </div>

      </div>
    </div>
  @endif

  <!-- ============================================================
       REJECTION REASON MODAL
  ============================================================ -->
  @if($showRejectModal)
    <div class="fixed inset-0 z-60 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 grid place-items-center text-sm">
              <i class="fas fa-ban"></i>
            </div>
            <div>
              <h3 class="font-black text-slate-950 text-sm">Reject Discussion Request</h3>
              <p class="text-[11px] text-slate-400">Request #REQ-{{ $selectedDiscussionIdForReject }}</p>
            </div>
          </div>
          <button wire:click="closeRejectModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Presets -->
        <div class="space-y-1.5">
          <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider block">Standard Violation Reason</label>
          <select
            wire:model.live="presetReason"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-pp-500"
          >
            <option value="">Choose standard reason or write custom below...</option>
            @foreach($standardReasons as $title => $desc)
              <option value="{{ $desc }}">{{ $title }}</option>
            @endforeach
          </select>
        </div>

        <!-- Explanation Textarea -->
        <div class="space-y-1.5">
          <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider block">Detailed Rejection Explanation (Required)</label>
          <textarea
            wire:model="rejectionReason"
            rows="4"
            placeholder="Provide a clear explanation for the requester on why their topic was flagged or rejected..."
            class="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500"
          ></textarea>
          @error('rejectionReason')
            <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
          <button
            wire:click="closeRejectModal"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            wire:click="submitReject"
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs transition shadow-xs cursor-pointer flex items-center gap-1.5"
          >
            <i class="fas fa-ban"></i>
            <span>Confirm Rejection</span>
          </button>
        </div>

      </div>
    </div>
  @endif

</div>
