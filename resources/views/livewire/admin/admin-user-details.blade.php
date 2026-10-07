<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <a href="{{ route('admin.users') }}" class="hover:text-pp-600 transition">User Accounts</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300 truncate max-w-xs">{{ $user->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight flex items-center gap-2.5">
                <span>{{ $user->name }}</span>
                @if($user->is_verified || $user->is_fully_verified)
                    <span class="text-purple-600 dark:text-purple-400" title="Identity Verified">
                        <i class="fas fa-check-circle text-lg"></i>
                    </span>
                @endif
                @if($user->isSuspended())
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60">
                        Suspended
                    </span>
                @endif
                @if($user->freeze_payout)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60">
                        <i class="fas fa-snowflake mr-1"></i>Payouts Frozen
                    </span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Account verification review, KYC documents, locations validation, activity engagements, and administrative controls.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
            <a
                href="{{ route('admin.users') }}"
                class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2"
            >
                <i class="fas fa-arrow-left text-slate-400 text-xs"></i>
                <span>Users List</span>
            </a>

            @if(auth()->id() !== $user->id)
                <button
                    type="button"
                    wire:click="toggleFreezePayout"
                    wire:confirm="{{ $user->freeze_payout ? 'Unfreeze payouts for this user?' : 'Freeze payouts for this user? All current and future settlements will be locked.' }}"
                    class="px-3.5 py-2 rounded-xl {{ $user->freeze_payout ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/50 border border-amber-200 dark:border-amber-800' }} font-bold text-xs transition flex items-center gap-1.5"
                >
                    <i class="fas fa-snowflake text-xs"></i>
                    <span>{{ $user->freeze_payout ? 'Unfreeze Payouts' : 'Freeze Payouts' }}</span>
                </button>

                @if(! $user->isSuspended())
                    <button
                        type="button"
                        wire:click="suspend"
                        wire:confirm="Suspend this user account? They will lose access to trading and publishing listings."
                        class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 font-bold text-xs transition flex items-center gap-1.5"
                    >
                        <i class="fas fa-user-slash text-xs"></i>
                        <span>Suspend</span>
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="unsuspend"
                        wire:confirm="Restore platform access for this user account?"
                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
                    >
                        <i class="fas fa-user-check text-xs"></i>
                        <span>Unsuspend</span>
                    </button>
                @endif

                <button
                    type="button"
                    wire:click="delete"
                    wire:confirm="Permanently delete this user account? This action cannot be undone."
                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                    title="Delete Account"
                >
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            @endif
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

    <!-- USER PROFILE OVERVIEW CARD -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="relative shrink-0">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs" />
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-pp-500 to-pp-700 text-white font-black text-xl flex items-center justify-center border border-pp-600 shadow-2xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    @if($user->isSuspended())
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] shadow-xs" title="Suspended">
                            <i class="fas fa-ban"></i>
                        </span>
                    @elseif($user->is_verified || $user->is_fully_verified)
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] shadow-xs" title="Verified">
                            <i class="fas fa-check"></i>
                        </span>
                    @endif
                </div>

                <div>
                    <div class="flex items-center flex-wrap gap-2">
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">
                            {{ $user->name }}
                        </h2>
                        @if($user->role)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/50">
                                {{ $user->role->name }}
                            </span>
                        @endif
                    </div>

                    @if($user->business_name)
                        <p class="text-xs font-bold text-pp-600 dark:text-pp-400 flex items-center gap-1.5 mt-1">
                            <i class="fas fa-store text-xs opacity-80"></i>
                            <span>{{ $user->business_name }}</span>
                        </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-slate-500 dark:text-slate-400">
                        <a href="mailto:{{ $user->email }}" class="hover:text-pp-600 transition flex items-center gap-1.5">
                            <i class="fas fa-envelope text-slate-400 text-[11px]"></i>
                            <span>{{ $user->email }}</span>
                        </a>

                        <a href="tel:{{ $user->phone }}" class="hover:text-pp-600 transition flex items-center gap-1.5">
                            <i class="fas fa-phone text-slate-400 text-[11px]"></i>
                            <span>{{ $user->phone ?: 'No phone number' }}</span>
                        </a>

                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-globe text-slate-400 text-[11px]"></i>
                            <span>{{ $user->country?->name ?? 'Nigeria' }} ({{ $user->country?->code ?? 'NG' }})</span>
                        </span>

                        <span class="text-slate-400">
                            Joined {{ $user->created_at?->format('M d, Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Current Subscription Badge -->
            <div class="flex flex-col items-start md:items-end justify-center gap-1.5 shrink-0 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subscription Tier</span>
                @if($user->activeSubscription)
                    <div class="flex items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-pp-50 text-pp-700 dark:bg-pp-950/60 dark:text-pp-300 border border-pp-200/60 dark:border-pp-800/60 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $user->activeSubscription->plan?->name }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        @if($user->activeSubscription->ends_at)
                            Expires {{ $user->activeSubscription->ends_at->format('M d, Y') }}
                        @else
                            Continuous Tier
                        @endif
                        · Fee: {{ $user->activeSubscription->plan?->escrow_percentage }}%
                    </p>
                @else
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-200/70 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                        Free / Starter Tier
                    </span>
                    <p class="text-[11px] text-slate-400">Standard 10% escrow fee</p>
                @endif
            </div>
        </div>
    </div>

    <!-- USER ENGAGEMENTS SECTION -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-chart-pie text-pp-600"></i>
                <span>Platform Activity & Engagements</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">Activity summary across marketplace & services</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- Listings (Live / Total) -->
            <a 
                href="{{ route('admin.listings', ['q' => $user->name]) }}"
                class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-emerald-400 transition block group"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Listings</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fas fa-box-open"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 dark:text-white flex items-baseline gap-1">
                    <span class="text-emerald-600">{{ $liveListingsCount }}</span>
                    <span class="text-xs text-slate-400">/</span>
                    <span>{{ $listingsCount }}</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">Live / Total items</p>
            </a>

            <!-- Discussions Initiated -->
            <a 
                href="{{ route('admin.discussions') }}"
                class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-indigo-400 transition block group"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Discussions</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                        <i class="fas fa-comments"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 dark:text-white">{{ number_format($discussionsCount) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Requests & topics</p>
            </a>

            <!-- Responses Posted -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Responses</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">
                        <i class="fas fa-reply-all"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 dark:text-white">{{ number_format($responsesCount) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Replies & quotes</p>
            </div>

            <!-- Services -->
            <a 
                href="{{ route('admin.services') }}"
                class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-teal-400 transition block group"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Services</span>
                    <span class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs">
                        <i class="fas fa-wrench"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 dark:text-white">{{ number_format($totalServicesCount) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    {{ $servicesAsProviderCount }} prov · {{ $servicesAsCustomerCount }} cust
                </p>
            </a>

            <!-- Payments Sent (Purchases) -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Payments Sent</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                        <i class="fas fa-arrow-up"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 dark:text-white">{{ number_format($paymentsSentCount) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    ₦{{ number_format($paymentsSentTotal, 2) }}
                </p>
            </div>

            <!-- Payments Received (Sales) -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Payments In</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fas fa-arrow-down"></i>
                    </span>
                </div>
                <div class="mt-2 text-xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($paymentsReceivedCount) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    ₦{{ number_format($paymentsReceivedTotal, 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- VERIFICATION SECTION (KYC & IDENTITY SUBMISSION) -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white flex items-center gap-2">
                    <i class="fas fa-id-card text-purple-600"></i>
                    <span>Identity & KYC Verification</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Review submitted government photo identification documents, selfie images, and multi-frame liveness detection sequences.
                </p>
            </div>

            <!-- Overall User Verification Standing Badge -->
            <div class="flex items-center gap-2">
                @if($user->is_verified || $user->is_fully_verified)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200">
                        <i class="fas fa-check-circle text-xs"></i>
                        <span>Identity Verified</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                        <i class="fas fa-hourglass-half text-xs text-amber-500"></i>
                        <span>Unverified Identity</span>
                    </span>
                @endif
            </div>
        </div>

        @php
            $verifications = $user->verifications()->with('moderation')->latest()->get();
        @endphp

        @if($verifications->isNotEmpty())
            <div class="space-y-6">
                @foreach($verifications as $v)
                    @php
                        $vStatus = $v->status; // 'verified', 'approved', 'pending', 'rejected'
                        $isPending = $v->isPending();
                        $isApproved = $v->isVerified();
                        $isRejected = $v->isRejected();
                    @endphp
                    <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-4">
                        
                        <!-- Verification Header & Moderation Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold uppercase bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        {{ $v->document_type_label }}
                                    </span>
                                    @if($v->document_number)
                                        <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                            #{{ $v->document_number }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Submitted {{ $v->created_at?->format('M d, Y · h:i A') }} ({{ $v->created_at?->diffForHumans() }})
                                </p>
                            </div>

                            <!-- Moderation Status Badge -->
                            <div class="flex items-center gap-3">
                                @if($isPending)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending Review
                                    </span>
                                @elseif($isApproved)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200">
                                        <i class="fas fa-check text-xs"></i>
                                        Approved
                                    </span>
                                @elseif($isRejected)
                                    <div class="flex flex-col items-end">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200">
                                            <i class="fas fa-ban text-xs"></i>
                                            Rejected
                                        </span>
                                        @if($v->rejection_reason)
                                            <span class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5 max-w-xs text-right">
                                                Reason: {{ $v->rejection_reason }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Submitted Documents Gallery (Front, Back, Selfie, Liveness) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            <!-- Front Image -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center">
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    ID Front Document
                                </span>
                                @if($v->front_image_url)
                                    <div 
                                        wire:click="openImagePreview('{{ $v->front_image_url }}', 'ID Front Document - {{ $v->document_type_label }}')"
                                        class="cursor-pointer group relative rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 max-h-44 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img src="{{ $v->front_image_url }}" alt="Front ID" class="w-full h-36 object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                            <i class="fas fa-search-plus"></i> View Full
                                        </div>
                                    </div>
                                @else
                                    <div class="h-36 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-xs">
                                        <i class="fas fa-file-image text-xl mb-1"></i>
                                        <span>No front image</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Back Image -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center">
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    ID Back Document
                                </span>
                                @if($v->back_image_url)
                                    <div 
                                        wire:click="openImagePreview('{{ $v->back_image_url }}', 'ID Back Document - {{ $v->document_type_label }}')"
                                        class="cursor-pointer group relative rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 max-h-44 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img src="{{ $v->back_image_url }}" alt="Back ID" class="w-full h-36 object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                            <i class="fas fa-search-plus"></i> View Full
                                        </div>
                                    </div>
                                @else
                                    <div class="h-36 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-xs">
                                        <i class="fas fa-file-image text-xl mb-1"></i>
                                        <span>No back image (or passport)</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Selfie / Live Face Photo -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center">
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">
                                    Selfie Face Photo
                                </span>
                                @if($v->selfie_image_url)
                                    <div 
                                        wire:click="openImagePreview('{{ $v->selfie_image_url }}', 'Selfie Face Photo - {{ $user->name }}')"
                                        class="cursor-pointer group relative rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 max-h-44 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img src="{{ $v->selfie_image_url }}" alt="Selfie" class="w-full h-36 object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                            <i class="fas fa-search-plus"></i> View Full
                                        </div>
                                    </div>
                                @else
                                    <div class="h-36 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-xs">
                                        <i class="fas fa-user-circle text-xl mb-1"></i>
                                        <span>No selfie uploaded</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Liveness Sequence Frames (Multi-frame snapshots sequence) -->
                        @if(!empty($v->liveness_images) && is_array($v->liveness_images))
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-camera text-purple-600"></i>
                                        <span>Multi-Frame Liveness Detection Snapshots ({{ count($v->liveness_images) }} Frames)</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                                    @foreach($v->liveness_images as $idx => $img)
                                        @php
                                            $imgUrl = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                        @endphp
                                        <div 
                                            wire:click="openImagePreview('{{ $imgUrl }}', 'Liveness Frame #{{ $idx + 1 }}')"
                                            class="cursor-pointer group relative rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 aspect-square bg-slate-100"
                                        >
                                            <img src="{{ $imgUrl }}" alt="Liveness Frame {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-110 transition" />
                                            <span class="absolute bottom-0.5 left-0.5 bg-slate-950/60 text-white text-[9px] px-1 rounded font-mono">
                                                #{{ $idx + 1 }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- MODERATION ACTION BUTTONS -->
                        <div class="flex flex-wrap items-center justify-end gap-2.5 pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
                            {{-- If PENDING: show BOTH approve and reject --}}
                            @if($isPending)
                                <button
                                    type="button"
                                    wire:click="approveVerification({{ $v->id }})"
                                    class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
                                >
                                    <i class="fas fa-check text-xs"></i>
                                    <span>Approve Verification</span>
                                </button>

                                <button
                                    type="button"
                                    wire:click="openRejectModal('verification', {{ $v->id }})"
                                    class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-xs transition flex items-center gap-1.5"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                    <span>Reject with Reason</span>
                                </button>

                            {{-- If APPROVED: show REJECT button --}}
                            @elseif($isApproved)
                                <button
                                    type="button"
                                    wire:click="openRejectModal('verification', {{ $v->id }})"
                                    class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-xs transition flex items-center gap-1.5"
                                    title="Revoke and reject this verification"
                                >
                                    <i class="fas fa-ban text-xs"></i>
                                    <span>Revoke / Reject Verification</span>
                                </button>

                            {{-- If REJECTED: show APPROVE button --}}
                            @elseif($isRejected)
                                <button
                                    type="button"
                                    wire:click="approveVerification({{ $v->id }})"
                                    class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
                                    title="Overturn rejection and approve"
                                >
                                    <i class="fas fa-check-circle text-xs"></i>
                                    <span>Re-Approve Verification</span>
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <!-- No Verification Record Yet -->
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-center">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto text-lg mb-2.5">
                    <i class="fas fa-id-card"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No KYC Documents Submitted</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    The user has not submitted their government ID or live selfie verification documents yet.
                </p>

                <!-- Admin manual override if needed -->
                <div class="mt-4 flex items-center justify-center gap-2">
                    @if(! $user->is_verified)
                        <button
                            type="button"
                            wire:click="approveVerification"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5"
                        >
                            <i class="fas fa-check-circle text-xs"></i>
                            <span>Manually Verify Identity</span>
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="openRejectModal('user')"
                            class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 font-bold text-xs transition flex items-center gap-1.5"
                        >
                            <i class="fas fa-ban text-xs"></i>
                            <span>Mark as Unverified</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- LOCATIONS & ADDRESS PROOFS SECTION -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-teal-600"></i>
                    <span>Registered Locations & Address Proofs</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Review operating addresses, contact information, utility bills, and verify physical location proofs.
                </p>
            </div>

            <span class="text-xs text-slate-400 font-semibold">
                {{ $user->locations->count() }} Location(s) Registered
            </span>
        </div>

        @if($user->locations->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($user->locations as $loc)
                    @php
                        $locStatus = $loc->verification_status; // 'verified', 'pending', 'rejected', 'unverified'
                        $isLocPending = $loc->isPending();
                        $isLocVerified = $loc->isVerified();
                        $isLocRejected = $loc->isRejected();
                    @endphp
                    <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-4">
                        
                        <div class="space-y-3">
                            <!-- Location Header & Badges -->
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $loc->label }}
                                        </h4>
                                        @if($loc->is_default)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300 border border-teal-200">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Added {{ $loc->created_at?->format('M d, Y') }}
                                    </p>
                                </div>

                                <!-- Verification Status Badge -->
                                <div>
                                    @if($isLocVerified)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            Verified
                                        </span>
                                    @elseif($isLocPending)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending Review
                                        </span>
                                    @elseif($isLocRejected)
                                        <div class="flex flex-col items-end">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200">
                                                <i class="fas fa-ban text-xs"></i>
                                                Rejected
                                            </span>
                                            @if($loc->rejection_reason)
                                                <span class="text-[10px] text-rose-500 dark:text-rose-400 mt-0.5 text-right max-w-xs">
                                                    {{ $loc->rejection_reason }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border border-slate-200">
                                            Unverified
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Address & Contact Details -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xs space-y-1">
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ $loc->address_line_1 }}
                                    @if($loc->address_line_2)
                                        , {{ $loc->address_line_2 }}
                                    @endif
                                </div>
                                <div class="text-slate-500 dark:text-slate-400">
                                    {{ $loc->city }}{{ $loc->state ? ', ' . $loc->state->name : '' }}{{ $loc->postal_code ? ' · ' . $loc->postal_code : '' }}
                                </div>
                                <div class="text-slate-400 text-[11px] pt-1 flex items-center gap-3">
                                    <span><i class="fas fa-globe text-[10px]"></i> {{ $loc->country?->name ?? 'Nigeria' }}</span>
                                    @if($loc->contact_name)
                                        <span><i class="fas fa-user text-[10px]"></i> {{ $loc->contact_name }}</span>
                                    @endif
                                    @if($loc->phone)
                                        <span><i class="fas fa-phone text-[10px]"></i> {{ $loc->phone }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Utility Bill / Address Document -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Proof of Address (Utility Bill)
                                </span>
                                @if($loc->utility_bill_url)
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-200">
                                                <i class="fas fa-file-invoice"></i>
                                            </span>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">Utility Bill Uploaded</div>
                                                <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ basename($loc->utility_bill_path) }}</div>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="openImagePreview('{{ $loc->utility_bill_url }}', 'Utility Bill - {{ $loc->label }}')"
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950/50 transition flex items-center gap-1 border border-teal-200 dark:border-teal-800"
                                        >
                                            <i class="fas fa-eye text-[11px]"></i>
                                            <span>Inspect Bill</span>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-xs text-slate-400 py-1 italic">
                                        <i class="fas fa-file-excel text-slate-300"></i>
                                        <span>No utility bill or address proof uploaded</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- MODERATION ACTION BUTTONS FOR LOCATION -->
                        <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            {{-- If PENDING: show BOTH approve and reject --}}
                            @if($isLocPending)
                                <button
                                    type="button"
                                    wire:click="approveLocation({{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1"
                                >
                                    <i class="fas fa-check text-[11px]"></i>
                                    <span>Verify & Approve</span>
                                </button>

                                <button
                                    type="button"
                                    wire:click="openRejectModal('location', {{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-xs transition flex items-center gap-1"
                                >
                                    <i class="fas fa-times text-[11px]"></i>
                                    <span>Reject</span>
                                </button>

                            {{-- If APPROVED / VERIFIED: show REJECT button --}}
                            @elseif($isLocVerified)
                                <button
                                    type="button"
                                    wire:click="openRejectModal('location', {{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold text-xs transition flex items-center gap-1"
                                    title="Revoke verification"
                                >
                                    <i class="fas fa-ban text-[11px]"></i>
                                    <span>Reject / Revoke</span>
                                </button>

                            {{-- If REJECTED: show APPROVE button --}}
                            @elseif($isLocRejected)
                                <button
                                    type="button"
                                    wire:click="approveLocation({{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1"
                                    title="Overturn rejection and approve"
                                >
                                    <i class="fas fa-check-circle text-[11px]"></i>
                                    <span>Re-Approve Location</span>
                                </button>

                            {{-- If UNVERIFIED: show verify button --}}
                            @else
                                <button
                                    type="button"
                                    wire:click="approveLocation({{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1"
                                    title="Verify location address"
                                >
                                    <i class="fas fa-check-circle text-[11px]"></i>
                                    <span>Verify Location</span>
                                </button>

                                <button
                                    type="button"
                                    wire:click="openRejectModal('location', {{ $loc->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold text-xs transition flex items-center gap-1"
                                >
                                    <i class="fas fa-times text-[11px]"></i>
                                    <span>Reject</span>
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-center">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto text-lg mb-2.5">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Operating Locations Found</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    The user has not added any addresses or workshops to their account profile yet.
                </p>
            </div>
        @endif
    </div>

    <!-- BANK ACCOUNTS SECTION (PROFILES & PAYOUTS) -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-950 dark:text-white flex items-center gap-2">
                    <i class="fas fa-university text-emerald-600"></i>
                    <span>Settlement & Bank Accounts</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Accounts used for marketplace settlements, payouts, and escrow withdrawals.
                </p>
            </div>
            <span class="text-xs text-slate-400 font-semibold">{{ $user->bankAccounts->count() }} Account(s)</span>
        </div>

        @if($user->bankAccounts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($user->bankAccounts as $bank)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-slate-900 dark:text-white text-xs">
                                    {{ $bank->bank_name }}
                                </span>
                                @if($bank->is_default)
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Primary
                                    </span>
                                @endif
                            </div>
                            <div class="font-mono text-sm font-black text-slate-800 dark:text-slate-200 mt-1">
                                {{ $bank->account_number }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $bank->account_name }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            @if($bank->verified_at)
                                <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-1">
                                    <i class="fas fa-check-circle text-[11px]"></i>
                                    <span>Verified</span>
                                </span>
                                <button
                                    type="button"
                                    wire:click="unverifyBankAccount({{ $bank->id }})"
                                    class="text-[11px] text-slate-400 hover:text-rose-600 transition"
                                >
                                    Unverify
                                </button>
                            @else
                                <span class="text-amber-500 text-xs font-bold flex items-center gap-1">
                                    <i class="fas fa-clock text-[11px]"></i>
                                    <span>Pending</span>
                                </span>
                                <button
                                    type="button"
                                    wire:click="verifyBankAccount({{ $bank->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition"
                                >
                                    Verify
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 italic">No bank accounts linked to this account.</p>
        @endif
    </div>

    <!-- REJECTION REASON MODAL -->
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
                                    Reject {{ ucfirst($selectedRejectType) }} Verification
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Please specify why this verification was rejected. The explanation will be recorded and communicated to the user.
                                </p>
                            </div>
                        </div>

                        <!-- Preset Reason Quick Chips -->
                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Common Rejection Reasons
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @if($selectedRejectType === 'location')
                                    @foreach([
                                            'Utility bill is older than 3 months',
                                            'Name on utility bill does not match profile',
                                            'Address on document does not match entered location',
                                            'Document is blurry, cropped, or illegible',
                                            'Invalid document type submitted',
                                            'Suspected altered or forged document'
                                        ] as $chip)
                                            <button
                                                type="button"
                                                wire:click="setPresetReason('{{ $chip }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-medium transition border {{ $rejectionReason === $chip ? 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-700' : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 hover:bg-slate-100' }}"
                                            >
                                                {{ $chip }}
                                            </button>
                                    @endforeach
                                @else
                                    @foreach([
                                            'Government ID is expired',
                                            'Name on ID does not match account name',
                                            'Document photos are blurry or unreadable',
                                            'Selfie does not match photo on government ID',
                                            'Liveness detection sequence failed',
                                            'Incomplete document submission (missing back)'
                                        ] as $chip)
                                            <button
                                                type="button"
                                                wire:click="setPresetReason('{{ $chip }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-medium transition border {{ $rejectionReason === $chip ? 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-700' : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 hover:bg-slate-100' }}"
                                            >
                                                {{ $chip }}
                                            </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Custom Reason Textarea -->
                        <div class="mt-4">
                            <label for="rejectionReason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Detailed Explanation <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="rejectionReason"
                                wire:model="rejectionReason"
                                rows="3"
                                placeholder="Explain why this was rejected so the user knows what corrections to submit..."
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

    <!-- FULL RESOLUTION DOCUMENT LIGHTBOX MODAL -->
    @if ($previewImageModalUrl)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="image-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" wire:click="closeImagePreview"></div>

                <!-- Modal Dialog -->
                <div class="relative inline-block align-middle bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 max-w-4xl w-full border border-slate-800">
                    <div class="p-4 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between text-white">
                        <h3 class="text-sm font-bold truncate max-w-md" id="image-modal-title">
                            {{ $previewImageModalTitle }}
                        </h3>
                        <div class="flex items-center gap-3">
                            <a 
                                href="{{ $previewImageModalUrl }}" 
                                target="_blank" 
                                class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1"
                            >
                                <i class="fas fa-external-link-alt"></i> Open original
                            </a>
                            <button 
                                type="button" 
                                wire:click="closeImagePreview" 
                                class="text-slate-400 hover:text-white p-1 rounded-lg transition"
                            >
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 flex items-center justify-center max-h-[80vh] overflow-auto bg-slate-950/50">
                        <img 
                            src="{{ $previewImageModalUrl }}" 
                            alt="{{ $previewImageModalTitle }}" 
                            class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-lg"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>