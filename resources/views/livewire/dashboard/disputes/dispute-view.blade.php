<div class="space-y-6 w-full max-w-[1600px] mx-auto pb-16">

    <!-- BREADCRUMBS & TOP HEADER -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex-wrap">
                    <a href="{{ route('disputes') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition flex items-center gap-1">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        <span>Resolution Center</span>
                    </a>
                    <span>/</span>
                    <span class="font-mono text-slate-600 dark:text-slate-400 font-bold">Case Ref: #{{ $caseId }}</span>
                    <span>•</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase">
                        {{ $originCategory }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200/50 dark:border-purple-800/50 text-[10px] font-extrabold">
                        {{ $typeLabel }}
                    </span>
                </div>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Dispute Case #{{ $caseId }}
                    </h1>

                    @if($status === 'resolved')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Arbitration Finalized &amp; Settled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Under Platform Arbitration
                        </span>
                    @endif

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                        <i class="fas fa-lock text-[10px] text-slate-400 mr-1.5"></i>
                        <span>Frozen Escrow: {{ $currencySymbol }}{{ number_format($orderTotal, 2) }}</span>
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-2 flex-wrap">
                    <span>Invoice: <a href="{{ route('invoices.view', $invoiceId) }}?tab=dispute" class="font-mono text-pp-600 dark:text-pp-400 font-bold hover:underline">#{{ $invoiceNumber }}</a></span>
                    <span>•</span>
                    <span>Disputed Component: <strong class="text-slate-800 dark:text-slate-200 font-medium">{{ $disputedItemTitle }}</strong></span>
                </div>
            </div>

            <!-- ACTION CONTROLS & PERSPECTIVE SWITCHER -->
            <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0 flex-wrap">
                <!-- PERSPECTIVE SWITCHER (DEMO CONTROL) -->
                <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase px-2">Viewing as:</span>
                    <button
                        type="button"
                        wire:click="setPerspective('buyer')"
                        class="px-2.5 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $viewAs === 'buyer' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        Buyer
                    </button>
                    <button
                        type="button"
                        wire:click="setPerspective('seller')"
                        class="px-2.5 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $viewAs === 'seller' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        Seller
                    </button>
                </div>

                <!-- TOGGLE STATUS (DEMO CONTROL) -->
                <button
                    type="button"
                    wire:click="toggleResolutionStatus"
                    class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition cursor-pointer inline-flex items-center gap-1.5"
                    title="Click to toggle between Open Escrow and Resolved preview state"
                >
                    <i class="fas fa-arrows-rotate text-slate-400 text-[10px]"></i>
                    <span>{{ $status === 'resolved' ? 'Show Open' : 'Show Resolved' }}</span>
                </button>

                <a 
                    href="{{ route('invoices.view', $invoiceId) }}" 
                    class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-xs font-bold text-white dark:text-slate-900 transition shadow-2xs inline-flex items-center gap-1.5"
                >
                    <i class="fas fa-file-invoice text-xs"></i>
                    <span>View Invoice</span>
                </a>
            </div>
        </div>
    </div>

    <!-- FLASH NOTIFICATION -->
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- FIRST SECTION: PARTY RECORDS & FINANCIAL AUDIT (SIDE-BY-SIDE GRID) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- 1. COMPLAINANT (BUYER) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 space-y-3.5 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Complainant (Buyer)</span>
                </span>
                @if($viewAs === 'buyer')
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                        Your Account
                    </span>
                @else
                    <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                        <i class="fas fa-shield-check"></i>
                        <span>Verified</span>
                    </span>
                @endif
            </div>

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900/60 grid place-items-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr($buyerName, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <strong class="text-sm font-bold text-slate-900 dark:text-white block truncate">
                        {{ $buyerName }}
                    </strong>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block truncate">
                        {{ $buyerEmail }}
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-mono block mt-0.5">
                        {{ $buyerPhone }}
                    </span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs text-slate-500 dark:text-slate-400">
                <div>Orders: <strong class="text-slate-900 dark:text-white font-semibold">{{ $buyerCompletedOrders }} Completed</strong></div>
                <div>Dispute Rate: <strong class="text-slate-900 dark:text-white font-semibold">{{ $buyerDisputeRate }}</strong></div>
                <div class="col-span-2">Hub: <strong class="text-slate-800 dark:text-slate-200 font-medium">{{ $buyerHub }}</strong></div>
            </div>
        </div>

        <!-- 2. RESPONDENT (SELLER) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 space-y-3.5 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Respondent (Seller)</span>
                </span>
                @if($viewAs === 'seller')
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                        Your Account
                    </span>
                @else
                    <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                        <i class="fas fa-certificate"></i>
                        <span>Top Merchant</span>
                    </span>
                @endif
            </div>

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 grid place-items-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr($sellerName, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <strong class="text-sm font-bold text-slate-900 dark:text-white block truncate">
                        {{ $sellerName }}
                    </strong>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block truncate">
                        {{ $sellerEmail }}
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-mono block mt-0.5">
                        {{ $sellerPhone }}
                    </span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs text-slate-500 dark:text-slate-400">
                <div>Sales: <strong class="text-slate-900 dark:text-white font-semibold">{{ $sellerFulfilledSales }} Fulfilled</strong></div>
                <div>Dispute Rate: <strong class="text-slate-900 dark:text-white font-semibold">{{ $sellerDisputeRate }}</strong></div>
                <div class="col-span-2">Hub: <strong class="text-slate-800 dark:text-slate-200 font-medium">{{ $sellerHub }}</strong></div>
            </div>
        </div>

        <!-- 3. ESCROW CAPITAL SNAPSHOT -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    <i class="fas fa-shield-halved text-slate-400"></i>
                    <span>Escrow Vault Audit</span>
                </span>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    #{{ $settlementRef }} Locked
                </span>
            </div>

            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                    <span>Component Subtotal:</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">{{ $currencySymbol }}{{ number_format($orderTotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                    <span>Platform Escrow Fee:</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">{{ $currencySymbol }}{{ number_format($escrowFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                    <span>Logistics Waybill:</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">{{ $currencySymbol }}{{ number_format($logisticsFee, 2) }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-100 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
                    <span>Total Frozen Capital:</span>
                    <span class="font-mono text-sm">{{ $currencySymbol }}{{ number_format($totalFrozenCapital, 2) }}</span>
                </div>
            </div>

            <div class="text-[11px] text-slate-400 pt-1 flex items-center gap-1.5">
                <i class="fas fa-lock text-[10px]"></i>
                <span>Protected by Parts &amp; Parcel Escrow Gateway</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- ARBITRATION VERDICT / STATUS BANNER (READ-ONLY FOR PARTIES) -->
    <!-- ========================================================================= -->
    @if($status === 'resolved')
        <!-- OFFICIAL BINDING VERDICT (RESOLVED STATE) -->
        <div class="rounded-2xl border border-emerald-200/80 dark:border-emerald-800/80 bg-white dark:bg-slate-900 p-6 space-y-4 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-100 dark:border-emerald-950 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 grid place-items-center shrink-0">
                        <i class="fas fa-gavel text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Official Mediator Binding Verdict &amp; Financial Settlement
                        </h3>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            Arbitrated by {{ $resolverName }} · Finalized on {{ $resolvedAt }}
                        </span>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 shrink-0">
                    <i class="fas fa-check-double text-[10px]"></i>
                    <span>Ruling Executed &amp; Finalized</span>
                </span>
            </div>

            <!-- VERDICT SUMMARY BOX -->
            <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 space-y-2 text-xs">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-[11px] font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                        Determination Outcome:
                    </span>
                    <strong class="text-sm font-bold text-emerald-950 dark:text-emerald-200">
                        @if($decision === 'buyer_favor')
                            Ruled in Buyer's Favor — Full Refund of {{ $currencySymbol }}{{ number_format($refundAmount, 2) }} Authorized
                        @elseif($decision === 'seller_favor')
                            Ruled in Seller's Favor — Escrow Released to Seller
                        @else
                            Split Resolution Settlement Executed
                        @endif
                    </strong>
                </div>

                <div class="pt-2 border-t border-emerald-200/50 dark:border-emerald-900/40">
                    <span class="font-bold text-slate-800 dark:text-slate-200 block mb-1">Arbitrator Rationale &amp; Findings:</span>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                        "{{ $resolutionNotes }}"
                    </p>
                </div>
            </div>

            <!-- BINDING TERMS FOOTNOTE -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400 pt-1">
                <span class="flex items-center gap-1.5">
                    <i class="fas fa-circle-info text-slate-400"></i>
                    <span>This decision is legally binding and irrevocable under Parts &amp; Parcel Platform Mediation Policies.</span>
                </span>
                <span class="font-medium text-slate-600 dark:text-slate-300">
                    Settlement Reference: #{{ $settlementRef }}-DISBURSED
                </span>
            </div>
        </div>
    @else
        <!-- ACTIVE MEDIATION IN PROGRESS (OPEN STATE) -->
        <div class="rounded-2xl border border-amber-200/80 dark:border-amber-900/60 bg-amber-50/30 dark:bg-amber-950/20 p-5 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200/50 dark:border-amber-900/40 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-950 dark:text-amber-200">
                        Arbitration Review in Progress — Escrow Secured
                    </h3>
                </div>
                <span class="text-xs font-mono text-amber-800 dark:text-amber-300">
                    Vault #{{ $settlementRef }} Locked
                </span>
            </div>

            <p class="text-xs text-amber-900/90 dark:text-amber-200/90 leading-relaxed">
                An independent Parts &amp; Parcel dispute arbitrator is currently examining all evidence exhibits, diagnostics, courier reports, and transaction records. Escrow funds (<strong>{{ $currencySymbol }}{{ number_format($orderTotal, 2) }}</strong>) remain 100% frozen in the platform vault until a determination is rendered. Neither party can withdraw or forfeit funds until a final binding ruling is issued.
            </p>

            <div class="flex items-center gap-2 text-[11px] text-amber-800/80 dark:text-amber-400">
                <i class="fas fa-bell text-[10px]"></i>
                <span>Both parties will be automatically notified once the arbitrator issues a final decision. Please ensure any requested evidence in the Evidence Locker below is submitted promptly.</span>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- FULL-WIDTH TABBED INVESTIGATION WORKSPACE (4 TABS — NO CHAT) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden w-full">
        
        <!-- FULL-WIDTH MINIMALIST TAB BAR (EVIDENCE IS THE FIRST TAB!) -->
        <div class="border-b border-slate-200/80 dark:border-slate-800 px-6 pt-3">
            <div class="flex items-center gap-8 overflow-x-auto">
                
                <!-- TAB 1: EVIDENCE (FIRST TAB) -->
                <button
                    type="button"
                    wire:click="setTab('evidence')"
                    class="pb-3 text-xs font-semibold transition border-b-2 cursor-pointer flex items-center gap-2 {{ $activeTab === 'evidence' ? 'border-slate-900 dark:border-white text-slate-900 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-camera text-[11px]"></i>
                    <span>Evidence Locker &amp; Requests ({{ count($evidenceRequests) }})</span>
                </button>

                <!-- TAB 2: CLAIMS -->
                <button
                    type="button"
                    wire:click="setTab('claims')"
                    class="pb-3 text-xs font-semibold transition border-b-2 cursor-pointer flex items-center gap-2 {{ $activeTab === 'claims' ? 'border-slate-900 dark:border-white text-slate-900 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-scale-balanced text-[11px]"></i>
                    <span>Claims &amp; Disputed Items</span>
                </button>

                <!-- TAB 3: SHIPPING & LOGISTICS -->
                <button
                    type="button"
                    wire:click="setTab('logistics')"
                    class="pb-3 text-xs font-semibold transition border-b-2 cursor-pointer flex items-center gap-2 {{ $activeTab === 'logistics' ? 'border-slate-900 dark:border-white text-slate-900 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-truck text-[11px]"></i>
                    <span>Shipping &amp; Return Waybills</span>
                </button>

                <!-- TAB 4: AUDIT LOG & TIMELINE -->
                <button
                    type="button"
                    wire:click="setTab('timeline')"
                    class="pb-3 text-xs font-semibold transition border-b-2 cursor-pointer flex items-center gap-2 {{ $activeTab === 'timeline' ? 'border-slate-900 dark:border-white text-slate-900 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-timeline text-[11px]"></i>
                    <span>Audit Log &amp; Lifecycle ({{ count($timelineEvents) }})</span>
                </button>

            </div>
        </div>

        <div class="p-6">

            <!-- ========================================================================= -->
            <!-- TAB 1: EVIDENCE LOCKER & FORMAL REQUESTS (FIRST TAB) -->
            <!-- ========================================================================= -->
            @if($activeTab === 'evidence')
                <div class="space-y-6">

                    <!-- EVIDENCE STATUS HEADER -->
                    <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-folder-open text-slate-500"></i>
                                <span>Evidence Verification &amp; Party Requisition Locker</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Official exhibits submitted by buyer and seller, and active documentation requisitions issued by the Platform Arbitrator.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 text-xs">
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono">
                                {{ count($evidenceRequests) }} Requisition Files
                            </span>
                        </div>
                    </div>

                    <!-- EVIDENCE REQUESTS & SUBMISSIONS FEED -->
                    <div class="space-y-5">
                        @forelse($evidenceRequests as $req)
                            <div class="rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/60 p-5 space-y-4 shadow-2xs">
                                
                                <!-- REQUEST METADATA HEADER -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <span class="font-mono text-xs font-bold text-slate-400">#REQ-{{ $req['id'] }}</span>
                                        
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $req['target'] === 'buyer' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                            Requisition For: {{ $req['target_name'] }}
                                        </span>

                                        <strong class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $req['title'] }}
                                        </strong>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs">
                                        @if($req['status'] === 'submitted')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                                <i class="fas fa-check text-[10px]"></i>
                                                <span>Submitted {{ $req['submitted_at'] ? 'on ' . $req['submitted_at'] : '' }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>Awaiting Response</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- INSTRUCTIONS GIVEN BY ADMIN -->
                                <div class="text-xs text-slate-600 dark:text-slate-300">
                                    <span class="font-bold text-slate-700 dark:text-slate-200">Arbitrator Instructions:</span>
                                    <span>{{ $req['instructions'] }}</span>
                                    @if(!empty($req['requested_at']))
                                        <span class="text-[11px] text-slate-400 font-mono ml-2">({{ $req['requested_at'] }})</span>
                                    @endif
                                </div>

                                <!-- IF SUBMITTED: SHOW SUBMITTER NOTES & ATTACHED MEDIA -->
                                @if($req['status'] === 'submitted')
                                    <div class="space-y-3 pt-1">
                                        @if(!empty($req['party_notes']))
                                            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border-l-2 border-slate-400 text-xs text-slate-700 dark:text-slate-300">
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block mb-0.5">Submitter Statement:</span>
                                                <span>{{ $req['party_notes'] }}</span>
                                            </div>
                                        @endif

                                        <!-- MEDIA ATTACHMENTS GRID -->
                                        @if(!empty($req['files']))
                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5 pt-1">
                                                @foreach($req['files'] as $f)
                                                    <div class="rounded-xl border border-slate-200/80 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-800/40">
                                                        <div 
                                                            wire:click="openImagePreview('{{ $f['url'] ?? $f['path'] }}', '{{ $f['label'] ?? $f['name'] }} (#REQ-{{ $req['id'] }})')"
                                                            class="h-32 overflow-hidden relative group cursor-pointer"
                                                        >
                                                            <img 
                                                                src="{{ $f['thumb'] ?? ($f['url'] ?? $f['path']) }}" 
                                                                alt="{{ $f['label'] ?? $f['name'] }}" 
                                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                                            >
                                                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                                                <i class="fas fa-magnifying-glass-plus text-xs"></i>
                                                                <span>Zoom</span>
                                                            </div>
                                                        </div>
                                                        <div class="p-2 text-xs">
                                                            <div class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $f['label'] ?? $f['name'] }}</div>
                                                            <div class="text-[10px] text-slate-400 font-mono truncate">{{ $f['name'] }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                                        <span class="text-[11px] flex items-center gap-1.5">
                                            <i class="fas fa-shield-check text-emerald-500"></i>
                                            <span>Exhibit cryptographically verified and submitted to platform arbitrator file.</span>
                                        </span>
                                    </div>
                                @else
                                    <!-- PENDING REQUISITION CALLOUT -->
                                    <div class="p-4 rounded-xl {{ $viewAs === $req['target'] ? 'bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800' : 'bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                        <div>
                                            @if($viewAs === $req['target'])
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                                    <strong class="text-amber-950 dark:text-amber-200 font-bold">
                                                        Action Required from You: Admin Requisition
                                                    </strong>
                                                </div>
                                                <p class="text-amber-900/80 dark:text-amber-300/80 text-[11px]">
                                                    Target Deadline: {{ $req['deadline'] ?? 'Within 24 Hours' }}. Please submit the requested photos or documentation for arbitrator review.
                                                </p>
                                            @else
                                                <strong class="text-slate-800 dark:text-slate-200 font-bold block">
                                                    Awaiting Submission from {{ $req['target_name'] }}
                                                </strong>
                                                <span class="text-slate-500 dark:text-slate-400 text-[11px]">
                                                    Target Deadline: {{ $req['deadline'] ?? 'Within 24 Hours' }}. The other party has been alerted via email.
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($viewAs === $req['target'])
                                                <!-- INTERACTIVE UPLOAD BUTTON FOR TARGET PARTY -->
                                                <button
                                                    type="button"
                                                    wire:click="openUploadModal({{ $req['id'] }})"
                                                    class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-bold text-xs transition cursor-pointer shadow-2xs inline-flex items-center gap-1.5"
                                                >
                                                    <i class="fas fa-upload text-xs"></i>
                                                    <span>Upload &amp; Submit Evidence</span>
                                                </button>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 text-xs italic">
                                                    Pending other party response
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs">
                                No evidence exhibits or requisitions recorded for this case.
                            </div>
                        @endforelse
                    </div>

                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 2: CLAIMS & DISPUTED ITEMS -->
            <!-- ========================================================================= -->
            @if($activeTab === 'claims')
                <div class="space-y-6">
                    
                    <!-- CLAIMS COMPARISON -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- BUYER CLAIM -->
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-900 dark:text-white">
                                <span class="flex items-center gap-1.5 text-rose-700 dark:text-rose-400">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span>Complainant Statement ({{ $buyerName }})</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                                "{{ $complainantStatement }}"
                            </p>
                        </div>

                        <!-- SELLER REBUTTAL -->
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-900 dark:text-white">
                                <span class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>Respondent Defense ({{ $sellerName }})</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                                "{{ $respondentDefense }}"
                            </p>
                        </div>

                    </div>

                    <!-- DISPUTED ITEMS BREAKDOWN -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Invoice Disputed Item Breakdown
                        </h4>

                        <div class="p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <strong class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $disputedItemTitle }}
                                    </strong>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200/70 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        Qty: 1
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                    {{ $disputedItemPartNumber }}
                                </p>
                                <div class="text-xs text-rose-600 dark:text-rose-400 font-semibold pt-1 flex items-center gap-1">
                                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                                    <span>Defect Claimed: {{ $disputedItemDefect }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <strong class="text-base font-bold font-mono text-slate-900 dark:text-white block">
                                    {{ $currencySymbol }}{{ number_format($disputedItemAmount, 2) }}
                                </strong>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    Full Invoiced Value
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 3: SHIPPING & LOGISTICS WAYBILLS -->
            <!-- ========================================================================= -->
            @if($activeTab === 'logistics')
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- OUTBOUND WAYBILL -->
                        <div class="p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Outbound Courier Shipment
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                    {{ $outboundStatus }}
                                </span>
                            </div>
                            <div class="space-y-1.5 text-xs">
                                <div>Carrier: <strong class="text-slate-800 dark:text-slate-200">{{ $outboundCarrier }}</strong></div>
                                <div>Tracking Code: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $outboundWaybill }}</strong></div>
                                <div>Dispatched: <strong class="text-slate-800 dark:text-slate-200">{{ $outboundDispatchedAt }}</strong></div>
                                <div>Route: <strong class="text-slate-800 dark:text-slate-200">{{ $outboundRoute }}</strong></div>
                            </div>
                        </div>

                        <!-- RETURN WAYBILL -->
                        <div class="p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Reverse Return Transit
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                    {{ $returnStatus }}
                                </span>
                            </div>
                            <div class="space-y-1.5 text-xs">
                                <div>Return Waybill: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $returnWaybill }}</strong></div>
                                <div>Carrier: <strong class="text-slate-800 dark:text-slate-200">{{ $returnCarrier }}</strong></div>
                                <div>Receiving Address: <strong class="text-slate-800 dark:text-slate-200">{{ $returnDestination }}</strong></div>
                                <div class="text-slate-500 dark:text-slate-400 text-[11px] pt-1">
                                    Package held in escrow depot until arbitration ruling determines return requirement.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            <!-- ========================================================================= -->
            <!-- TAB 4: AUDIT LOG & LIFECYCLE (DYNAMIC FROM POST-SALE TABLES) -->
            <!-- ========================================================================= -->
            @if($activeTab === 'timeline')
                <div class="space-y-4">
                    <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-px before:bg-slate-200 dark:before:bg-slate-800 text-xs">
                        
                        @forelse($timelineEvents as $event)
                            <div>
                                <span class="absolute -left-0.5 top-1 w-2.5 h-2.5 rounded-full ring-4 ring-white dark:ring-slate-900 {{ match($event['color'] ?? 'slate') { 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500', 'blue' => 'bg-blue-500', 'amber' => 'bg-amber-500', 'purple' => 'bg-purple-500', default => 'bg-slate-400' } }}"></span>
                                <div class="font-semibold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    @if(!empty($event['icon']))
                                        <i class="fas {{ $event['icon'] }} text-[10px] text-slate-400"></i>
                                    @endif
                                    <span>{{ $event['title'] }}</span>
                                </div>
                                <div class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5 leading-relaxed">{{ $event['description'] }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $event['time'] }}</div>
                            </div>
                        @empty
                            <div class="text-slate-400 text-xs py-4">No lifecycle milestones recorded yet.</div>
                        @endforelse

                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- UPLOAD EVIDENCE MODAL (INTERACTIVE FOR PARTIES) -->
    <!-- ========================================================================= -->
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 max-w-lg w-full space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-upload text-slate-500"></i>
                        <span>Upload Requested Evidence (#REQ-{{ $activeRequestId }})</span>
                    </h3>
                    <button 
                        type="button" 
                        wire:click="closeUploadModal" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm cursor-pointer p-1"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                @if($this->activeRequest)
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/70 text-xs space-y-1">
                        <strong class="text-slate-900 dark:text-white block font-bold">
                            {{ $this->activeRequest['title'] }}
                        </strong>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                            {{ $this->activeRequest['instructions'] }}
                        </p>
                    </div>
                @endif

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Your Explanation / Inspection Notes *
                        </label>
                        <textarea 
                            wire:model="uploadNotes" 
                            rows="3" 
                            placeholder="Describe the attached photos or documents clearly for the arbitrator..." 
                            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                        ></textarea>
                    </div>

                    <!-- MOCK DROPZONE -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Attach Media / Photos / Diagnostics
                        </label>
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-5 text-center bg-slate-50/50 dark:bg-slate-800/30">
                            <i class="fas fa-cloud-arrow-up text-slate-400 text-xl mb-1.5 block"></i>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">
                                Drag &amp; drop evidence files or browse
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                Supports JPG, PNG, PDF, HEIC (Max 10MB per file)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        wire:click="closeUploadModal" 
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        wire:click="submitEvidence" 
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition cursor-pointer shadow-2xs inline-flex items-center gap-1.5"
                    >
                        <i class="fas fa-check text-xs"></i>
                        <span>Submit to Arbitrator</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- LIGHTBOX IMAGE PREVIEW MODAL -->
    <!-- ========================================================================= -->
    @if($previewImageModalUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 max-w-3xl w-full space-y-3 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ $previewImageModalTitle ?? 'Evidence Document' }}
                    </span>
                    <button 
                        type="button" 
                        wire:click="closeImagePreview" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm cursor-pointer p-1"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-hidden rounded-xl bg-slate-950 flex items-center justify-center">
                    <img 
                        src="{{ $previewImageModalUrl }}" 
                        alt="Evidence Preview" 
                        class="max-h-[70vh] w-auto object-contain"
                    >
                </div>
            </div>
        </div>
    @endif

</div>