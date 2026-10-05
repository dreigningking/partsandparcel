<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Operations</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Moderation Queue</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Content Moderation & Review Queue
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Inspect, verify, approve, or reject user-submitted listings, community discussions, and blog post comments.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <button
                type="button"
                wire:click="sendNotifierAlert"
                wire:loading.attr="disabled"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
                title="Send notification digest of pending items to all admins"
            >
                <i class="fas fa-bell text-amber-500" wire:loading.remove wire:target="sendNotifierAlert"></i>
                <i class="fas fa-spinner fa-spin text-amber-500" wire:loading wire:target="sendNotifierAlert"></i>
                <span>Notify Admins</span>
            </button>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:border-rose-800/60 dark:bg-rose-950/40 p-4 text-sm font-semibold text-rose-800 dark:text-rose-300 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 sm:gap-4">
        <!-- Pending Total -->
        <div 
            wire:click="setFilter('pending')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'pending' && $type === 'all' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Awaiting</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                    <i class="fas fa-hourglass-half"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($pendingCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Needs action</p>
        </div>

        <!-- Pending Listings -->
        <div 
            wire:click="setType('listing')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $type === 'listing' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Listings</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-box-open"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingListingsCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Marketplace</p>
        </div>

        <!-- Pending Locations (Address Proofs) -->
        <div 
            wire:click="setType('location')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $type === 'location' ? 'border-teal-500 ring-2 ring-teal-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Addresses</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingLocationsCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Utility bills</p>
        </div>

        <!-- Pending User Verifications (KYC) -->
        <div 
            wire:click="setType('verification')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $type === 'verification' ? 'border-purple-500 ring-2 ring-purple-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Identity</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs">
                    <i class="fas fa-id-card"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingVerificationsCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">IDs & Selfies</p>
        </div>

        <!-- Pending Discussions -->
        <div 
            wire:click="setType('discussion')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $type === 'discussion' ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Discussions</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fas fa-comments"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingDiscussionsCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Community</p>
        </div>

        <!-- Pending Comments -->
        <div 
            wire:click="setType('post_comment')"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $type === 'post_comment' ? 'border-sky-500 ring-2 ring-sky-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Comments</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">
                    <i class="fas fa-comment-dots"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingCommentsCount) }}</div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Blog notes</p>
        </div>

        <!-- Processed Summary -->
        <div 
            wire:click="setFilter('processed')"
            class="col-span-2 sm:col-span-1 p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border transition cursor-pointer {{ $filter === 'processed' ? 'border-pp-500 ring-2 ring-pp-500/20 shadow-sm' : 'border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Processed</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-xs">
                    <i class="fas fa-shield-alt"></i>
                </span>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($approvedCount) }}</span>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-base sm:text-lg font-bold text-rose-500 dark:text-rose-400">{{ number_format($rejectedCount) }}</span>
            </div>
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Approved / Rejected</p>
        </div>
    </div>

    <!-- MAIN CONTROL PANEL & DATA TABLE -->
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">

        <!-- FILTERS & SEARCH TOOLBAR -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- STATUS TABS -->
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="setFilter('pending')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Pending Review</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $pendingCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('approved')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Approved</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'approved' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $approvedCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('rejected')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>Rejected</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'rejected' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $rejectedCount }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setFilter('all')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $filter === 'all' ? 'bg-pp-700 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    <span>All Records</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $totalCount }}
                    </span>
                </button>
            </div>

            <!-- SEARCH & TYPE FILTERS -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Type Filter Dropdown -->
                <div class="relative">
                    <select
                        wire:model.live="type"
                        class="h-9 pl-3 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                        <option value="all">All Content Types</option>
                        <option value="listing">Listings (Marketplace)</option>
                        <option value="location">Address Proofs (Locations)</option>
                        <option value="verification">Identity KYC (Verifications)</option>
                        <option value="discussion">Discussions (Community)</option>
                        <option value="post_comment">Blog Comments</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search item, author, reason..."
                        class="w-full h-9 pl-8 pr-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-500/30"
                    >
                    @if($search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                        >
                            <i class="fas fa-times-circle text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Sort Order -->
                <button
                    type="button"
                    wire:click="$set('sortOrder', '{{ $sortOrder === 'desc' ? 'asc' : 'desc' }}')"
                    class="h-9 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
                    title="Toggle sort direction"
                >
                    <i class="fas fa-sort-amount-{{ $sortOrder === 'desc' ? 'down' : 'up' }} text-slate-400"></i>
                    <span class="hidden sm:inline">{{ $sortOrder === 'desc' ? 'Newest' : 'Oldest' }}</span>
                </button>
            </div>
        </div>

        <!-- TABLE OF MODERATIONS -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Content Item</th>
                        <th class="px-4 py-3.5">Type & Action</th>
                        <th class="px-4 py-3.5">Submitter</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Timeline / Reviewer</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($moderations as $moderation)
                        @php
                            $item = $moderation->moderatable;
                            $isListing = $moderation->moderatable_type === 'App\Models\Listing' || $item instanceof \App\Models\Listing;
                            $isDiscussion = $moderation->moderatable_type === 'App\Models\Discussion' || $item instanceof \App\Models\Discussion;
                            $isComment = $moderation->moderatable_type === 'App\Models\PostComment' || $item instanceof \App\Models\PostComment;
                            $isLocation = $moderation->moderatable_type === 'App\Models\Location' || $item instanceof \App\Models\Location;
                            $isVerification = $moderation->moderatable_type === 'App\Models\Verification' || $item instanceof \App\Models\Verification;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">

                            <!-- ITEM / CONTENT -->
                            <td class="px-4 py-3.5 max-w-xs sm:max-w-sm">
                                <div class="flex items-start gap-3">
                                    <!-- Type Icon / Thumbnail -->
                                    <div class="shrink-0 mt-0.5">
                                        @if($isListing)
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs border border-emerald-100 dark:border-emerald-800/60 shadow-2xs">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                        @elseif($isLocation)
                                            <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-xs border border-teal-100 dark:border-teal-800/60 shadow-2xs">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                        @elseif($isVerification)
                                            <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xs border border-purple-100 dark:border-purple-800/60 shadow-2xs">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                        @elseif($isDiscussion)
                                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-100 dark:border-indigo-800/60 shadow-2xs">
                                                <i class="fas fa-comments"></i>
                                            </div>
                                        @elseif($isComment)
                                            <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-xs border border-sky-100 dark:border-sky-800/60 shadow-2xs">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center font-bold text-xs">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content Titles & Details -->
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 dark:text-white truncate hover:text-pp-600 transition cursor-pointer" wire:click="preview({{ $moderation->id }})">
                                            {{ $moderation->item_title }}
                                        </div>

                                        @if($isListing && $item)
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span class="font-black text-slate-900 dark:text-slate-200">
                                                    ₦{{ number_format((float) ($item->price ?? 0), 2) }}
                                                </span>
                                                <span>·</span>
                                                <span class="capitalize">{{ $item->item?->condition_status ?? 'Used' }}</span>
                                                @if($item->item?->deviceModel?->name)
                                                    <span>·</span>
                                                    <span class="truncate">{{ $item->item->deviceModel->name }}</span>
                                                @endif
                                            </div>
                                        @elseif($isLocation && $item)
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $item->city ?? '' }}, {{ $item->state?->name ?? '' }}</span>
                                                <span>·</span>
                                                <span class="truncate max-w-[150px]">{{ $item->address_line_1 }}</span>
                                                @if($item->utility_bill_path)
                                                    <span>·</span>
                                                    <span class="text-teal-600 dark:text-teal-400 font-semibold flex items-center gap-1"><i class="fas fa-file-invoice"></i> Bill</span>
                                                @endif
                                            </div>
                                        @elseif($isVerification && $item)
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span class="font-bold text-purple-600 dark:text-purple-400 uppercase">{{ str_replace('_', ' ', $item->document_type ?? 'ID') }}</span>
                                                @if($item->document_number)
                                                    <span>·</span>
                                                    <span class="font-mono text-[10px]">{{ $item->document_number }}</span>
                                                @endif
                                                @if($item->liveness_verified)
                                                    <span>·</span>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1"><i class="fas fa-check-circle"></i> Live Face Checked</span>
                                                @endif
                                            </div>
                                        @elseif($isDiscussion && $item)
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span class="px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold uppercase">
                                                    {{ $item->type ?? 'request' }}
                                                </span>
                                                @if($item->budget)
                                                    <span>Budget: ₦{{ $item->budget }}</span>
                                                @endif
                                                <span class="truncate max-w-[180px]">{{ Str::limit($item->body, 50) }}</span>
                                            </div>
                                        @elseif($isComment && $item)
                                            <div class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 italic">
                                                "{{ Str::limit($item->comment, 65) }}"
                                            </div>
                                        @else
                                            <div class="text-[11px] text-slate-400">Content reference #{{ $moderation->moderatable_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- TYPE & ACTION -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isListing ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : ($isLocation ? 'bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300' : ($isVerification ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : ($isDiscussion ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300'))) }}">
                                        {{ $moderation->type_label }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-semibold {{ $moderation->action === 'created' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' }}">
                                        <i class="fas fa-{{ $moderation->action === 'created' ? 'plus-circle' : 'edit' }} text-[9px]"></i>
                                        <span>{{ ucfirst($moderation->action ?? 'created') }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- SUBMITTER -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-black text-[10px]">
                                        {{ strtoupper(substr($moderation->author_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[130px]">
                                            {{ $moderation->author_name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[130px]">
                                            {{ $moderation->author_email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($moderation->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending Review
                                    </span>
                                @elseif($moderation->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                        <i class="fas fa-check text-[10px]"></i>
                                        Approved
                                    </span>
                                @else
                                    <div class="flex flex-col gap-0.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/60">
                                            <i class="fas fa-ban text-[10px]"></i>
                                            Rejected
                                        </span>
                                        @if($moderation->reason)
                                            <span class="text-[10px] text-rose-500 dark:text-rose-400 truncate max-w-[150px]" title="{{ $moderation->reason }}">
                                                {{ $moderation->reason }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- TIMELINE & MODERATOR -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="text-slate-600 dark:text-slate-300 font-medium">
                                    {{ $moderation->created_at?->diffForHumans() ?? '—' }}
                                </div>
                                @if($moderation->moderator)
                                    <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                                        <i class="fas fa-user-shield text-[9px] text-slate-400"></i>
                                        <span>{{ $moderation->moderator->name }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Preview Button -->
                                    <button
                                        type="button"
                                        wire:click="preview({{ $moderation->id }})"
                                        class="p-2 rounded-lg text-slate-500 hover:text-pp-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                        title="Preview Content"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    @if($moderation->status === 'pending')
                                        <!-- Quick Approve -->
                                        <button
                                            type="button"
                                            wire:click="approve({{ $moderation->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1"
                                            title="Approve immediately"
                                        >
                                            <i class="fas fa-check text-[10px]"></i>
                                            <span class="hidden sm:inline">Approve</span>
                                        </button>

                                        <!-- Quick Reject -->
                                        <button
                                            type="button"
                                            wire:click="openRejectModal({{ $moderation->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-300 font-bold text-xs border border-rose-200 dark:border-rose-800 transition flex items-center gap-1"
                                            title="Reject with reason"
                                        >
                                            <i class="fas fa-times text-[10px]"></i>
                                            <span class="hidden sm:inline">Reject</span>
                                        </button>
                                    @elseif($moderation->status === 'approved')
                                        <!-- Revoke / Reject -->
                                        <button
                                            type="button"
                                            wire:click="openRejectModal({{ $moderation->id }})"
                                            class="px-2 py-1 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-[11px] font-semibold transition"
                                            title="Revoke Approval"
                                        >
                                            <span>Revoke</span>
                                        </button>
                                    @elseif($moderation->status === 'rejected')
                                        <!-- Re-approve -->
                                        <button
                                            type="button"
                                            wire:click="approve({{ $moderation->id }})"
                                            class="px-2 py-1 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-[11px] font-semibold transition"
                                            title="Re-approve Item"
                                        >
                                            <span>Re-approve</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-lg mb-3">
                                    <i class="fas fa-check-double text-emerald-500"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No moderation items found</h3>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    @if($filter === 'pending')
                                        Great job! All submitted listings, discussions, and comments have been reviewed.
                                    @else
                                        No items match the selected filter criteria or search query.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if ($moderations->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $moderations->links() }}
            </div>
        @endif
    </div>

    <!-- REJECTION MODAL -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closeRejectModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900">
                                <i class="fas fa-exclamation-triangle text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-bold text-slate-950 dark:text-white" id="modal-title">
                                    Reject Moderation Item
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Please select or describe the reason for rejecting this content. This feedback will be recorded and shared with the author.
                                </p>
                            </div>
                        </div>

                        <!-- Preset Reason Quick Chips -->
                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Common Reasons
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach([
                                        'Prohibited or dangerous item/content',
                                        'Inappropriate or offensive language',
                                        'Suspected fraud or spam attempt',
                                        'Inaccurate or misleading information',
                                        'Low quality images or lack of details',
                                        'Policy and terms violation'
                                    ] as $chip)
                                        <button
                                            type="button"
                                            wire:click="setPresetReason('{{ $chip }}')"
                                            class="px-2.5 py-1 rounded-lg text-xs font-medium transition border {{ $rejectionReason === $chip ? 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-700' : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 hover:bg-slate-100' }}"
                                        >
                                            {{ $chip }}
                                        </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom Reason Textarea -->
                        <div class="mt-4">
                            <label for="rejectionReason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Detailed Rejection Explanation <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="rejectionReason"
                                wire:model="rejectionReason"
                                rows="3"
                                placeholder="Explain why this content was rejected so the user can make required corrections..."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-hidden focus:ring-2 focus:ring-rose-500/30"
                            ></textarea>
                            @error('rejectionReason')
                                <p class="text-xs text-rose-600 dark:text-rose-400 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeRejectModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="confirmReject"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5"
                        >
                            <i class="fas fa-ban text-[11px]" wire:loading.remove wire:target="confirmReject"></i>
                            <i class="fas fa-spinner fa-spin text-[11px]" wire:loading wire:target="confirmReject"></i>
                            <span>Confirm Rejection</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- CONTENT PREVIEW MODAL -->
    @if ($showPreviewModal && $previewItem)
        @php
            $target = $previewItem->moderatable;
            $isListing = $previewItem->moderatable_type === 'App\Models\Listing' || $target instanceof \App\Models\Listing;
            $isDiscussion = $previewItem->moderatable_type === 'App\Models\Discussion' || $target instanceof \App\Models\Discussion;
            $isComment = $previewItem->moderatable_type === 'App\Models\PostComment' || $target instanceof \App\Models\PostComment;
            $isLocation = $previewItem->moderatable_type === 'App\Models\Location' || $target instanceof \App\Models\Location;
            $isVerification = $previewItem->moderatable_type === 'App\Models\Verification' || $target instanceof \App\Models\Verification;
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="preview-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closePreview"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-800">

                    <!-- Preview Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black {{ $isListing ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($isLocation ? 'bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300' : ($isVerification ? 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : ($isDiscussion ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300'))) }}">
                                {{ $previewItem->type_label }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">
                                Submitted {{ $previewItem->created_at?->format('M d, Y h:i A') }}
                            </span>
                        </div>
                        <button
                            type="button"
                            wire:click="closePreview"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Preview Content Body -->
                    <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">

                        <!-- Content Title -->
                        <div>
                            <h2 class="text-xl font-black text-slate-950 dark:text-white">
                                {{ $previewItem->item_title }}
                            </h2>
                            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                <span>Author: <strong class="text-slate-800 dark:text-slate-200">{{ $previewItem->author_name }}</strong></span>
                                <span>·</span>
                                <span>{{ $previewItem->author_email }}</span>
                            </div>
                        </div>

                        <!-- LISTING DETAILS -->
                        @if ($isListing && $target)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Price</div>
                                        <div class="text-sm font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                                            ₦{{ number_format((float) ($target->price ?? 0), 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Quantity</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->quantity ?? 1 }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Condition</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5 capitalize">
                                            {{ $target->item?->condition_status ?? 'Used' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Shipping</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->allow_shipping ? 'Allowed' : 'Local Only' }}
                                        </div>
                                    </div>
                                </div>

                                @if($target->item?->description)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Item Description</h4>
                                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                            {{ $target->item->description }}
                                        </div>
                                    </div>
                                @endif

                                @if($target->item?->condition_notes)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Condition Notes</h4>
                                        <div class="p-3.5 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 text-xs text-amber-900 dark:text-amber-200 leading-relaxed border border-amber-100 dark:border-amber-900/40">
                                            {{ $target->item->condition_notes }}
                                        </div>
                                    </div>
                                @endif

                                <div class="pt-2">
                                    <a 
                                        href="{{ route('admin.listings.show', $target->id) }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-pp-600 hover:text-pp-700 dark:text-pp-400 hover:underline"
                                    >
                                        <span>Open Full Listing Property Page</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </div>

                            <!-- DISCUSSION DETAILS -->
                        @elseif ($isDiscussion && $target)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Type</div>
                                        <div class="text-sm font-black text-indigo-600 dark:text-indigo-400 mt-0.5 capitalize">
                                            {{ $target->type ?? 'Item' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Budget</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->budget ? '₦' . $target->budget : 'Open' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Category</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5 truncate">
                                            {{ $target->category?->name ?? '—' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Urgency</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->urgency }}
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Discussion Content</h4>
                                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                        {{ $target->body }}
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a 
                                        href="{{ route('community.request', $target->id) }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-pp-600 hover:text-pp-700 dark:text-pp-400 hover:underline"
                                    >
                                        <span>View on Community Board</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </div>

                            <!-- POST COMMENT DETAILS -->
                        @elseif ($isComment && $target)
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">Blog Post Reference</div>
                                    <div class="text-sm font-black text-slate-900 dark:text-white mt-1">
                                        {{ $target->post?->title ?? "Post #{$target->post_id}" }}
                                    </div>
                                    @if($target->post)
                                        <div class="mt-1">
                                            <a href="{{ route('admin.blog.show', $target->post_id) }}" target="_blank" class="text-xs font-bold text-pp-600 hover:underline inline-flex items-center gap-1">
                                                <span>Open Blog Post In Admin</span>
                                                <i class="fas fa-external-link-alt text-[9px]"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Submitted Comment Body</h4>
                                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-800 dark:text-slate-200 leading-relaxed border border-slate-100 dark:border-slate-700/60 whitespace-pre-line">
                                        {{ $target->comment }}
                                    </div>
                                </div>

                            <!-- LOCATION DETAILS -->
                        @elseif ($isLocation && $target)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Label</div>
                                        <div class="text-sm font-black text-slate-900 dark:text-white mt-0.5">
                                            {{ $target->label ?? 'Location' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">City & State</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->city }}, {{ $target->state?->name ?? '—' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Country</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->country?->name ?? 'Nigeria' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase">Default Location</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 mt-0.5">
                                            {{ $target->is_default ? 'Yes' : 'No' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-700 dark:text-slate-300">
                                    <span class="font-bold text-slate-500 uppercase tracking-wider block text-[10px] mb-1">Full Street Address</span>
                                    {{ $target->address_line_1 }}
                                    @if($target->address_line_2)
                                        <div>{{ $target->address_line_2 }}</div>
                                    @endif
                                    @if($target->postal_code)
                                        <div class="text-slate-400 mt-1">Postal Code: {{ $target->postal_code }}</div>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                                        <span>Utility Bill / Address Proof Document</span>
                                        <span class="text-[11px] font-semibold text-slate-400">Electricity, Water, or Waste Bill</span>
                                    </h4>

                                    @if ($target->utility_bill_path)
                                        @php
                                            $ext = strtolower(pathinfo($target->utility_bill_path, PATHINFO_EXTENSION));
                                            $billUrl = Storage::url($target->utility_bill_path);
                                        @endphp

                                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                            <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-950 flex flex-col items-center">
                                                <a href="{{ $billUrl }}" target="_blank" title="Click to view full image">
                                                    <img src="{{ $billUrl }}" alt="Utility Bill" class="max-h-96 w-auto object-contain mx-auto">
                                                </a>
                                                <div class="w-full bg-slate-900/90 p-2 text-center">
                                                    <a href="{{ $billUrl }}" target="_blank" class="text-xs text-pp-400 hover:underline font-bold inline-flex items-center gap-1">
                                                        <span>Open full bill image in new tab</span>
                                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-6 rounded-xl border-2 border-dashed border-teal-300 dark:border-teal-800 bg-teal-50/50 dark:bg-teal-950/20 text-center">
                                                <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/60 text-teal-600 dark:text-teal-300 flex items-center justify-center mx-auto text-xl mb-2">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <h5 class="text-sm font-bold text-slate-800 dark:text-slate-200">PDF Document Uploaded</h5>
                                                <p class="text-xs text-slate-500 mt-1 mb-3">Address proof submitted as PDF document.</p>
                                                <a href="{{ $billUrl }}" target="_blank" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs inline-flex items-center gap-2 transition shadow-xs">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    <span>View Utility Bill PDF</span>
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-center text-slate-400 text-xs">
                                            <i class="fas fa-exclamation-circle text-amber-500 text-base mb-1 block"></i>
                                            No utility bill document has been uploaded for this address yet.
                                        </div>
                                    @endif
                                </div>

                            <!-- USER IDENTITY KYC & LIVENESS VERIFICATION -->
                        @elseif ($isVerification && $target)
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">Document Type</div>
                                    <div class="text-sm font-black text-purple-600 dark:text-purple-400 mt-0.5 uppercase">
                                        {{ str_replace('_', ' ', $target->document_type ?? 'Government ID') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">Document Number</div>
                                    <div class="text-sm font-black font-mono text-slate-800 dark:text-slate-200 mt-0.5">
                                        {{ $target->document_number ?? 'Not Provided' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">Liveness Check</div>
                                    <div class="text-sm font-black mt-0.5 {{ $target->liveness_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-500' }}">
                                        {{ $target->liveness_verified ? 'Passed (Live)' : 'Pending' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">KYC Status</div>
                                    <div class="text-sm font-black mt-0.5 capitalize {{ $target->status === 'verified' ? 'text-emerald-600' : ($target->status === 'rejected' ? 'text-rose-600' : 'text-amber-500') }}">
                                        {{ $target->status ?? 'Pending' }}
                                    </div>
                                </div>
                            </div>

                            <!-- COMPARISON PANEL: ID CARD vs LIVE SELFIE -->
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Identity Document & Live Face Comparison
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <!-- FRONT ID -->
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 bg-white dark:bg-slate-800/60">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase mb-2 flex items-center justify-between">
                                            <span>Front of ID Card</span>
                                            <i class="fas fa-id-card text-purple-500"></i>
                                        </div>
                                        @if($target->front_image)
                                            <a href="{{ Storage::url($target->front_image) }}" target="_blank" class="block aspect-4/3 rounded-lg overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-700 hover:opacity-90 transition">
                                                <img src="{{ Storage::url($target->front_image) }}" alt="Front ID" class="w-full h-full object-cover">
                                            </a>
                                            <div class="mt-2 text-center">
                                                <a href="{{ Storage::url($target->front_image) }}" target="_blank" class="text-[11px] text-pp-600 hover:underline font-bold">
                                                    View Full Size
                                                </a>
                                            </div>
                                        @else
                                            <div class="aspect-4/3 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 text-xs">
                                                No front image
                                            </div>
                                        @endif
                                    </div>

                                    <!-- BACK ID -->
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 bg-white dark:bg-slate-800/60">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase mb-2 flex items-center justify-between">
                                            <span>Back of ID Card</span>
                                            <i class="fas fa-id-card-alt text-purple-500"></i>
                                        </div>
                                        @if($target->back_image)
                                            <a href="{{ Storage::url($target->back_image) }}" target="_blank" class="block aspect-4/3 rounded-lg overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-700 hover:opacity-90 transition">
                                                <img src="{{ Storage::url($target->back_image) }}" alt="Back ID" class="w-full h-full object-cover">
                                            </a>
                                            <div class="mt-2 text-center">
                                                <a href="{{ Storage::url($target->back_image) }}" target="_blank" class="text-[11px] text-pp-600 hover:underline font-bold">
                                                    View Full Size
                                                </a>
                                            </div>
                                        @else
                                            <div class="aspect-4/3 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 text-xs">
                                                Optional / Not provided
                                            </div>
                                        @endif
                                    </div>

                                    <!-- LIVE SELFIE / FACIAL RECOGNITION -->
                                    <div class="rounded-xl border border-purple-200 dark:border-purple-800/60 p-3 bg-purple-50/20 dark:bg-purple-950/20">
                                        <div class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase mb-2 flex items-center justify-between">
                                            <span>Live Camera Selfie</span>
                                            <i class="fas fa-camera text-emerald-500"></i>
                                        </div>
                                        @if($target->selfie_image)
                                            <a href="{{ Storage::url($target->selfie_image) }}" target="_blank" class="block aspect-4/3 rounded-lg overflow-hidden bg-slate-900 border border-purple-300 dark:border-purple-700 hover:opacity-90 transition">
                                                <img src="{{ Storage::url($target->selfie_image) }}" alt="Live Selfie" class="w-full h-full object-cover">
                                            </a>
                                            <div class="mt-2 text-center">
                                                <a href="{{ Storage::url($target->selfie_image) }}" target="_blank" class="text-[11px] text-purple-600 hover:underline font-bold">
                                                    View Full Size
                                                </a>
                                            </div>
                                        @else
                                            <div class="aspect-4/3 rounded-lg border-2 border-dashed border-purple-200 dark:border-purple-800 flex items-center justify-center text-slate-400 text-xs">
                                                No live selfie captured
                                            </div>
                                        @endif

                                        @if(!empty($target->liveness_images) && is_array($target->liveness_images))
                                            <div class="mt-2.5 pt-2.5 border-t border-purple-200 dark:border-purple-800/60">
                                                <div class="text-[9px] font-black text-purple-700 dark:text-purple-300 uppercase mb-1.5 flex items-center justify-between">
                                                    <span>3-Frame Liveness Burst</span>
                                                    <span class="text-emerald-600 font-bold">Motion Verified</span>
                                                </div>
                                                <div class="grid grid-cols-3 gap-1">
                                                    @foreach($target->liveness_images as $fIdx => $fPath)
                                                        <a href="{{ Storage::url($fPath) }}" target="_blank" class="aspect-4/3 rounded overflow-hidden bg-slate-900 border border-purple-200 dark:border-purple-800 hover:opacity-85 transition relative block">
                                                            <img src="{{ Storage::url($fPath) }}" alt="Frame {{ $fIdx + 1 }}" class="w-full h-full object-cover">
                                                            <span class="absolute bottom-0.5 right-0.5 px-1 py-0.2 bg-black/70 text-white text-[8px] font-bold rounded">#{{ $fIdx + 1 }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Moderation Metadata Status -->
                        @if ($previewItem->status !== 'pending')
                            <div class="p-3.5 rounded-xl border {{ $previewItem->status === 'approved' ? 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800' : 'bg-rose-50/50 border-rose-200 dark:bg-rose-950/20 dark:border-rose-800' }}">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold {{ $previewItem->status === 'approved' ? 'text-emerald-800 dark:text-emerald-300' : 'text-rose-800 dark:text-rose-300' }}">
                                        Moderation Status: {{ ucfirst($previewItem->status) }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        Reviewed by {{ $previewItem->moderator->name ?? 'Admin' }}
                                    </span>
                                </div>
                                @if($previewItem->reason)
                                    <div class="mt-1 text-xs text-rose-700 dark:text-rose-300">
                                        Reason: {{ $previewItem->reason }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Preview Footer Actions -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <button
                            type="button"
                            wire:click="closePreview"
                            class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition"
                        >
                            Close Preview
                        </button>

                        <div class="flex items-center gap-2">
                            @if ($previewItem->status === 'pending')
                                <button
                                    type="button"
                                    wire:click="openRejectModal({{ $previewItem->id }})"
                                    class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-300 font-bold text-xs border border-rose-200 dark:border-rose-800 transition"
                                >
                                    Reject
                                </button>
                                <button
                                    type="button"
                                    wire:click="approve({{ $previewItem->id }})"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition"
                                >
                                    Approve Content
                                </button>
                            @elseif ($previewItem->status === 'approved')
                                <button
                                    type="button"
                                    wire:click="openRejectModal({{ $previewItem->id }})"
                                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition"
                                >
                                    Revoke & Reject
                                </button>
                            @elseif ($previewItem->status === 'rejected')
                                <button
                                    type="button"
                                    wire:click="approve({{ $previewItem->id }})"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition"
                                >
                                    Re-approve
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>