<div>
    @if(auth()->check() && !auth()->user()->bankAccounts()->exists())
        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-amber-50 border-2 border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-soft">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <i class="fas fa-university text-base"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        Add Bank Account to Receive Payments
                        <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold uppercase">Required for Sales</span>
                    </h4>
                    <p class="text-xs text-slate-600 mt-0.5">
                        You have not connected a bank account yet. Connect your bank account to receive direct transfer payments from buyers and platform payouts.
                    </p>
                </div>
            </div>
            <a href="{{ route('profile') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-xs transition shrink-0 text-center flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fas fa-plus-circle"></i> Add Bank Account
            </a>
        </div>
    @endif

    <!-- HERO BANNER -->
    <section class="rounded-2xl bg-gradient-to-br from-pp-900 via-pp-800 to-pp-600 text-white p-6 sm:p-8 shadow-soft overflow-hidden">
        <div class="max-w-3xl">
            <div class="text-xs font-bold uppercase tracking-[.16em] text-pp-200">
                Your Parts &amp; Parcel account
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-2">One account. Two ways to participate.</h1>
            <p class="mt-3 text-sm text-pp-100 leading-6">
                Buy devices and parts, make offers, follow purchases — or switch to selling and manage devices, listings, offers and payouts.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('category') }}" class="px-4 py-2.5 rounded-xl bg-white text-pp-800 dark:text-white hover:bg-slate-100 text-sm font-bold shadow-xs transition">
                    Browse Marketplace
                </a>
                <button onclick="openSection('seller')" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold transition cursor-pointer">
                    Go to Selling
                </button>
            </div>
        </div>
    </section>

    <!-- METRIC STATS GRID -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <!-- ACTIVE PURCHASES -->
        <a href="{{ route('invoices', ['scope' => 'buying']) }}" class="bg-white border border-slate-200 hover:border-pp-300 rounded-2xl p-5 shadow-card transition group block">
            <span class="text-xs font-semibold text-slate-500">Active purchases</span>
            <div class="text-2xl font-extrabold mt-2 text-slate-900 group-hover:text-pp-600 transition">{{ $activePurchasesCount }}</div>
            <small class="text-slate-400">
                @if($awaitingDeliveryCount > 0)
                    {{ $awaitingDeliveryCount }} awaiting delivery
                @else
                    {{ $activePurchasesCount > 0 ? 'All delivered' : 'No active purchases' }}
                @endif
            </small>
        </a>

        <!-- CART -->
        <a href="{{ route('cart') }}" class="bg-white border border-slate-200 hover:border-pp-300 rounded-2xl p-5 shadow-card transition group block">
            <span class="text-xs font-semibold text-slate-500">Cart</span>
            <div class="text-2xl font-extrabold mt-2 text-slate-900 group-hover:text-pp-600 transition">{{ $cartItemsCount }}</div>
            <small class="text-slate-400">
                @if($cartSellersCount > 0)
                    Across {{ $cartSellersCount }} {{ \Illuminate\Support\Str::plural('seller', $cartSellersCount) }}
                @else
                    Cart is empty
                @endif
            </small>
        </a>

        <!-- YOUR LISTINGS -->
        <a href="{{ route('mylistings') }}" class="bg-white border border-slate-200 hover:border-pp-300 rounded-2xl p-5 shadow-card transition group block">
            <span class="text-xs font-semibold text-slate-500">Your listings</span>
            <div class="text-2xl font-extrabold mt-2 text-slate-900 group-hover:text-pp-600 transition">{{ $totalListingsCount }}</div>
            <small class="text-slate-400">{{ $activeListingsCount }} active · {{ $soldListingsCount }} sold</small>
        </a>

        <!-- PENDING OFFERS -->
        <a href="{{ route('offers') }}" class="bg-white border border-slate-200 hover:border-pp-300 rounded-2xl p-5 shadow-card transition group block">
            <span class="text-xs font-semibold text-slate-500">Pending offers</span>
            <div class="text-2xl font-extrabold mt-2 text-slate-900 group-hover:text-pp-600 transition">{{ $totalPendingOffersCount }}</div>
            <small class="{{ $offersNeedingAttentionCount > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                @if($offersNeedingAttentionCount > 0)
                    {{ $offersNeedingAttentionCount }} {{ \Illuminate\Support\Str::plural('need', $offersNeedingAttentionCount) }} your response
                @else
                    All caught up
                @endif
            </small>
        </a>
    </section>

    <!-- BUYING & SELLING WORKSPACES -->
    <section class="grid xl:grid-cols-2 gap-6 mt-6">
        <!-- 🛍 BUYING ACTIVITY -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🛍</span> Buying
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Your activity as a buyer</p>
                </div>
                <a class="text-xs font-bold text-pp-600 hover:underline" href="{{ route('invoices', ['scope' => 'buying']) }}">View all →</a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($recentPurchases as $purchase)
                    <a href="{{ route('invoices.view', ['invoice_id' => $purchase['id']]) }}" class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition flex justify-between items-center gap-3 block">
                        <div class="min-w-0">
                            <b class="text-sm text-slate-900 truncate block">{{ $purchase['title'] }}</b>
                            <p class="text-xs text-slate-500 mt-1">Invoice #{{ $purchase['invoice_number'] }} · {{ $purchase['delivery_status'] }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <b class="text-sm text-slate-900">{{ $purchase['currency_symbol'] }}{{ number_format($purchase['amount'], 2) }}</b>
                            <p class="text-[11px] font-semibold {{ $purchase['status_color'] === 'emerald' ? 'text-emerald-600' : ($purchase['status_color'] === 'amber' ? 'text-amber-600' : ($purchase['status_color'] === 'blue' ? 'text-blue-600' : 'text-slate-500')) }}">
                                {{ $purchase['status_text'] }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-bag text-slate-300 text-3xl mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium">No recent purchases found.</p>
                        <a href="{{ route('category') }}" class="text-xs font-bold text-pp-600 hover:underline mt-1 inline-block">Browse Marketplace →</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 📦 SELLING ACTIVITY -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📦</span> Selling
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Your activity as a seller</p>
                </div>
                <a class="text-xs font-bold text-pp-600 hover:underline" href="{{ route('mylistings') }}">Seller center →</a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-3 gap-3">
                    <a href="{{ route('mylistings') }}" class="rounded-xl bg-slate-50 hover:bg-slate-100/70 transition p-3 block">
                        <small class="text-slate-500 font-medium">Active listings</small>
                        <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $activeListingsCount }}</div>
                    </a>
                    <a href="{{ route('offers') }}" class="rounded-xl bg-slate-50 hover:bg-slate-100/70 transition p-3 block">
                        <small class="text-slate-500 font-medium">Awaiting action</small>
                        <div class="text-xl font-extrabold mt-0.5 {{ $offersNeedingAttentionCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                            {{ $offersNeedingAttentionCount }}
                        </div>
                    </a>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <small class="text-slate-500 font-medium">Available payout</small>
                        <div class="text-xl font-extrabold text-slate-900 mt-0.5">
                            @if($availablePayout >= 1000000)
                                {{ $currencySymbol }}{{ number_format($availablePayout / 1000000, 1) }}M
                            @elseif($availablePayout >= 1000)
                                {{ $currencySymbol }}{{ number_format($availablePayout / 1000, 0) }}k
                            @else
                                {{ $currencySymbol }}{{ number_format($availablePayout) }}
                            @endif
                        </div>
                    </div>
                </div>

                @if($offersNeedingAttentionCount > 0)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 flex justify-between items-center gap-3">
                        <div>
                            <b class="text-xs text-amber-900 font-extrabold">
                                {{ $offersNeedingAttentionCount }} {{ \Illuminate\Support\Str::plural('offer', $offersNeedingAttentionCount) }} need attention
                            </b>
                            <p class="text-[11px] text-amber-700 mt-0.5">
                                {{ $buyersWaitingCount }} {{ \Illuminate\Support\Str::plural('buyer is', $buyersWaitingCount) }} waiting for your response.
                            </p>
                        </div>
                        <a href="{{ route('offers') }}" class="px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shrink-0 transition">
                            Review offers
                        </a>
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('mylistings') }}" class="py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white text-xs font-bold text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fas fa-layer-group text-xs"></i> Manage Listings
                    </a>
                    <a href="{{ route('myitems') }}" class="py-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fas fa-boxes-stacked text-xs"></i> Manage Devices
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- RECENT ACTIVITY & COMMUNITY -->
    <section class="grid xl:grid-cols-[1.5fr_1fr] gap-6 mt-6">
        <!-- RECENT ACTIVITY TIMELINE -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="font-extrabold text-slate-900">Recent activity</h2>
                <p class="text-xs text-slate-500 mt-1">One timeline across buying and selling</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentActivities as $activity)
                    <a href="{{ $activity['url'] }}" class="p-4 block hover:bg-slate-50 transition">
                        <b class="text-sm text-slate-900">{{ $activity['title'] }}</b>
                        <p class="text-xs text-slate-500 mt-1">{{ $activity['subtitle'] }}</p>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <i class="fas fa-history text-slate-300 text-2xl mb-1.5"></i>
                        <p class="font-medium">No recent activity recorded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- COMMUNITY FORUM -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-card p-5">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-slate-900">Community</h2>
                    <p class="text-xs text-slate-500 mt-1">Your forum activity</p>
                </div>
                <a href="{{ route('subscriptions') }}" class="px-2.5 py-1 rounded-full bg-pp-50 hover:bg-pp-100 text-pp-700 text-[10px] font-bold transition">
                    {{ $responsesRemaining }} {{ \Illuminate\Support\Str::plural('response', $responsesRemaining) }} left
                </a>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($communityDiscussions as $discussion)
                    <a href="{{ route('community.request', ['id' => $discussion->id]) }}" class="rounded-xl bg-slate-50 hover:bg-slate-100/70 p-4 block transition">
                        <b class="text-xs text-slate-900 line-clamp-1">{{ $discussion->title }}</b>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                            <span>{{ $discussion->responses_count }} {{ \Illuminate\Support\Str::plural('response', $discussion->responses_count) }}</span>
                            <span>·</span>
                            <span>{{ $discussion->offers_count }} {{ \Illuminate\Support\Str::plural('offer', $discussion->offers_count) }}</span>
                            <span>·</span>
                            <span>{{ $discussion->created_at->diffForHumans() }}</span>
                        </p>
                    </a>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fas fa-comments text-slate-300 text-2xl mb-1.5"></i>
                        <p class="font-medium">No community activity yet.</p>
                        <a href="{{ route('community') }}" class="text-xs font-bold text-pp-600 hover:underline mt-1 inline-block">Explore Community →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>