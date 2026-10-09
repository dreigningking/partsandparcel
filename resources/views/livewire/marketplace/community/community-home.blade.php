<main class="w-full pb-16" x-data="{ showPostModal: @entangle('showPostModal') }" x-init="$watch('showPostModal', value => { document.body.style.overflow = value ? 'hidden' : '' })">
    <!-- ============================================================
    BREADCRUMB (CONSISTENT WITH ITEM DETAILS & CATEGORY)
    ============================================================ -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('welcome') }}" class="hover:text-slate-700">Home</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Community Hub</span>
        </div>
    </div>

    <!-- ============================================================
    HERO SECTION (CONSISTENT WITH MARKETPLACE BANNER)
    ============================================================ -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="bg-gradient-to-r via-white to-pp-50/50 rounded-2xl border border-pp-100 p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 shadow-2xs">
            <div class="max-w-xl">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Community <span class="text-pp-600">Hub</span>
                </h1>
                <div class="text-xs sm:text-sm font-extrabold text-pp-700 mt-1">Ask. Find. Offer. Solve.</div>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">Find products, parts, technicians, transporters and practical solutions from the Parts &amp; Parcel community.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <button type="button" @click="showPostModal = true" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-plus-circle"></i> Post a Request
                    </button>
                    <button onclick="document.getElementById('requestListSection').scrollIntoView({ behavior: 'smooth' })" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-pp-300 hover:bg-pp-50 text-slate-700 hover:text-pp-700 font-bold text-xs transition flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-list-ul"></i> Browse Requests
                    </button>
                </div>
            </div>

            <!-- STATS COUNTER -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shrink-0 shadow-2xs">
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">{{ $stats['open_requests'] ?? '1,248' }}</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Open Requests</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">{{ $stats['offers_received'] ?? '3,721' }}</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Offers Received</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">{{ $stats['active_members'] ?? '9,430' }}</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Active Members</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">{{ $stats['fulfilled'] ?? '2,186' }}</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Fulfilled</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    MAIN CONTENT & SIDEBAR LAYOUT
    ============================================================ -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-[280px_1fr] gap-7 items-start">

            <!-- SIDEBAR (WEB VIEW) -->
            <aside class="hidden lg:block sticky top-24">
                <livewire:components.filters.community-filter :isMobile="false" key="desktop-filter" />
            </aside>

            <!-- MAIN CONTENT -->
            <main class="min-w-0" id="requestListSection">

                <!-- Mobile Filter Toggle Button -->
                <button onclick="toggleMobileFilterDrawer()" class="lg:hidden w-full py-3 px-4 mb-4 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-slate-800 flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                    <i class="fas fa-sliders-h text-pp-600"></i> Filters &amp; Options
                </button>

                <!-- TABS (CONSISTENT WITH CATEGORY INTENT TABS) -->
                <div class="border-b border-slate-200 mb-5">
                    <nav class="-mb-px flex space-x-2 sm:space-x-3 overflow-x-auto no-scrollbar" aria-label="Tabs">
                        <button wire:click="setTab('all')" class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 transition cursor-pointer rounded-t-xl whitespace-nowrap {{ $tab === 'all' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">All Requests</button>
                        <button wire:click="setTab('products')" class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 transition cursor-pointer rounded-t-xl whitespace-nowrap {{ $tab === 'products' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">Products &amp; Parts</button>
                        <button wire:click="setTab('repairs')" class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 transition cursor-pointer rounded-t-xl whitespace-nowrap {{ $tab === 'repairs' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">Repairs &amp; Services</button>
                        <button wire:click="setTab('questions')" class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 transition cursor-pointer rounded-t-xl whitespace-nowrap {{ $tab === 'questions' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900 hover:border-slate-300' }}">Questions &amp; Advice</button>
                    </nav>
                </div>

                <!-- TOOLBAR & SEARCH BAR -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 mb-5 shadow-2xs">
                    <div class="text-xs text-slate-600 font-medium">
                        @if($totalRequests > 0)
                            Showing <strong class="text-slate-900 font-bold">{{ $startDisplay }}–{{ $endDisplay }}</strong> of <strong class="text-slate-900 font-bold">{{ $totalRequests }}</strong> open requests
                        @else
                            Showing <strong class="text-slate-900 font-bold">0</strong> requests
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label for="sortSelect" class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Sort</label>
                        <select wire:model.live="sort" id="sortSelect" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            <option value="relevance">Relevance</option>
                            <option value="newest">Newest</option>
                            <option value="offers">Most Offers</option>
                            <option value="budget">Highest Budget</option>
                        </select>
                    </div>
                </div>

                <!-- REQUEST CARDS LIST -->
                <div class="space-y-4">
                    @forelse($requests as $r)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs hover:shadow-card hover:border-pp-200 transition duration-200 space-y-3">
                            <!-- META HEADER -->
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                        <i class="fas fa-user-circle text-pp-600"></i> {{ $r['name'] }}
                                        @if(!empty($r['verified']))
                                            <span class="px-1.5 py-0.2 rounded-full bg-pp-50 text-pp-700 font-bold text-[9px] border border-pp-100" title="Verified Member">✓ Verified</span>
                                        @endif
                                    </span>
                                    <span class="flex items-center gap-1 text-slate-500"><i class="fas fa-map-pin text-slate-400 text-[10px]"></i> {{ $r['location'] }}</span>
                                    <span class="flex items-center gap-1 text-slate-400"><i class="far fa-clock text-[10px]"></i> {{ $r['time'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!empty($r['brand']))
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] border border-slate-200">
                                            <i class="fas fa-tag text-slate-400 mr-1"></i> {{ $r['brand'] }} @if(!empty($r['model'])) · {{ $r['model'] }} @endif
                                        </span>
                                    @endif
                                    <span class="px-2.5 py-0.5 rounded-full bg-pp-50 text-pp-700 font-extrabold text-[10px] uppercase tracking-wider border border-pp-100">{{ $r['type'] }}</span>
                                </div>
                            </div>

                            <!-- TITLE & DESCRIPTION -->
                            <div>
                                <a href="{{ route('community.request', ['id' => $r['id']]) }}" class="hover:text-pp-600 transition inline-block">
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">{{ $r['title'] }}</h3>
                                </a>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-2 mt-1">{{ $r['desc'] }}</p>
                            </div>

                            <!-- ATTACHED MEDIA PREVIEW STRIP (PHOTO, VIDEO, PDF) -->
                            @if(!empty($r['media']) && count($r['media']) > 0)
                                <div class="pt-1">
                                    <div class="flex items-center gap-2 overflow-x-auto pb-1">
                                        @foreach(array_slice($r['media'], 0, 4) as $idx => $m)
                                            <a href="{{ route('community.request', ['id' => $r['id']]) }}" class="relative w-14 h-14 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 shrink-0 group hover:border-pp-600 transition" title="{{ $m['name'] ?? 'Media Attachment' }}">
                                                @if($m['type'] === 'image')
                                                    <img src="{{ $m['url'] }}" alt="{{ $m['name'] ?? 'Media' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" />
                                                    <span class="absolute bottom-1 right-1 bg-slate-950/70 text-white text-[8px] font-bold px-1 rounded"><i class="fas fa-image"></i></span>
                                                @elseif($m['type'] === 'video')
                                                    <div class="w-full h-full bg-slate-900 flex items-center justify-center text-white">
                                                        <i class="fas fa-play text-xs text-pp-400 group-hover:scale-110 transition"></i>
                                                    </div>
                                                    <span class="absolute bottom-1 right-1 bg-slate-950/80 text-amber-400 text-[8px] font-bold px-1 rounded">VID</span>
                                                @else
                                                    <div class="w-full h-full bg-rose-50 flex flex-col items-center justify-center p-1 text-center">
                                                        <i class="fas fa-file-pdf text-rose-600 text-sm group-hover:scale-110 transition"></i>
                                                        <span class="text-[8px] font-extrabold text-rose-700 truncate w-full mt-0.5">DOC</span>
                                                    </div>
                                                @endif

                                                @if($idx === 3 && count($r['media']) > 4)
                                                    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-2xs flex items-center justify-center text-white font-extrabold text-xs">
                                                        +{{ count($r['media']) - 3 }}
                                                    </div>
                                                @endif
                                            </a>
                                        @endforeach
                                        <span class="text-[11px] text-slate-400 font-semibold pl-1">
                                            {{ count($r['media']) }} {{ count($r['media']) === 1 ? 'file' : 'files' }} attached
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- CARD FOOTER & SPECIFICATION PILLS -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold text-xs border border-pp-100 flex items-center gap-1.5">
                                        <i class="fas fa-handshake"></i> {{ $r['offers'] }} Offers
                                    </span>
                                    @if(!empty($r['budget']))
                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-900 font-extrabold text-xs">
                                            {{ $r['budget'] }}
                                        </span>
                                    @endif
                                    @if(!empty($r['fulfillment']))
                                        <span class="px-2.5 py-1 rounded-full bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200/80 flex items-center gap-1.5">
                                            <i class="fas fa-truck-pickup text-slate-400 text-[11px]"></i> {{ $r['fulfillment'] }}
                                        </span>
                                    @endif
                                    @if(!empty($r['urgency']) && $r['urgency'] !== 'Flexible')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200 flex items-center gap-1">
                                            <i class="fas fa-bolt text-amber-500 text-[10px]"></i> {{ $r['urgency'] }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-400 flex items-center gap-3 pl-1">
                                        <span><i class="fas fa-comment-dots text-slate-400"></i> {{ $r['replies'] ?? 0 }}</span>
                                        <span><i class="fas fa-eye text-slate-400"></i> {{ $r['views'] ?? 1 }}</span>
                                    </span>
                                </div>
                                <div>
                                    @if($r['type'] === 'Question / Advice')
                                        <a href="{{ route('community.request', ['id' => $r['id']]) }}" class="px-4 py-2 rounded-xl border border-pp-600 text-pp-700 hover:bg-pp-50 font-extrabold text-xs transition inline-flex items-center gap-1.5">
                                            <i class="fas fa-comment"></i> Reply
                                        </a>
                                    @else
                                        <a href="{{ route('community.request', ['id' => $r['id']]) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">
                                            <i class="fas fa-arrow-right"></i> View &amp; Submit Offer
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
                            <i class="fas fa-inbox text-3xl text-slate-300 mb-3 block"></i>
                            <p class="font-extrabold text-sm text-slate-900">No requests match your filters</p>
                            <p class="text-xs text-slate-500 mt-1">Try adjusting your filters or search terms.</p>
                        </div>
                    @endforelse
                </div>

                <!-- PAGINATION CONTROLS -->
                @if($totalPages > 1)
                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <span class="text-xs text-slate-500 font-medium">Showing {{ $startDisplay }}–{{ $endDisplay }} of {{ $totalRequests }} requests</span>
                        <div class="flex items-center gap-1.5">
                            <button wire:click="setPage({{ $page - 1 }})" class="w-9 h-9 rounded-xl text-xs font-extrabold flex items-center justify-center transition border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer" {{ $page <= 1 ? 'disabled style=opacity:0.3;cursor:default;' : '' }}>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            @for($i = 1; $i <= $totalPages; $i++)
                                <button wire:click="setPage({{ $i }})" class="w-9 h-9 rounded-xl text-xs font-extrabold flex items-center justify-center transition cursor-pointer {{ $i === $page ? 'bg-pp-600 text-white shadow-2xs' : 'border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100' }}">{{ $i }}</button>
                            @endfor
                            <button wire:click="setPage({{ $page + 1 }})" class="w-9 h-9 rounded-xl text-xs font-extrabold flex items-center justify-center transition border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer" {{ $page >= $totalPages ? 'disabled style=opacity:0.3;cursor:default;' : '' }}>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                @endif

            </main>
        </div>
    </div>

    <!-- ============================================================
    POST REQUEST MODAL (MARKETPLACE STYLED)
    ============================================================ -->
    <div
        x-show="showPostModal"
        x-cloak
        @keydown.escape.window="showPostModal = false"
        class="fixed inset-0 z-[100] overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <!-- BACKDROP -->
        <div
            x-show="showPostModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
            @click="showPostModal = false"
            wire:click="closePostModal"
        ></div>

        <!-- SCROLLABLE WRAPPER (items-start ensures top of modal is always visible and never cut off) -->
        <div class="flex min-h-full items-start justify-center p-4 sm:p-6 text-center">
            <!-- MODAL CARD -->
            <div
                x-show="showPostModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative w-full max-w-2xl bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 text-left my-6 sm:my-8 transform transition-all"
            >
                <button
                    type="button"
                    @click="showPostModal = false"
                    wire:click="closePostModal"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-sm font-bold transition cursor-pointer"
                    aria-label="Close modal"
                >
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center text-lg border border-pp-100">
                        <i class="fas fa-pen-square"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 leading-tight">Post a Community Request</h2>
                        <p class="text-xs text-slate-500">Reach verified sellers, technicians, and local experts for parts, repairs, or guidance.</p>
                    </div>
                </div>

                @guest
                    <!-- UNAUTHENTICATED STATE WARNING -->
                    <div class="text-center py-8 space-y-4">
                        <div class="w-16 h-16 rounded-2xl bg-pp-50 text-pp-600 grid place-items-center mx-auto text-2xl font-bold border border-pp-100 shadow-2xs">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Sign in to Post a Request</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                            You need to log in or create an account to post sourcing requests and receive offers from verified sellers and technicians.
                        </p>
                        <div class="pt-2 flex items-center justify-center gap-3">
                            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition shadow-xs">
                                Log In
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-extrabold text-xs transition">
                                Create Account
                            </a>
                        </div>
                    </div>
                @else
                    @if($postSuccessMessage)
                        <div class="p-4 mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span>✅</span> Your request has been posted successfully! The community will start responding shortly.
                            </div>
                            <button type="button" @click="showPostModal = false" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold underline shrink-0 cursor-pointer">
                                Close
                            </button>
                        </div>
                    @endif

                    <form wire:submit="submitRequest" class="space-y-4 pt-2">
                        <!-- TYPE -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Request Type <span class="text-rose-500">*</span></label>
                            <select wire:model="formType" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                                <option value="item">Product / Part (Looking to buy)</option>
                                <option value="service">Repair / Service (Looking for a technician)</option>
                                <option value="advice">Question / Advice (Need technical troubleshooting)</option>
                            </select>
                            @error('formType') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- REQUEST TITLE (FULL WIDTH) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Request Title <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="formTitle" required placeholder="e.g. Looking for HP EliteBook 840 G5 motherboard in Lagos" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            @error('formTitle') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- CATEGORY & BRAND ROW (CATEGORY MANDATORY, BRAND OPTIONAL) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                                <x-searchable-select
                                    wire:model.live="formCategory"
                                    :options="$allCategories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])"
                                    placeholder="Select Category"
                                    search-placeholder="Search categories..."
                                />
                                @error('formCategory') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Brand <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <x-searchable-select
                                    wire:model="formBrand"
                                    :options="$allBrands->map(fn($b) => ['value' => $b->id, 'label' => $b->name])"
                                    placeholder="Select Brand"
                                    search-placeholder="Search brands..."
                                />
                            </div>
                        </div>

                        <!-- MODEL & REFERENCED LOCATION ROW -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Device / Part Model <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <x-searchable-select
                                    wire:model="formModel"
                                    :options="$allModels->map(fn($m) => ['value' => $m->id, 'label' => $m->name])"
                                    placeholder="Select Model"
                                    search-placeholder="Search models..."
                                />
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700">Referenced Location</label>
                                    <button type="button" @click="$dispatch('open-location-modal')" class="text-xs font-bold text-pp-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <i class="fas fa-plus text-[10px]"></i> Add New Location
                                    </button>
                                </div>
                                @if($allLocations->isEmpty())
                                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between gap-3 text-xs text-amber-800">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-amber-500"></i>
                                            <span>No saved location found</span>
                                        </div>
                                        <button type="button" @click="$dispatch('open-location-modal')" class="px-2.5 py-1 bg-amber-600 text-white rounded-lg font-bold text-[11px] hover:bg-amber-700 transition">
                                            + Add Location
                                        </button>
                                    </div>
                                @else
                                    <x-searchable-select
                                        wire:model="formLocation"
                                        :options="$allLocations->map(fn($l) => ['value' => $l->id, 'label' => ($l->label ?? $l->name ?? $l->city) . ' (' . $l->city . ($l->state ? ', ' . (is_object($l->state) ? $l->state->name : $l->state) : '') . ')' . ($l->is_default ? ' ★ Default' : '')])"
                                        placeholder="Select Location"
                                        search-placeholder="Search locations..."
                                    />
                                @endif
                                @if(session()->has('location_success'))
                                    <span class="text-[11px] text-emerald-600 font-bold mt-1 block">✅ {{ session('location_success') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- BUDGET & PREFERRED FULFILMENT ROW (SAME LINE) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Budget (₦) <span class="text-slate-400 font-normal">(Optional)</span></label>
                                <input type="text" wire:model="formBudget" placeholder="e.g. 70,000 – 90,000 or Flexible" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Preferred Fulfilment</label>
                                <select wire:model="formFulfillment" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                                    <option value="flexible">Flexible / Any Method</option>
                                    <option value="buyer_pickup">Buyer pickup ("I will pick up from shop")</option>
                                    <option value="seller_delivery">Seller delivery ("Deliver to my address")</option>
                                    <option value="shop_pickup">Pickup in Shop / On-site inspection</option>
                                </select>
                            </div>
                        </div>

                        <!-- DESCRIPTION -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Description <span class="text-rose-500">*</span></label>
                            <textarea wire:model="formDesc" required placeholder="Describe what you need in detail — include part numbers, symptoms, tested status requirements, etc." rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition"></textarea>
                            @error('formDesc') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- MULTI-MEDIA ATTACHMENTS (IMAGE, VIDEO, PDF/DOCUMENTS) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Attached Media &amp; Documents
                                <span class="text-slate-400 font-normal">(Photos, Boot Test Videos, Diagnostic Specs — max 25MB each)</span>
                            </label>

                            <!-- DROPZONE -->
                            <div class="border-2 border-dashed border-slate-200 hover:border-pp-500 rounded-2xl p-4 text-center transition bg-slate-50/60 hover:bg-white relative group">
                                <input type="file" wire:model="formMedia" multiple accept="image/*,video/*,.pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="mediaUploadInput">
                                <div class="flex flex-col items-center justify-center space-y-1.5 pointer-events-none">
                                    <div class="w-9 h-9 rounded-xl bg-pp-50 text-pp-600 grid place-items-center text-sm border border-pp-100 group-hover:scale-105 transition">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="text-xs font-bold text-slate-700">
                                        <span class="text-pp-600 underline">Click to upload</span> or drag and drop files
                                    </div>
                                    <p class="text-[11px] text-slate-400">Photos (JPG, PNG, WEBP), Videos (MP4, MOV), or Diagnostic PDFs</p>
                                </div>
                            </div>

                            <!-- UPLOADING INDICATOR -->
                            <div wire:loading wire:target="formMedia" class="mt-2 text-xs font-bold text-pp-600 flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i> Uploading files to server... please wait
                            </div>

                            <!-- STAGED MEDIA TILES -->
                            @if(!empty($formMedia) && count($formMedia) > 0)
                                <div class="mt-3 space-y-2">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Staged Attachments ({{ count($formMedia) }}):</span>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach($formMedia as $index => $file)
                                            @php
                                                $ext = strtolower($file->getClientOriginalExtension());
                                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $isVid = in_array($ext, ['mp4', 'mov', 'webm']);
                                            @endphp
                                            <div class="relative rounded-xl border border-slate-200 bg-white p-2 flex items-center gap-2 shadow-2xs">
                                                @if($isImg)
                                                    <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-100">
                                                @elseif($isVid)
                                                    <div class="w-10 h-10 rounded-lg bg-slate-900 text-amber-400 grid place-items-center shrink-0 text-xs">
                                                        <i class="fas fa-video"></i>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 grid place-items-center shrink-0 text-sm">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                @endif

                                                <div class="min-w-0 flex-1">
                                                    <p class="text-[11px] font-bold text-slate-800 truncate" title="{{ $file->getClientOriginalName() }}">
                                                        {{ $file->getClientOriginalName() }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-400">
                                                        {{ number_format($file->getSize() / 1024, 0) }} KB
                                                    </p>
                                                </div>

                                                <button type="button" wire:click="removeMedia({{ $index }})" class="w-5 h-5 rounded-full bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 grid place-items-center text-[10px] transition cursor-pointer" title="Remove file">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @error('formMedia.*') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 mt-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                            <i class="fas fa-paper-plane" wire:loading.remove wire:target="submitRequest"></i>
                            <i class="fas fa-spinner fa-spin" wire:loading wire:target="submitRequest"></i>
                            <span wire:loading.remove wire:target="submitRequest">Publish Request to Community</span>
                            <span wire:loading wire:target="submitRequest">Publishing...</span>
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>

    <!-- ============================================================
    FILTER DRAWER (MOBILE VIEW — SLIDES FROM RIGHT INSTANTLY)
    ============================================================ -->
    <div id="communityFilterOverlay" onclick="closeMobileFilterDrawer()" class="overlay fixed inset-0 bg-slate-950/40 z-[150]"></div>
    <aside id="communityFilterDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-80 max-w-[85vw] bg-white z-[160] p-5 overflow-y-auto custom-scrollbar shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 shrink-0">
            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders-h text-pp-600"></i> Filters &amp; Options
            </h3>
            <button onclick="closeMobileFilterDrawer()" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-lg font-bold transition cursor-pointer" aria-label="Close filters">×</button>
        </div>
        <div class="flex-1">
            <livewire:components.filters.community-filter :isMobile="true" key="mobile-filter" />
        </div>
    </aside>
</main>

@push('scripts')
    <script>
        function toggleMobileFilterDrawer() {
            const drawer = document.getElementById('communityFilterDrawer');
            const overlay = document.getElementById('communityFilterOverlay');
            if (drawer) drawer.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        }

        function closeMobileFilterDrawer() {
            const drawer = document.getElementById('communityFilterDrawer');
            const overlay = document.getElementById('communityFilterOverlay');
            if (drawer) drawer.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
    </script>
@endpush
