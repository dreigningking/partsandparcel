<main class="w-full pb-16">
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
        <div class="bg-gradient-to-r from-pp-50 via-white to-pp-50/50 rounded-2xl border border-pp-100 p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 shadow-2xs">
            <div class="max-w-xl">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Community <span class="text-pp-600">Hub</span>
                </h1>
                <div class="text-xs sm:text-sm font-extrabold text-pp-700 mt-1">Ask. Find. Offer. Solve.</div>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">Find products, parts, technicians, transporters and practical solutions from the Parts &amp; Parcel community.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <button wire:click="openPostModal" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
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
                    <div class="text-lg sm:text-xl font-black text-slate-900">1,248</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Open Requests</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">3,721</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Offers Received</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">9,430</div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Active Members</div>
                </div>
                <div class="text-center px-3 py-1">
                    <div class="text-lg sm:text-xl font-black text-slate-900">2,186</div>
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
                                    <span class="font-extrabold text-slate-900 flex items-center gap-1.5"><i class="fas fa-user-circle text-pp-600"></i> {{ $r['name'] }}</span>
                                    <span class="flex items-center gap-1 text-slate-500"><i class="fas fa-map-pin text-slate-400 text-[10px]"></i> {{ $r['location'] }}</span>
                                    <span class="flex items-center gap-1 text-slate-400"><i class="far fa-clock text-[10px]"></i> {{ $r['time'] }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-pp-50 text-pp-700 font-extrabold text-[10px] uppercase tracking-wider border border-pp-100">{{ $r['type'] }}</span>
                            </div>

                            <!-- TITLE & DESCRIPTION -->
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">{{ $r['title'] }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-2">{{ $r['desc'] }}</p>

                            <!-- CARD FOOTER -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold text-xs border border-pp-100 flex items-center gap-1.5">
                                        <i class="fas fa-handshake"></i> {{ $r['offers'] }} Offers Received
                                    </span>
                                    @if(!empty($r['budget']))
                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-900 font-extrabold text-xs">
                                            {{ $r['budget'] }}
                                        </span>
                                    @endif
                                    @if($r['status'] !== 'open')
                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-xs">
                                            {{ $r['status'] === 'offers' ? 'Offers Received' : 'Recently Fulfilled' }}
                                        </span>
                                    @endif
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
    @if($showPostModal)
        <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 sm:p-8 shadow-xl relative border border-slate-200 my-8">
                <button wire:click="closePostModal" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-sm font-bold transition cursor-pointer" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
                <h2 class="text-xl font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                    <i class="fas fa-pen-square text-pp-600"></i> Post a Request
                </h2>
                <p class="text-xs text-slate-500 mb-6">Tell the community what you need — products, parts, repairs, or advice.</p>

                @if($postSuccessMessage)
                    <div class="p-4 mb-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                        <span>✅</span> Your request has been posted successfully! The community will start responding shortly.
                    </div>
                @endif

                <form wire:submit="submitRequest" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">What do you need? <span class="text-rose-500">*</span></label>
                        <select wire:model="formType" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            <option value="">Select request type</option>
                            <option value="Product / Part">Product / Part</option>
                            <option value="Repair / Service">Repair / Service</option>
                            <option value="Question / Advice">Question / Advice</option>
                        </select>
                        @error('formType') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                        <select wire:model="formCategory" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            <option value="">Select category</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Vehicles">Vehicles</option>
                            <option value="Appliances">Appliances</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Construction">Construction</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Agricultural">Agricultural</option>
                        </select>
                        @error('formCategory') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Request title <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="formTitle" required placeholder="e.g. Looking for HP EliteBook motherboard" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                        @error('formTitle') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description <span class="text-rose-500">*</span></label>
                        <textarea wire:model="formDesc" required placeholder="Describe what you need in detail…" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition"></textarea>
                        @error('formDesc') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Location <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="formLocation" required placeholder="e.g. Computer Village, Ikeja" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            @error('formLocation') <span class="text-rose-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Budget (₦)</label>
                            <input type="text" wire:model="formBudget" placeholder="e.g. 70,000 – 90,000" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Preferred response</label>
                        <select wire:model="formResponse" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">
                            <option value="Any">Any response</option>
                            <option value="Commercial offers">Commercial offers</option>
                            <option value="Community advice">Community advice</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-3 mt-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </form>
            </div>
        </div>
    @endif

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
