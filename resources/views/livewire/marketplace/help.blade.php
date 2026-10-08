<div class="bg-slate-50/60 dark:bg-slate-950 min-h-screen pb-20">

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden bg-gradient-to-b from-pp-900 via-pp-800 to-pp-900 text-white pt-12 pb-20 px-4 sm:px-6 lg:px-8">
        <!-- Ambient Decorative Glows -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-pp-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="relative max-w-5xl mx-auto text-center space-y-6">
            <!-- Breadcrumb / Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-bold text-pp-200">
                <i class="fas fa-circle-question text-amber-400"></i>
                <span>Parts &amp; Parcel Knowledge Hub &amp; Support</span>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white">
                How can we help you today?
            </h1>
            <p class="text-sm sm:text-base text-pp-200/90 max-w-2xl mx-auto font-medium">
                Everything you need to know about buying parts, escrow protection, seller subscriptions, offers, and delivery logistics across Nigeria &amp; West Africa.
            </p>

            <!-- SEARCH BAR -->
            <div class="max-w-2xl mx-auto pt-2">
                <div class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-white/20 dark:border-slate-800 p-2 text-slate-800 dark:text-slate-200 transition focus-within:ring-4 focus-within:ring-pp-400/30">
                    <span class="pl-3 pr-2 text-slate-400 dark:text-slate-500 text-base">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search answers, e.g. 'escrow hold', 'returns', 'make offer', 'seller subscription'..."
                        class="w-full bg-transparent border-0 py-2.5 px-2 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden"
                    />
                    @if ($search !== '' || $selectedTopic !== '')
                        <button
                            type="button"
                            wire:click="clearFilters"
                            class="px-3 py-1.5 text-xs font-bold text-slate-400 hover:text-rose-500 transition mr-2 cursor-pointer"
                            title="Clear search"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                    <div class="px-5 py-2.5 rounded-xl bg-pp-600 text-white font-bold text-xs shadow-soft shrink-0">
                        Search
                    </div>
                </div>

                <!-- POPULAR SEARCH PILLS -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4 text-xs text-pp-200">
                    <span class="text-pp-300/80 font-medium">Quick Topics:</span>
                    <button
                        type="button"
                        wire:click="filterTopic('Escrow & Secure Payments')"
                        class="px-2.5 py-1 rounded-lg transition text-[11px] font-semibold cursor-pointer {{ $selectedTopic === 'Escrow & Secure Payments' ? 'bg-emerald-500 text-white ring-2 ring-emerald-300' : 'bg-white/10 hover:bg-white/20 text-white' }}"
                    >
                        <i class="fas fa-shield-halved text-emerald-400 mr-1"></i> Escrow Protection
                    </button>
                    <button
                        type="button"
                        wire:click="filterTopic('Buying & Making Offers')"
                        class="px-2.5 py-1 rounded-lg transition text-[11px] font-semibold cursor-pointer {{ $selectedTopic === 'Buying & Making Offers' ? 'bg-amber-500 text-white ring-2 ring-amber-300' : 'bg-white/10 hover:bg-white/20 text-white' }}"
                    >
                        <i class="fas fa-handshake-simple text-amber-400 mr-1"></i> Making Offers
                    </button>
                    <button
                        type="button"
                        wire:click="filterTopic('Shipping & Deliveries')"
                        class="px-2.5 py-1 rounded-lg transition text-[11px] font-semibold cursor-pointer {{ $selectedTopic === 'Shipping & Deliveries' ? 'bg-blue-500 text-white ring-2 ring-blue-300' : 'bg-white/10 hover:bg-white/20 text-white' }}"
                    >
                        <i class="fas fa-truck-fast text-blue-400 mr-1"></i> Shipping &amp; Logistics
                    </button>
                    <button
                        type="button"
                        wire:click="filterTopic('Seller Tiers & Plans')"
                        class="px-2.5 py-1 rounded-lg transition text-[11px] font-semibold cursor-pointer {{ $selectedTopic === 'Seller Tiers & Plans' ? 'bg-rose-500 text-white ring-2 ring-rose-300' : 'bg-white/10 hover:bg-white/20 text-white' }}"
                    >
                        <i class="fas fa-store text-rose-400 mr-1"></i> Seller Plans
                    </button>
                    <button
                        type="button"
                        wire:click="filterTopic('Disputes & Mediation')"
                        class="px-2.5 py-1 rounded-lg transition text-[11px] font-semibold cursor-pointer {{ $selectedTopic === 'Disputes & Mediation' ? 'bg-indigo-500 text-white ring-2 ring-indigo-300' : 'bg-white/10 hover:bg-white/20 text-white' }}"
                    >
                        <i class="fas fa-scale-balanced text-indigo-400 mr-1"></i> Disputes
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 space-y-16">

        <!-- 3 QUICK ACTION HIGHLIGHT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card 1: Track Order & Escrow -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-soft border border-slate-200/80 dark:border-slate-800 flex items-start gap-4 hover:-translate-y-1 transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-box-check"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Track Order &amp; Escrow</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        View real-time dispatch progress, waybill info, and funds safely locked in escrow until your inspection is complete.
                    </p>
                    <a href="{{ route('invoices') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-pp-600 dark:text-pp-400 hover:underline pt-1">
                        <span>Check Invoices</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: 48-Hour Inspection & Return -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-soft border border-slate-200/80 dark:border-slate-800 flex items-start gap-4 hover:-translate-y-1 transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-scale-balanced"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">48hr Inspection Period</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Received a part that doesn't fit or work? Open a dispute before the 48-hour inspection countdown expires to pause payout.
                    </p>
                    <a href="{{ route('disputes') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline pt-1">
                        <span>Dispute Resolution</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Contact Support Team -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-soft border border-slate-200/80 dark:border-slate-800 flex items-start gap-4 hover:-translate-y-1 transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-pp-50 dark:bg-pp-950/60 text-pp-600 dark:text-pp-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Need Direct Help?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Our dedicated support agents in Lagos and Abuja are available via Live Chat, WhatsApp, or email ticket.
                    </p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-pp-600 dark:text-pp-400 hover:underline pt-1">
                        <span>Contact Support</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- SEARCH RESULTS (WHEN ACTIVE) -->
        @if ($isSearching)
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-soft space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                            Search Results
                            @if ($search) for "<span class="text-pp-600 dark:text-pp-400">{{ $search }}</span>" @endif
                            @if ($selectedTopic) in <span class="text-amber-500">{{ $selectedTopic }}</span> @endif
                        </h3>
                        <p class="text-xs text-slate-400">Found {{ $searchResults->count() }} matching {{ Str::plural('article', $searchResults->count()) }}</p>
                    </div>
                    <button type="button" wire:click="clearFilters" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-times-circle"></i>
                        <span>Clear Filter</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($searchResults as $article)
                        <a href="{{ route('help.show', $article->slug) }}" class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 hover:border-pp-300 dark:hover:border-pp-500/50 transition flex flex-col justify-between space-y-3 group shadow-2xs">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 mb-1">
                                    {{ $article->help_topic }}
                                </span>
                                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white group-hover:text-pp-600 dark:group-hover:text-pp-400 transition leading-snug">
                                    {{ $article->title }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1">
                                    {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                                </p>
                            </div>
                            <span class="text-[11px] font-bold text-pp-600 dark:text-pp-400 flex items-center gap-1 pt-1">
                                <span>Read guide</span> <i class="fas fa-arrow-right text-[9px]"></i>
                            </span>
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 space-y-2">
                            <i class="fas fa-search text-3xl text-slate-300 dark:text-slate-700"></i>
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-300">No help articles matched your search.</p>
                            <p class="text-[11px] text-slate-400">Try searching for keywords like "escrow", "offers", "shipping", "inspection" or <a href="{{ route('contact') }}" class="text-pp-600 font-bold hover:underline">contact support</a>.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- TOPIC DIRECTORY (6 CORE CATEGORIES) -->
        @if (! $isSearching)
        <section class="space-y-8">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Explore Help by Topic
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Browse our organized guides for buyers, automotive specialists, device technicians, and scrap dealers.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- 1. BUYING & MAKING OFFERS -->
                <div id="offers-section" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Buying &amp; Making Offers</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Learn how to browse inventory, negotiate custom pricing, submit single-item offers, and lock items before they sell out.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Buying & Making Offers']) && $articlesByTopic['Buying & Making Offers']->count() > 0)
                                @foreach ($articlesByTopic['Buying & Making Offers'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-pp-600 dark:hover:text-pp-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-pp-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>How to make an offer on a listing</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Counter-offers and seller negotiation</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Checking part compatibility &amp; condition</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>3-day offer expiration policy</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="filterTopic('Buying & Making Offers')" class="text-[11px] font-bold text-pp-600 dark:text-pp-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

                <!-- 2. ESCROW & SAFE PAYMENTS -->
                <div id="escrow-section" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Escrow &amp; Secure Payments</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Your money is safe. Funds are held in neutral escrow until you receive and verify the item matches description.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Escrow & Secure Payments']) && $articlesByTopic['Escrow & Secure Payments']->count() > 0)
                                @foreach ($articlesByTopic['Escrow & Secure Payments'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-emerald-600 dark:hover:text-emerald-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-emerald-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>How Parts &amp; Parcel Escrow works</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Supported payments: Paystack &amp; Flutterwave</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Multi-currency transactions (NGN, USD, GBP)</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>When do sellers receive payout?</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="filterTopic('Escrow & Secure Payments')" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. SHIPPING & PARCEL LOGISTICS -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Shipping &amp; Deliveries</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Nationwide parcel dispatch, waybill tracking, fragile auto parts packaging, and hub pickup options.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Shipping & Deliveries']) && $articlesByTopic['Shipping & Deliveries']->count() > 0)
                                @foreach ($articlesByTopic['Shipping & Deliveries'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-blue-600 dark:hover:text-blue-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-blue-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Doorstep delivery vs. Hub pickup</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>How to input waybill tracking numbers</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Shipping heavy salvage &amp; engine blocks</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>What to do if a package arrives damaged</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="filterTopic('Shipping & Deliveries')" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

                <!-- 4. SELLER TIERS & OPERATIONS -->
                <div id="seller-section" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Seller Tiers &amp; Plans</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Grow your automotive shop or gadget refurbishing brand. Starter, Pro, and Enterprise membership privileges.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Seller Tiers & Plans']) && $articlesByTopic['Seller Tiers & Plans']->count() > 0)
                                @foreach ($articlesByTopic['Seller Tiers & Plans'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-amber-600 dark:hover:text-amber-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-amber-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Comparing Starter Free, Pro &amp; Enterprise</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Listing limits &amp; featured promotion boosts</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>How to get the Verified Vendor Badge</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Escrow commission discounts &amp; fee caps</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <button type="button" wire:click="filterTopic('Seller Tiers & Plans')" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                        <a href="{{ route('pricing') }}" class="text-[11px] font-bold text-pp-600 hover:underline">
                            Pricing
                        </a>
                    </div>
                </div>

                <!-- 5. COMMUNITY REQUESTS (RFQS) -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-users-gear"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Community Requests (RFQs)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Can't find a rare part? Post a request to thousands of verified auto mechanics, dismantlers, and electronics technicians.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Community Requests (RFQs)']) && $articlesByTopic['Community Requests (RFQs)']->count() > 0)
                                @foreach ($articlesByTopic['Community Requests (RFQs)'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-purple-600 dark:hover:text-purple-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-purple-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Posting a "Need a Part" community RFQ</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Receiving &amp; comparing quotes from sellers</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Vendor daily response quotas</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Converting an accepted quote into an invoice</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <button type="button" wire:click="filterTopic('Community Requests (RFQs)')" class="text-[11px] font-bold text-purple-600 dark:text-purple-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                        <a href="{{ route('community') }}" class="text-[11px] font-bold text-pp-600 hover:underline">
                            Community
                        </a>
                    </div>
                </div>

                <!-- 6. RETURNS, DISPUTES & MEDIATION -->
                <div id="inspection-section" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-soft transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base mb-4">
                            <i class="fas fa-handshake-angle"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-2">Disputes &amp; Mediation</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            How Parts &amp; Parcel arbitrates disagreements, handles defective components, and protects both parties fairly.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-medium border-t border-slate-100 dark:border-slate-800 pt-3">
                            @if (!empty($articlesByTopic['Disputes & Mediation']) && $articlesByTopic['Disputes & Mediation']->count() > 0)
                                @foreach ($articlesByTopic['Disputes & Mediation'] as $art)
                                    <li>
                                        <a href="{{ route('help.show', $art->slug) }}" class="flex items-center justify-between hover:text-rose-600 dark:hover:text-rose-400 transition group">
                                            <span class="group-hover:translate-x-0.5 transition-transform truncate pr-2">{{ $art->title }}</span>
                                            <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-rose-600 shrink-0"></i>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Filing a dispute within 48 hours</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Submitting photo and video proof</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Neutral arbitration procedure &amp; timeline</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                                <li class="flex items-center justify-between hover:text-pp-600 transition cursor-pointer">
                                    <span>Return shipping costs &amp; full refund release</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="filterTopic('Disputes & Mediation')" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 hover:underline cursor-pointer">
                            <span>View all guides</span> <i class="fas fa-arrow-right text-[9px]"></i>
                        </button>
                    </div>
                </div>

            </div>
        </section>
        @endif

        <!-- STEP-BY-STEP "HOW IT WORKS" VISUAL FLOW -->
        <section class="bg-gradient-to-br from-pp-900 to-indigo-950 rounded-3xl p-8 sm:p-12 text-white relative overflow-hidden shadow-soft">
            <div class="relative z-10 max-w-3xl space-y-3">
                <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-pp-200 tracking-wider uppercase">
                    Guaranteed Protection
                </span>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    The 4-Step Parts &amp; Parcel Escrow Journey
                </h2>
                <p class="text-xs sm:text-sm text-pp-200">
                    No payment is ever sent directly to the vendor upfront. Here is how your transactions are protected:
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-10 relative z-10">
                <!-- Step 1 -->
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-2.5">
                    <div class="w-8 h-8 rounded-full bg-pp-500 text-white font-black text-xs flex items-center justify-center">
                        1
                    </div>
                    <h4 class="font-extrabold text-sm text-white">Find or Request</h4>
                    <p class="text-xs text-pp-200 leading-relaxed">
                        Search catalog listings or post a Community Request with your part specifications, VIN, or OEM part number.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-2.5">
                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white font-black text-xs flex items-center justify-center">
                        2
                    </div>
                    <h4 class="font-extrabold text-sm text-white">Negotiate Offer</h4>
                    <p class="text-xs text-pp-200 leading-relaxed">
                        Agree on price, shipping method, and warranty with the seller via the structured offer drawer or direct checkout.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white font-black text-xs flex items-center justify-center">
                        3
                    </div>
                    <h4 class="font-extrabold text-sm text-white">Escrow Payment</h4>
                    <p class="text-xs text-pp-200 leading-relaxed">
                        Pay securely using card or bank transfer. Funds are safely locked in escrow while the vendor prepares dispatch.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-2.5">
                    <div class="w-8 h-8 rounded-full bg-blue-500 text-white font-black text-xs flex items-center justify-center">
                        4
                    </div>
                    <h4 class="font-extrabold text-sm text-white">Inspect &amp; Release</h4>
                    <p class="text-xs text-pp-200 leading-relaxed">
                        You have 48 hours to inspect fitting and condition. Once verified, escrow releases payment to the seller.
                    </p>
                </div>
            </div>
        </section>

        <!-- FREQUENTLY ASKED QUESTIONS (ACCORDION SECTION) -->
        <section class="space-y-8">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-pp-600 dark:text-pp-400">Instant Answers</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Frequently Asked Questions
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Quick solutions to common questions from both buyers and sellers.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-6xl mx-auto">

                <!-- FAQ 1 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>How does Parts &amp; Parcel Escrow protect my money?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        When you pay for an item or accept a seller's invoice, the money is not sent directly to the vendor's bank account. Instead, it is locked in a secure neutral escrow vault. The vendor only receives funds after you confirm delivery and complete your 48-hour inspection window without filing a dispute.
                    </p>
                </details>

                <!-- FAQ 2 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>What if the part received does not fit or is defective?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        You have a mandatory 48-hour inspection period starting from the moment delivery is marked as completed. If the part doesn't fit, doesn't match descriptions, or doesn't work, simply click "Raise Dispute" on your invoice. Escrow release is instantly frozen until the seller provides an exchange or our team authorizes a full refund.
                    </p>
                </details>

                <!-- FAQ 3 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>Are offers legally binding before payment?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        Offers and counter-offers represent agreed commercial pricing terms, but they are not final or binding until payment is completed. All listings remain subject to real-time inventory availability. When you proceed to checkout an accepted offer, the stock quantity is locked temporarily for your payment.
                    </p>
                </details>

                <!-- FAQ 4 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>How do Community Requests (RFQs) work?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        If you cannot find a specific part in our marketplace catalog, navigate to the Community page and submit an RFQ. Detail the vehicle make, model, year, or device specs with photos. Qualified vendors submit offers directly to your request. You can compare warranty, price, and reputation before choosing the best quote.
                    </p>
                </details>

                <!-- FAQ 5 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>How are seller subscription plans charged?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        Sellers can choose between monthly or annual billing. All accounts start on the Starter Free tier. Upgrading to Pro or Enterprise provides higher daily RFQ quote responses, larger inventory capacity, lower escrow commission percentages, fee caps, and priority placement in search results.
                    </p>
                </details>

                <!-- FAQ 6 -->
                <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs open:border-pp-500/50 open:ring-2 open:ring-pp-500/10 transition">
                    <summary class="flex items-center justify-between font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none">
                        <span>What payment options are available?</span>
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xs group-open:rotate-180 transition-transform">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </summary>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800 pt-3">
                        We partner with Paystack and Flutterwave to support all major debit/credit cards (Mastercard, Visa, Verve), direct bank transfers, USSD codes, and international cards. All payments are encrypted with PCI-DSS Level 1 compliance.
                    </p>
                </details>

            </div>
        </section>

        <!-- STILL NEED HELP? CTA BRIDGE -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-slate-200/80 dark:border-slate-800 shadow-soft flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-left">
                <span class="px-3 py-1 rounded-full bg-pp-50 dark:bg-pp-950/60 text-pp-700 dark:text-pp-300 font-extrabold text-[11px] uppercase tracking-wider">
                    24/7 Dedicated Support
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                    Didn't find what you are looking for?
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-xl">
                    Our support engineers and escrow arbitration specialists are available to answer inquiries, verify parts, or assist with disputes.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('contact') }}"
                    class="px-6 py-3 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-envelope"></i>
                    <span>Contact Support Team</span>
                </a>
            </div>
        </section>

    </div>

</div>