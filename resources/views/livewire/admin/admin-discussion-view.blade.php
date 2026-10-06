<div class="flex flex-col gap-6">

  @php
    $latestMod = $discussion->latestModeration;
    $modStatus = $latestMod?->status ?? 'pending';
    $isPinned = $discussion->is_pinned;
    $isLocked = $discussion->is_locked;
    $typeLabel = match ($discussion->type) {
        'service' => 'Repair / Service',
        'advice' => 'Question / Advice',
        default => 'Product / Part',
    };
    $typeBadgeClass = match ($discussion->type) {
        'service' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        'advice' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
        default => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
    };

    $author = $discussion->user;
  @endphp

  <!-- ============================================================
       TOP BREADCRUMB & ADMIN COMMAND BAR
  ============================================================ -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1.5 text-xs font-bold text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.discussions') }}" class="text-pp-600 dark:text-pp-400 hover:underline flex items-center gap-1 font-extrabold">
          <i class="fas fa-arrow-left text-[10px]"></i> Community Discussions
        </a>
        <span>/</span>
        <span class="text-slate-400">#REQ-{{ $discussion->id }}</span>
        <span>/</span>
        <span class="text-slate-700 dark:text-slate-300 font-extrabold truncate max-w-[240px]">{{ $discussion->title }}</span>
      </div>

      <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-2xl font-black text-slate-950 dark:text-white">
          {{ $discussion->title }}
        </h1>

        <!-- MODERATION STATUS BADGE -->
        @if($modStatus === 'approved')
          <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Approved / Live
          </span>
        @elseif($modStatus === 'rejected')
          <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fas fa-ban text-[10px]"></i> Rejected by Admin
          </span>
        @else
          <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-2xs animate-pulse">
            <i class="fas fa-clock text-[10px]"></i> Pending Review
          </span>
        @endif

        <!-- LIFECYCLE BADGE -->
        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase border {{ $discussion->status === 'open' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($discussion->status === 'fulfilled' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
          ● {{ ucfirst($discussion->status ?? 'open') }}
        </span>

        @if($isPinned)
          <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase flex items-center gap-1">
            <i class="fas fa-thumbtack text-[8px]"></i> Pinned
          </span>
        @endif

        @if($isLocked)
          <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 text-[10px] font-black uppercase flex items-center gap-1">
            <i class="fas fa-lock text-[8px]"></i> Locked
          </span>
        @endif
      </div>

      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        Request ID: <code class="text-slate-800 dark:text-slate-200 font-bold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">#REQ-{{ $discussion->id }}</code>
        · Author: <strong class="text-slate-800 dark:text-slate-200">{{ $discussion->user?->name ?? 'Community Member' }}</strong>
        · Intent: <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ $typeLabel }}</span>
        · Published {{ $discussion->created_at->format('M d, Y · H:i') }} ({{ $discussion->created_at->diffForHumans() }})
      </p>
    </div>

    <!-- COMMAND BUTTONS -->
    <div class="flex items-center gap-2 flex-wrap">

      <!-- View in Community Hub (Public Link) -->
      <a
        href="{{ route('community.request', ['id' => $discussion->id]) }}"
        target="_blank"
        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-extrabold text-xs transition flex items-center gap-1.5"
        title="View on Community Hub"
      >
        <i class="fas fa-external-link-alt text-[10px]"></i>
        <span>Public View</span>
      </a>

      <!-- Pin Toggle -->
      <button
        type="button"
        wire:click="togglePin"
        class="px-3.5 py-2 rounded-xl {{ $isPinned ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer"
        title="{{ $isPinned ? 'Unpin this discussion' : 'Pin to top of feed' }}"
      >
        <i class="fas fa-thumbtack text-[11px]"></i>
        <span>{{ $isPinned ? 'Unpin' : 'Pin' }}</span>
      </button>

      <!-- Lock Toggle -->
      <button
        type="button"
        wire:click="toggleLock"
        class="px-3.5 py-2 rounded-xl {{ $isLocked ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer"
        title="{{ $isLocked ? 'Unlock replies' : 'Lock discussion from replies' }}"
      >
        <i class="fas {{ $isLocked ? 'fa-unlock' : 'fa-lock' }} text-[11px]"></i>
        <span>{{ $isLocked ? 'Unlock' : 'Lock' }}</span>
      </button>

      <!-- Delete Button -->
      <button
        type="button"
        wire:click="delete"
        wire:confirm="Are you sure you want to permanently delete this discussion request? All replies will be removed."
        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer"
      >
        <i class="fas fa-trash"></i>
        <span>Delete</span>
      </button>

    </div>
  </div>

  <!-- ============================================================
       TOP KPI RIBBON (FEATURING MODERATION STATUS & INLINE APPROVE / REJECT BUTTONS)
  ============================================================ -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

    <!-- KPI 1: MODERATION STATUS & RAPID ACTION CONTROLS (HIGH PROMINENCE ON RIBBON) -->
    <div class="bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-900/80 rounded-2xl border-2 {{ $modStatus === 'approved' ? 'border-emerald-200 shadow-emerald-50/50' : ($modStatus === 'rejected' ? 'border-rose-200 shadow-rose-50/50' : 'border-amber-300 shadow-amber-50/50') }} p-4 shadow-sm flex flex-col justify-between gap-3 xl:col-span-2">
      <div class="flex items-start justify-between gap-2">
        <div>
          <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
            Current Moderation Status
          </span>
          <div class="flex items-center gap-2">
            @if($modStatus === 'approved')
              <span class="px-2.5 py-1 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs font-black inline-flex items-center gap-1.5">
                <i class="fas fa-check-circle text-emerald-600"></i> Approved
              </span>
            @elseif($modStatus === 'rejected')
              <span class="px-2.5 py-1 rounded-lg bg-rose-100 border border-rose-300 text-rose-900 text-xs font-black inline-flex items-center gap-1.5">
                <i class="fas fa-ban text-rose-600"></i> Rejected
              </span>
            @else
              <span class="px-2.5 py-1 rounded-lg bg-amber-100 border border-amber-300 text-amber-900 text-xs font-black inline-flex items-center gap-1.5 animate-pulse">
                <i class="fas fa-clock text-amber-600"></i> Pending Review
              </span>
            @endif
          </div>
        </div>

        <!-- Moderator info caption -->
        <span class="text-[10px] text-slate-500 dark:text-slate-400 text-right leading-tight">
          @if($latestMod)
            Action by {{ $latestMod->moderator?->name ?? 'Admin' }}<br>
            <span class="text-slate-400">{{ $latestMod->created_at->diffForHumans() }}</span>
          @else
            Awaiting initial<br>moderation review
          @endif
        </span>
      </div>

      <!-- MODERATION BUTTONS ON TOP KPI RIBBON -->
      <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70 dark:border-slate-800">
        <button
          type="button"
          wire:click="approve"
          wire:confirm="Approve this community discussion and ensure it is live for members?"
          class="flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer"
          title="Approve and activate topic"
        >
          <i class="fas fa-check text-[11px]"></i>
          <span>Approve Topic</span>
        </button>

        <button
          type="button"
          wire:click="openRejectModal"
          class="flex-1 py-1.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer"
          title="Reject topic or state violations"
        >
          <i class="fas fa-ban text-[11px]"></i>
          <span>Reject / Flag</span>
        </button>
      </div>
    </div>

    <!-- KPI 2: MARKETPLACE VIEWS (ViewedEntity) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Discussion Views</span>
        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center">
          <i class="fas fa-eye text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-slate-900 dark:text-white leading-none">
          {{ number_format($viewsCount) }}
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          {{ number_format($uniqueViewers) }} registered viewers
        </p>
      </div>
    </div>

    <!-- KPI 3: COMMUNITY WATCHERS (Watchlist) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Watchers</span>
        <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center">
          <i class="fas fa-bookmark text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-indigo-700 dark:text-indigo-400 leading-none">
          {{ number_format($watchersCount) }} <span class="text-xs text-slate-500 font-bold">saved</span>
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          Bookmarked by members
        </p>
      </div>
    </div>

    <!-- KPI 4: COMMUNITY RESPONSES (Response) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Responses</span>
        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
          <i class="fas fa-comments text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-2xl font-black text-slate-900 dark:text-white leading-none">
          {{ $responsesCount }} <span class="text-xs text-slate-500 font-bold">replies</span>
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          {{ $responsesWithOffersCount }} with commercial offers
        </p>
      </div>
    </div>

    <!-- KPI 5: COMMERCIAL OFFERS (Response.Offer) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-500 mb-2">
        <span class="text-[10px] font-black uppercase tracking-wider">Vendor Quotes</span>
        <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
          <i class="fas fa-handshake text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-xl font-black text-slate-900 dark:text-white leading-none truncate" title="₦{{ number_format($totalOffersValue, 2) }}">
          {{ $totalOffersCount }} <span class="text-xs text-slate-500 font-bold">offer(s)</span>
        </div>
        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
          ₦{{ number_format($totalOffersValue, 0) }} total volume
        </p>
      </div>
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

  <!-- ============================================================
       COMMUNITY REPORTS ALERT BANNER & OPEN REPORTS PANEL (Report)
  ============================================================ -->
  @if($openReports->isNotEmpty())
    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 font-bold text-xs space-y-4 shadow-2xs">
      <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
            <i class="fas fa-flag text-base"></i>
          </div>
          <div>
            <span class="font-black text-rose-900 block text-sm">
              Discussion Flagged: {{ $openReports->count() }} Unresolved Report(s) Filed
            </span>
            <span class="text-rose-700 text-[11px] font-medium">
              Community members have flagged this discussion topic for policy violations, inappropriate content, or misleading requests.
            </span>
          </div>
        </div>
        <button
          type="button"
          wire:click="setTab('trust')"
          class="px-3.5 py-1.5 rounded-xl bg-white border border-rose-300 hover:bg-rose-100 text-rose-900 font-black text-xs transition cursor-pointer shadow-xs"
        >
          View Full Trust Audit
        </button>
      </div>

      <!-- Open Topic Reports List -->
      <div class="space-y-3 pt-2 border-t border-rose-200/60">
        <h3 class="text-xs font-black uppercase tracking-wider text-rose-950 flex items-center gap-1.5">
          <i class="fas fa-exclamation-triangle text-rose-600"></i> Topic Reports &amp; Flags
        </h3>

        @foreach($openReports as $rep)
          <div class="p-4 rounded-xl bg-white border border-rose-200 space-y-2">
            <div class="flex items-start justify-between gap-2">
              <div>
                <span class="text-xs font-black text-slate-900 block">{{ $rep->title ?: 'Discussion Violation' }}</span>
                <span class="text-[10px] text-slate-400 font-medium">
                  Reported by <strong class="text-slate-700">{{ $rep->user?->name ?? 'Community Member' }}</strong> · {{ $rep->created_at->diffForHumans() }}
                </span>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800 animate-pulse">
                Pending Review
              </span>
            </div>

            @if($rep->description)
              <p class="text-xs text-slate-700 font-normal leading-relaxed bg-slate-50/70 p-2.5 rounded-lg border border-slate-100">
                {{ $rep->description }}
              </p>
            @endif

            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 justify-end">
              <button
                type="button"
                wire:click="dismissDiscussionReport({{ $rep->id }})"
                class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer"
              >
                Dismiss Flag
              </button>
              <button
                type="button"
                wire:click="resolveDiscussionReport({{ $rep->id }})"
                class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition cursor-pointer shadow-xs"
              >
                Mark Resolved
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if($openReportedResponses->isNotEmpty())
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 font-bold text-xs flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
          <i class="fas fa-comments text-base"></i>
        </div>
        <div>
          <span class="font-black text-amber-900 text-sm block">
            Reported Responses: {{ $openReportedResponses->count() }} Community Reply(ies) Flagged
          </span>
          <span class="text-amber-800 text-[11px] font-medium">
            One or more responses in this discussion thread have been reported by users. Review and take administrative action below.
          </span>
        </div>
      </div>
      <button
        type="button"
        wire:click="setTab('responses')"
        class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs transition cursor-pointer"
      >
        View Reported Replies
      </button>
    </div>
  @endif

  <!-- ============================================================
       OPTION A: TABBED NAVIGATION BAR
  ============================================================ -->
  <div class="border-b border-slate-200 dark:border-slate-800">
    <nav class="flex items-center gap-2 overflow-x-auto pb-px">

      <!-- TAB 1: OVERVIEW & SPECS -->
      <button
        type="button"
        wire:click="setTab('overview')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'overview' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}"
      >
        <i class="fas fa-sliders-h"></i>
        <span>Overview &amp; Specs</span>
      </button>

      <!-- TAB 2: RESPONSES & COMMERCIAL OFFERS -->
      <button
        type="button"
        wire:click="setTab('responses')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'responses' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}"
      >
        <i class="fas fa-comments"></i>
        <span>Responses &amp; Offers</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $responsesCount > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
          {{ $responsesCount }}
        </span>
        @if($responsesWithOffersCount > 0)
          <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-100 text-amber-800" title="{{ $responsesWithOffersCount }} responses with offers">
            {{ $responsesWithOffersCount }} offers
          </span>
        @endif
      </button>

      <!-- TAB 3: TRUST & MODERATION -->
      <button
        type="button"
        wire:click="setTab('trust')"
        class="pb-3 px-4 text-xs font-black uppercase tracking-wider transition border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'trust' ? 'border-pp-600 text-pp-600' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}"
      >
        <i class="fas fa-shield-alt"></i>
        <span>Trust, Reports &amp; Moderation</span>
        @if($openReports->isNotEmpty() || $openReportedResponses->isNotEmpty())
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 animate-pulse">
            {{ $openReports->count() + $openReportedResponses->count() }}
          </span>
        @else
          <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
            {{ $moderations->count() }}
          </span>
        @endif
      </button>

    </nav>
  </div>

  <!-- ============================================================
       TAB CONTENTS CONTAINER
  ============================================================ -->
  <div class="mt-2">

    <!-- ============================================================
         TAB 1: OVERVIEW & SPECIFICATIONS
         (Discussion, Brand, Category, DeviceModel, Media)
    ============================================================ -->
    @if($activeTab === 'overview')
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: PRIMARY DETAILS, HARDWARE SPECS & MEDIA (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- 1. DISCUSSION SPECIFICATION & BODY CARD -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-xl text-xs font-black uppercase border {{ $typeBadgeClass }}">
                  {{ $typeLabel }}
                </span>
                @if($discussion->category)
                  <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs">
                    {{ $discussion->category->name }}
                  </span>
                @endif
              </div>

              <div class="flex items-center gap-3 text-xs text-slate-400 font-bold">
                <span title="Views count"><i class="fas fa-eye mr-1"></i> {{ number_format($viewsCount) }} views</span>
                <span title="Watchers count"><i class="fas fa-bookmark mr-1"></i> {{ number_format($watchersCount) }} watchers</span>
                <span title="Replies"><i class="fas fa-comments mr-1"></i> {{ $responsesCount }} replies</span>
              </div>
            </div>

            <!-- COMPATIBILITY & TAXONOMY SPECS GRID (Brand, Category, DeviceModel) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-0.5">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Brand / Make</span>
                <strong class="text-slate-900 dark:text-white block truncate">{{ $discussion->brand?->name ?? '—' }}</strong>
              </div>

              <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-0.5">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Device / Model</span>
                <strong class="text-slate-900 dark:text-white block truncate">{{ $discussion->deviceModel?->name ?? '—' }}</strong>
              </div>

              <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-0.5">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Category</span>
                <strong class="text-slate-900 dark:text-white block truncate">{{ $discussion->category?->name ?? 'General' }}</strong>
              </div>

              <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-0.5">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Fulfillment / Urgency</span>
                <strong class="text-slate-900 dark:text-white block truncate">
                  {{ $discussion->fulfillment }} · {{ $discussion->urgency }}
                </strong>
              </div>
            </div>

            <!-- BODY / PROBLEM STATEMENT -->
            <div class="space-y-2">
              <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Discussion Description &amp; Technical Scope:</h3>
              <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs leading-relaxed font-medium whitespace-pre-line">
                {{ $discussion->body }}
              </div>
            </div>

          </div>

          <!-- 2. ATTACHED MEDIA GALLERY: IMAGES, VIDEOS & DOCS (Media) -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-photo-film text-pp-600"></i> Attached Media &amp; Files ({{ $allMedia->count() }})
              </h3>
              <div class="flex items-center gap-2 text-xs text-slate-400 font-bold">
                <span>{{ $mediaImages->count() }} Images</span>
                <span>·</span>
                <span>{{ $mediaVideos->count() }} Videos</span>
                <span>·</span>
                <span>{{ $mediaDocs->count() }} Docs</span>
              </div>
            </div>

            @if($allMedia->isNotEmpty())
              
              <!-- 2A. IMAGES (Photos) -->
              @if($mediaImages->isNotEmpty())
                <div class="space-y-2">
                  <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Reference Photos ({{ $mediaImages->count() }})</span>
                  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($mediaImages as $mediaItem)
                      <a href="{{ $mediaItem->url }}" target="_blank" class="group relative rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-100 dark:bg-slate-800 aspect-square block">
                        <img
                          src="{{ $mediaItem->url }}"
                          alt="Reference photo"
                          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                          loading="lazy"
                        />
                        <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-black gap-1">
                          <i class="fas fa-search-plus"></i> View Image
                        </div>
                      </a>
                    @endforeach
                  </div>
                </div>
              @endif

              <!-- 2B. VIDEOS -->
              @if($mediaVideos->isNotEmpty())
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                  <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Video Footage ({{ $mediaVideos->count() }})</span>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($mediaVideos as $vid)
                      <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-3 bg-slate-50 dark:bg-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                          <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                            <i class="fas fa-video text-xs"></i>
                          </div>
                          <div>
                            <span class="text-xs font-black text-slate-900 dark:text-white block truncate max-w-[200px]">{{ $vid->file_name ?? 'Video Clip' }}</span>
                            <span class="text-[10px] text-slate-400">{{ $vid->mime_type ?? 'video/mp4' }}</span>
                          </div>
                        </div>
                        <a href="{{ $vid->url }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-pp-600 text-white font-black text-xs hover:bg-pp-700 transition">
                          Play
                        </a>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              <!-- 2C. DOCUMENTS & SCHEMATICS -->
              @if($mediaDocs->isNotEmpty())
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                  <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Technical Documents &amp; Schematics ({{ $mediaDocs->count() }})</span>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($mediaDocs as $doc)
                      <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-3 bg-slate-50 dark:bg-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                          <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <i class="fas fa-file-pdf text-xs"></i>
                          </div>
                          <div>
                            <span class="text-xs font-black text-slate-900 dark:text-white block truncate max-w-[200px]">{{ $doc->file_name ?? 'Specification Document' }}</span>
                            <span class="text-[10px] text-slate-400">{{ $doc->mime_type ?? 'document' }}</span>
                          </div>
                        </div>
                        <a href="{{ $doc->url }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 font-black text-xs transition">
                          Open
                        </a>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

            @else
              <div class="p-8 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 text-center">
                <i class="fas fa-photo-film text-3xl text-slate-300 dark:text-slate-600 mb-2"></i>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-400">No media attachments uploaded</p>
                <p class="text-[11px] text-slate-400">Author did not attach reference photos, videos, or schematics.</p>
              </div>
            @endif
          </div>

        </div>

        <!-- RIGHT COLUMN: BUDGET, LIFECYCLE & LOCATION (1 COL) -->
        <div class="space-y-6">

          <!-- 1. BUDGET & COMMERCIAL VALUATION -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
              <i class="fas fa-wallet text-pp-600"></i> Budget &amp; Commercial Terms
            </h3>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 space-y-1">
              <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Author Budget</span>
              <div class="text-2xl font-black text-slate-950 dark:text-white">
                {{ !empty($discussion->budget) ? (is_numeric($discussion->budget) ? '₦' . number_format((float) $discussion->budget, 2) : $discussion->budget) : 'Open / Negotiable' }}
              </div>
            </div>

            <div class="space-y-2 text-xs font-medium">
              <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                <span>Fulfillment Type:</span>
                <strong class="text-slate-900 dark:text-white">{{ $discussion->fulfillment }}</strong>
              </div>
              <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                <span>Urgency Level:</span>
                <strong class="text-slate-900 dark:text-white">{{ $discussion->urgency }}</strong>
              </div>
              <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                <span>Total Proposals:</span>
                <strong class="text-emerald-600 font-extrabold">{{ $totalOffersCount }} offers submitted</strong>
              </div>
            </div>
          </div>

          <!-- 2. LIFECYCLE CONTROLS -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
              <i class="fas fa-sync text-pp-600"></i> Lifecycle Status
            </h3>

            <div class="grid grid-cols-3 gap-2">
              <button
                type="button"
                wire:click="changeStatus('open')"
                class="py-2 px-3 rounded-xl text-xs font-black transition cursor-pointer {{ $discussion->status === 'open' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
              >
                Open
              </button>
              <button
                type="button"
                wire:click="changeStatus('fulfilled')"
                class="py-2 px-3 rounded-xl text-xs font-black transition cursor-pointer {{ $discussion->status === 'fulfilled' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
              >
                Fulfilled
              </button>
              <button
                type="button"
                wire:click="changeStatus('closed')"
                class="py-2 px-3 rounded-xl text-xs font-black transition cursor-pointer {{ $discussion->status === 'closed' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}"
              >
                Closed
              </button>
            </div>
          </div>

          <!-- 3. GEOGRAPHIC DISPATCH / LOCATION (Location) -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-3">
            <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
              <i class="fas fa-map-marker-alt text-pp-600"></i> Target Location
            </h3>

            <div class="text-xs space-y-2">
              <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                {{ $discussion->location_text }}
              </div>
              <p class="text-slate-500 font-medium">
                Country: <strong>{{ $discussion->user->country?->name ?? 'Nigeria' }}</strong>
              </p>
            </div>
          </div>

        </div>

      </div>
    @endif

    <!-- ============================================================
         TAB 2: RESPONSES & COMMERCIAL OFFERS
         (Response, Offer)
    ============================================================ -->
    @if($activeTab === 'responses')
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-6">
        
        <!-- Header & Sub-filters -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
          <div>
            <h3 class="text-base font-black text-slate-950 dark:text-white flex items-center gap-2">
              <i class="fas fa-comments text-emerald-600"></i> Community Responses &amp; Proposals
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              Review vendor responses, proposals, technical answers, and attached offers.
            </p>
          </div>

          <!-- Sub-tab filter buttons -->
          <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
            <button
              type="button"
              wire:click="setResponseTab('all')"
              class="px-3 py-1.5 rounded-xl text-xs font-black transition cursor-pointer {{ $responseTab === 'all' ? 'bg-white dark:bg-slate-900 text-slate-950 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}"
            >
              All ({{ $responsesCount }})
            </button>
            <button
              type="button"
              wire:click="setResponseTab('offers')"
              class="px-3 py-1.5 rounded-xl text-xs font-black transition cursor-pointer {{ $responseTab === 'offers' ? 'bg-white dark:bg-slate-900 text-amber-700 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}"
            >
              Offers Only ({{ $responsesWithOffersCount }})
            </button>
            <button
              type="button"
              wire:click="setResponseTab('reported')"
              class="px-3 py-1.5 rounded-xl text-xs font-black transition cursor-pointer {{ $responseTab === 'reported' ? 'bg-white dark:bg-slate-900 text-rose-700 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}"
            >
              Reported ({{ $reportedResponses->count() }})
            </button>
          </div>
        </div>

        @if($filteredResponses->isNotEmpty())
          <div class="space-y-4">
            @foreach($filteredResponses as $resp)
              @php
                $respReports = $resp->reports;
                $hasOffer = $resp->offers->isNotEmpty();
              @endphp
              <div class="p-5 rounded-2xl border {{ $respReports->isNotEmpty() ? 'border-amber-300 bg-amber-50/30 dark:bg-amber-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30' }} space-y-4">
                
                <!-- Response Header -->
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-pp-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                      {{ strtoupper(substr($resp->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                      <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ $resp->user?->name ?? 'Community Member' }}</span>
                        @if($resp->user?->is_verified)
                          <span class="text-blue-500 text-[10px]" title="Verified KYC"><i class="fas fa-check-circle"></i></span>
                        @endif
                        @if($hasOffer)
                          <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[9px] font-black uppercase">
                            <i class="fas fa-handshake mr-0.5"></i> Commercial Offer
                          </span>
                        @endif
                      </div>
                      <span class="text-[10px] text-slate-400">
                        {{ $resp->created_at->format('M d, Y · H:i') }} ({{ $resp->created_at->diffForHumans() }})
                      </span>
                    </div>
                  </div>

                  <!-- Quick Actions -->
                  <div class="flex items-center gap-1.5">
                    <button
                      type="button"
                      wire:click="deleteResponse({{ $resp->id }})"
                      wire:confirm="Permanently delete this response from the discussion?"
                      class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition cursor-pointer"
                      title="Delete reply"
                    >
                      <i class="fas fa-trash text-[10px]"></i> Delete
                    </button>
                  </div>
                </div>

                <!-- Response Body -->
                <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-medium whitespace-pre-line">
                  {{ $resp->body }}
                </div>

                <!-- COMMERCIAL OFFER ATTACHED (Response.Offer) -->
                @if($hasOffer)
                  <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3">
                    <div class="flex items-center justify-between border-b border-amber-200/80 pb-2">
                      <div class="flex items-center gap-2">
                        <i class="fas fa-handshake text-amber-600"></i>
                        <span class="text-xs font-black text-amber-950 uppercase tracking-wider">
                          Vendor Proposal &amp; Offer Details
                        </span>
                      </div>
                      <span class="text-xs font-black text-amber-900">
                        ₦{{ number_format($resp->offers->first()->total(), 2) }}
                      </span>
                    </div>

                    @foreach($resp->offers as $offer)
                      <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-[11px] font-bold text-amber-900">
                          <span>Status: <strong class="uppercase">{{ $offer->status }}</strong></span>
                          <span>Delivery: <strong>{{ $offer->delivery_method ?: 'Direct Delivery' }}</strong></span>
                        </div>

                        <!-- Offer Items -->
                        @if($offer->items->isNotEmpty())
                          <div class="space-y-1 bg-white/80 p-2.5 rounded-xl border border-amber-200/60">
                            @foreach($offer->items as $offItem)
                              <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-slate-800">{{ $offItem->description ?: 'Offered Part' }} (x{{ $offItem->quantity }})</span>
                                <span class="font-black text-slate-900">₦{{ number_format($offItem->subtotal(), 2) }}</span>
                              </div>
                            @endforeach
                          </div>
                        @endif

                        @if($offer->terms)
                          <p class="text-[11px] text-amber-900 italic">
                            Terms: "{{ $offer->terms }}"
                          </p>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif

                <!-- RESPONSE REPORT FLAGS (If any) -->
                @if($respReports->isNotEmpty())
                  <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 space-y-2">
                    <span class="text-[11px] font-black text-rose-900 block flex items-center gap-1.5">
                      <i class="fas fa-flag text-rose-600"></i> Report Filed Against This Reply
                    </span>
                    @foreach($respReports as $rReport)
                      <div class="flex items-center justify-between text-xs pt-1 border-t border-rose-200/60">
                        <div>
                          <strong class="text-rose-950">{{ $rReport->title ?: 'Inappropriate Reply' }}:</strong>
                          <span class="text-rose-800 font-medium">{{ $rReport->description }}</span>
                        </div>
                        @if($rReport->status === 'pending' || $rReport->status === 'open')
                          <div class="flex items-center gap-1 shrink-0">
                            <button
                              type="button"
                              wire:click="dismissResponseReport({{ $rReport->id }})"
                              class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 text-[10px] font-bold"
                            >
                              Dismiss
                            </button>
                            <button
                              type="button"
                              wire:click="resolveResponseReport({{ $rReport->id }})"
                              class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[10px] font-black"
                            >
                              Resolve
                            </button>
                          </div>
                        @else
                          <span class="text-[10px] font-black uppercase text-emerald-700">{{ $rReport->status }}</span>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif

              </div>
            @endforeach
          </div>
        @else
          <!-- Empty State -->
          <div class="p-12 text-center rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl">
              <i class="fas fa-comments"></i>
            </div>
            <h4 class="text-sm font-black text-slate-800 dark:text-white">No Responses Found</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
              No replies matching the selected filter currently exist for this request.
            </p>
          </div>
        @endif

      </div>
    @endif

    <!-- ============================================================
         TAB 3: TRUST, REPORTS & MODERATION AUDIT
         (Moderation, Report, User)
    ============================================================ -->
    @if($activeTab === 'trust')
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: TOPIC REPORTS & RESPONSE REPORTS (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- 1. DISCUSSION REPORTS LIST -->
          <div id="discussion-reports" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-flag text-rose-600"></i> Topic Reports &amp; Flags ({{ $discussion->reports->count() }})
              </h3>
              <span class="text-xs text-slate-400 font-semibold">Discussion Integrity</span>
            </div>

            @if($discussion->reports->isNotEmpty())
              <div class="space-y-3">
                @foreach($discussion->reports as $rep)
                  <div class="p-4 rounded-2xl border {{ $rep->status === 'resolved' ? 'bg-slate-50 dark:bg-slate-800 border-slate-200' : ($rep->status === 'dismissed' ? 'bg-slate-50/60 border-slate-200' : 'bg-rose-50/50 border-rose-200') }} space-y-2">
                    <div class="flex items-start justify-between gap-2">
                      <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black flex items-center justify-center">
                          <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                          <span class="text-xs font-black text-slate-900 dark:text-white block">{{ $rep->title ?: 'Topic Violation Report' }}</span>
                          <span class="text-[10px] text-slate-400">
                            Reported by <strong>{{ $rep->user?->name ?? 'Community Member' }}</strong> · {{ $rep->created_at->diffForHumans() }}
                          </span>
                        </div>
                      </div>

                      <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase
                        {{ $rep->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($rep->status === 'dismissed' ? 'bg-slate-200 text-slate-700' : 'bg-rose-100 text-rose-800 animate-pulse') }}">
                        {{ ucfirst($rep->status) }}
                      </span>
                    </div>

                    @if($rep->description)
                      <p class="text-xs text-slate-700 dark:text-slate-300 bg-white/80 dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200/60 leading-relaxed font-medium">
                        {{ $rep->description }}
                      </p>
                    @endif

                    @if($rep->resolution_notes)
                      <div class="text-[11px] text-slate-500 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 font-medium">
                        <strong>Resolution Notes:</strong> {{ $rep->resolution_notes }}
                        @if($rep->resolvedBy)
                          <span class="text-slate-400 block text-[10px]">Resolved by {{ $rep->resolvedBy->name }}</span>
                        @endif
                      </div>
                    @endif

                    @if($rep->status === 'pending' || $rep->status === 'open')
                      <div class="flex items-center gap-2 pt-2 border-t border-slate-200/50 justify-end">
                        <button
                          type="button"
                          wire:click="dismissDiscussionReport({{ $rep->id }})"
                          class="px-3 py-1 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer"
                        >
                          Dismiss Flag
                        </button>
                        <button
                          type="button"
                          wire:click="resolveDiscussionReport({{ $rep->id }})"
                          class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition cursor-pointer shadow-xs"
                        >
                          Mark Resolved
                        </button>
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            @else
              <div class="p-8 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 text-center">
                <i class="fas fa-shield-heart text-3xl text-emerald-400 mb-2"></i>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Zero Discussion Reports</p>
                <p class="text-[11px] text-slate-400">No community member has flagged this topic.</p>
              </div>
            @endif
          </div>

          <!-- 2. REPORTED RESPONSES ARCHIVE -->
          @if($reportedResponses->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
              <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                  <i class="fas fa-comments text-amber-500"></i> Reported Community Replies ({{ $reportedResponses->count() }})
                </h3>
              </div>

              <div class="space-y-3">
                @foreach($reportedResponses as $r)
                  <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/40 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                      <span class="font-black text-slate-900">{{ $r->user?->name ?? 'Community Member' }}'s Reply</span>
                      <button
                        type="button"
                        wire:click="deleteResponse({{ $r->id }})"
                        class="text-rose-600 hover:text-rose-800 font-bold"
                      >
                        Delete Reply
                      </button>
                    </div>
                    <p class="text-slate-700 bg-white p-2.5 rounded-xl border border-amber-100">
                      {{ $r->body }}
                    </p>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

        </div>

        <!-- RIGHT COLUMN: MODERATION AUDIT TRAIL & TOPIC AUTHOR PROFILE (1 COL) -->
        <div class="space-y-6">

          <!-- 1. MODERATION AUDIT TRAIL (Moderation) -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-history text-pp-600"></i> Moderation Audit Trail ({{ $moderations->count() }})
              </h3>
            </div>

            @if($moderations->isNotEmpty())
              <div class="space-y-3">
                @foreach($moderations as $mod)
                  <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                      <span class="px-2 py-0.5 rounded-md font-black text-[10px] uppercase
                        {{ $mod->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($mod->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700') }}">
                        {{ $mod->status }}
                      </span>
                      <span class="text-[10px] text-slate-400">{{ $mod->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">
                      {{ $mod->action ?: 'Moderation Action' }}
                    </div>

                    @if($mod->moderator)
                      <div class="text-[10px] text-slate-500 font-medium">
                        Admin: <strong>{{ $mod->moderator->name }}</strong>
                      </div>
                    @endif

                    @if($mod->reason)
                      <div class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-600 dark:text-slate-400 italic">
                        "{{ $mod->reason }}"
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-xs text-slate-400 italic">No historical moderation entries recorded yet.</p>
            @endif
          </div>

          <!-- 2. TOPIC AUTHOR ACCOUNT PROFILE (User) -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-soft space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-user-circle text-pp-600"></i> Request Author Profile
              </h3>
              @if($author?->is_verified)
                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-black uppercase">
                  Verified KYC
                </span>
              @endif
            </div>

            @if($author)
              <div class="space-y-3 text-xs">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-pp-600 text-white font-black text-sm flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($author->name, 0, 1)) }}
                  </div>
                  <div>
                    <span class="font-black text-slate-950 dark:text-white text-sm block">{{ $author->name }}</span>
                    <span class="text-slate-400 text-[11px]">{{ $author->email }}</span>
                  </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5 font-medium text-slate-600 dark:text-slate-400">
                  <div class="flex justify-between">
                    <span>Phone:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $author->phone ?: 'Not provided' }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Country:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $author->country?->name ?? 'Nigeria' }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Location:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $discussion->location_text }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Member Since:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $author->created_at->format('M Y') }}</strong>
                  </div>
                  <div class="flex justify-between">
                    <span>Total Topics:</span>
                    <strong class="text-pp-700 dark:text-pp-400 font-extrabold">{{ $author->discussions()->count() }} requests</strong>
                  </div>
                </div>

                <div class="pt-2">
                  <a
                    href="{{ route('admin.users.show', $author->id) }}"
                    class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-black text-xs text-center block transition"
                  >
                    Inspect Author Account
                  </a>
                </div>
              </div>
            @endif
          </div>

        </div>

      </div>
    @endif

  </div>

  <!-- ============================================================
       QUICK REJECTION MODAL
  ============================================================ -->
  @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-base font-black text-slate-950 dark:text-white flex items-center gap-2">
            <i class="fas fa-ban text-rose-600"></i> Reject Community Discussion Request
          </h3>
          <button
            type="button"
            wire:click="closeRejectModal"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-400">
          State the specific reason for rejecting topic <strong>#REQ-{{ $discussion->id }}</strong>. This feedback will be recorded in moderation logs and notify the author.
        </p>

        <!-- Preset Reasons Select -->
        <div>
          <label class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 block mb-1">Preset Violations</label>
          <select
            wire:model.live="presetReason"
            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-rose-500"
          >
            <option value="">-- Choose a standard violation reason --</option>
            @foreach($standardReasons as $reasonTitle => $reasonText)
              <option value="{{ $reasonText }}">{{ $reasonTitle }}</option>
            @endforeach
          </select>
        </div>

        <!-- Custom Reason Textarea -->
        <div>
          <label class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 block mb-1">
            Rejection Explanation <span class="text-rose-600">*</span>
          </label>
          <textarea
            wire:model="rejectionReason"
            rows="4"
            placeholder="Type comprehensive feedback for the author regarding what needs to be changed..."
            class="w-full px-3.5 py-2.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 font-medium"
          ></textarea>
          @error('rejectionReason')
            <span class="text-rose-600 text-xs font-bold block mt-1">{{ $message }}</span>
          @enderror
        </div>

        <!-- Modal Actions -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            wire:click="closeRejectModal"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-extrabold text-xs transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            wire:click="submitReject"
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs transition cursor-pointer shadow-xs flex items-center gap-1.5"
          >
            <i class="fas fa-ban text-[11px]"></i>
            <span>Confirm Rejection</span>
          </button>
        </div>
      </div>
    </div>
  @endif

</div>
