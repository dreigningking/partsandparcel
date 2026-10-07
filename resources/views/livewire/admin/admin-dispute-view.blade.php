<div class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- BREADCRUMBS & TOP HEADER -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex-wrap">
                    <a href="{{ route('admin.disputes') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition flex items-center gap-1">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        <span>Disputes Desk</span>
                    </a>
                    <span>/</span>
                    <span class="font-mono text-slate-600 dark:text-slate-400 font-bold">Case Ref: #{{ $caseId }}</span>
                    <span>•</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase">
                        {{ $originCategory }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200/50 dark:border-rose-900/50 text-[10px] font-extrabold">
                        {{ $typeLabel }}
                    </span>
                </div>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Dispute Case #{{ $caseId }}
                    </h1>

                    @if($isResolved)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Ruling Executed &amp; Closed
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Under Admin Arbitration
                        </span>
                    @endif

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                        <i class="fas fa-lock text-[10px] text-slate-400 mr-1.5"></i>
                        <span>Frozen Escrow: {{ $currencySymbol }}{{ number_format($orderTotal, 2) }}</span>
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-2 flex-wrap">
                    <span>Invoice: <strong class="font-mono text-slate-700 dark:text-slate-300">#{{ $invoiceNumber }}</strong></span>
                    <span>•</span>
                    <span>Disputed Component: <strong class="text-slate-800 dark:text-slate-200 font-medium">{{ $disputedItemTitle }}</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 flex-wrap">
                <a 
                    href="{{ route('admin.invoices') }}" 
                    class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition inline-flex items-center gap-1.5"
                >
                    <i class="fas fa-file-invoice text-slate-400"></i>
                    <span>Invoices Desk</span>
                </a>
                <a 
                    href="#arbitration-bench" 
                    class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-xs font-bold text-white dark:text-slate-900 transition shadow-2xs inline-flex items-center gap-1.5"
                >
                    <i class="fas fa-gavel text-xs"></i>
                    <span>{{ $isResolved ? 'View Ruling' : 'Arbitrate Ruling' }}</span>
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
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                    <i class="fas fa-shield-check"></i>
                    <span>Verified</span>
                </span>
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
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                    <i class="fas fa-certificate"></i>
                    <span>Top Merchant</span>
                </span>
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
    <!-- FULL-WIDTH TABBED INVESTIGATION WORKSPACE (4 TABS — NO CHAT) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden w-full">
        
        <!-- FULL-WIDTH MINIMALIST TAB BAR (EVIDENCE IS FIRST TAB) -->
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

                <!-- TAB 3: SHIPPING & RETURN WAYBILLS -->
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

                    <!-- EVIDENCE CONTROL HEADER -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-folder-open text-slate-500"></i>
                                <span>Evidence Verification &amp; Party Request Desk</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Review submitted exhibits or officially requisition additional photos, diagnostics, or receipts from either party.
                            </p>
                        </div>

                        <!-- ACTION BUTTONS: REQUEST FROM EITHER PARTY -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button
                                type="button"
                                wire:click="openRequestModal('buyer')"
                                class="px-3.5 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition inline-flex items-center gap-1.5 cursor-pointer"
                            >
                                <i class="fas fa-plus text-rose-500 text-[10px]"></i>
                                <span>Request from Buyer</span>
                            </button>

                            <button
                                type="button"
                                wire:click="openRequestModal('seller')"
                                class="px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-xs font-semibold text-white dark:text-slate-900 transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Request from Seller</span>
                            </button>
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
                                            To: {{ $req['target_name'] }}
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
                                    <span class="font-bold text-slate-700 dark:text-slate-200">Requisition Instructions:</span>
                                    <span>{{ $req['instructions'] }}</span>
                                    @if(!empty($req['requested_at']))
                                        <span class="text-[11px] text-slate-400 font-mono ml-2">({{ $req['requested_at'] }})</span>
                                    @endif
                                </div>

                                <!-- IF SUBMITTED: SHOW PARTY NOTES & ATTACHED MEDIA -->
                                @if($req['status'] === 'submitted')
                                    <div class="space-y-3 pt-1">
                                        @if(!empty($req['party_notes']))
                                            <div class="p-3.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border-l-2 border-slate-400 text-xs text-slate-700 dark:text-slate-300">
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block mb-0.5">Submitter Note:</span>
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

                                    <!-- ACTION AFTER SUBMISSION -->
                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 text-[11px]">Evidence is verified on platform audit trail.</span>
                                        <button
                                            type="button"
                                            wire:click="openRequestModal('{{ $req['target'] }}')"
                                            class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition inline-flex items-center gap-1 cursor-pointer"
                                        >
                                            <i class="fas fa-reply text-[10px]"></i>
                                            <span>Request Follow-up / Another Item from {{ $req['target'] === 'buyer' ? 'Buyer' : 'Seller' }}</span>
                                        </button>
                                    </div>
                                @else
                                    <!-- PENDING SUBMISSION CARD -->
                                    <div class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                        <div>
                                            <span class="font-bold text-amber-900 dark:text-amber-300 block">
                                                Awaiting Submission from {{ $req['target_name'] }}
                                            </span>
                                            <span class="text-amber-800/80 dark:text-amber-400/80 text-[11px]">
                                                Target Deadline: {{ $req['deadline'] ?? 'Within 24 Hours' }}. Party has been alerted via email.
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                wire:click="simulatePartySubmission({{ $req['id'] }})"
                                                class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold text-[11px] transition cursor-pointer"
                                            >
                                                <i class="fas fa-upload mr-1"></i>
                                                <span>Simulate Party Submission</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs">
                                No evidence requests issued yet. Use the buttons above to requisition evidence.
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

                    <!-- DISPUTED ITEMS TABLE -->
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
                                    <span>Defect claimed: {{ $disputedItemDefect }}</span>
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
    <!-- DEDICATED FULL-WIDTH ARBITRATION BENCH (ANCHORED AT BOTTOM) -->
    <!-- ========================================================================= -->
    <div id="arbitration-bench" class="bg-white dark:bg-slate-900 rounded-2xl border-2 border-slate-900 dark:border-slate-700 p-6 sm:p-8 space-y-6 shadow-sm">
        
        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 grid place-items-center text-sm">
                        <i class="fas fa-gavel"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Official Arbitration Determination Bench
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Binding platform determination for Case #{{ $caseId }}. Resolves dispute and releases frozen escrow.
                        </p>
                    </div>
                </div>

                <div>
                    @if($isResolved)
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 text-xs font-semibold uppercase">
                            Ruling Registered &amp; Settled
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold uppercase">
                            Final Judgment Pending
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($isResolved)
            <!-- SETTLED DISPLAY -->
            <div class="p-5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 space-y-3 text-xs">
                <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-300 text-sm">
                    <span>Final Determination: {{ ucfirst(str_replace('_', ' ', $decision)) }}</span>
                    <span class="text-xs font-mono uppercase">Case Formally Closed</span>
                </div>
                <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-normal text-xs">
                    {{ $resolutionNotes }}
                </p>
                <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800/60 flex items-center justify-between text-xs font-mono">
                    <span>Disbursement Executed:</span>
                    <strong>{{ $currencySymbol }}{{ number_format($refundAmount, 2) }} {{ $decision === 'seller_favor' ? 'Released to Seller' : 'Refunded to Buyer' }}</strong>
                </div>
            </div>
        @else
            <!-- RULING INPUT FORM -->
            <div class="space-y-6 text-xs">

                <!-- VERDICT CHOICES -->
                <div class="space-y-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300 block text-xs uppercase tracking-wider">
                        Select Binding Verdict *
                    </label>
                    
                    <div class="grid sm:grid-cols-3 gap-3.5">
                        <!-- Option 1: Rule for Buyer -->
                        <button
                            type="button"
                            wire:click="setDecision('buyer_favor')"
                            class="p-4 rounded-xl border text-left cursor-pointer transition flex items-start justify-between gap-3 {{ $decision === 'buyer_favor' ? 'border-slate-900 bg-slate-50 dark:border-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300' }}"
                        >
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">Rule for Buyer</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Full/partial refund from escrow. Require return if item undamaged.</div>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 mt-0.5 {{ $decision === 'buyer_favor' ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900' : 'border-slate-300' }}">
                                @if($decision === 'buyer_favor')
                                    <i class="fas fa-check text-[9px]"></i>
                                @endif
                            </span>
                        </button>

                        <!-- Option 2: Rule for Seller -->
                        <button
                            type="button"
                            wire:click="setDecision('seller_favor')"
                            class="p-4 rounded-xl border text-left cursor-pointer transition flex items-start justify-between gap-3 {{ $decision === 'seller_favor' ? 'border-slate-900 bg-slate-50 dark:border-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300' }}"
                        >
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">Rule for Seller</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Contest justified. Release escrow settlement to merchant.</div>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 mt-0.5 {{ $decision === 'seller_favor' ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900' : 'border-slate-300' }}">
                                @if($decision === 'seller_favor')
                                    <i class="fas fa-check text-[9px]"></i>
                                @endif
                            </span>
                        </button>

                        <!-- Option 3: Split Settlement -->
                        <button
                            type="button"
                            wire:click="setDecision('split')"
                            class="p-4 rounded-xl border text-left cursor-pointer transition flex items-start justify-between gap-3 {{ $decision === 'split' ? 'border-slate-900 bg-slate-50 dark:border-white dark:bg-slate-800' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300' }}"
                        >
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">Split Settlement</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Split responsibility between parties or partial restock deduction.</div>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 mt-0.5 {{ $decision === 'split' ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900' : 'border-slate-300' }}">
                                @if($decision === 'split')
                                    <i class="fas fa-check text-[9px]"></i>
                                @endif
                            </span>
                        </button>
                    </div>
                </div>

                <!-- FINANCIAL SETTLEMENT BREAKDOWN & RETURN REQUIREMENT -->
                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">
                            Refund Amount to Buyer ({{ $currencySymbol }})
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="refundAmount"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white font-mono"
                        />
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Escrow holds {{ $currencySymbol }}{{ number_format($orderTotal, 2) }}. Seller receives remainder: {{ $currencySymbol }}{{ number_format(max(0, $orderTotal - $refundAmount), 2) }}
                        </span>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">
                            Physical Return Enforcement
                        </label>
                        <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex items-center gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="requireReturn"
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-900 w-4 h-4"
                            />
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">
                                Require Buyer to Return Physical Unit to Seller Depot
                            </span>
                        </label>
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Disbursement conditional upon return delivery confirmation.
                        </span>
                    </div>
                </div>

                <!-- RESOLUTION NOTES (PUBLIC & INTERNAL) -->
                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">
                            Official Judgment Statement &amp; Rationale *
                        </label>
                        <textarea
                            wire:model="resolutionNotes"
                            rows="4"
                            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white leading-relaxed"
                            placeholder="Explain the factual or technical findings supporting this ruling. Visible to both parties."
                        ></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">
                            Internal Staff Notes (Platform Eyes Only)
                        </label>
                        <textarea
                            wire:model="internalNotes"
                            rows="4"
                            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white leading-relaxed"
                            placeholder="Internal record for operations &amp; merchant review. Never shared with buyer or seller."
                        ></textarea>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
                    <div class="text-[11px] text-slate-500">
                        <i class="fas fa-triangle-exclamation text-amber-500 mr-1"></i>
                        <span>Executing this judgment will immediately disburse escrow funds and close Case #{{ $caseId }}.</span>
                    </div>

                    <button
                        type="button"
                        wire:click="executeArbitration"
                        class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition shadow-xs inline-flex items-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-gavel"></i>
                        <span>Execute Binding Arbitration Ruling</span>
                    </button>
                </div>

            </div>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- EVIDENCE REQUISITION MODAL (ADMIN CONTROL) -->
    <!-- ========================================================================= -->
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 max-w-lg w-full space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-camera text-slate-500"></i>
                        <span>Requisition Evidence from Party</span>
                    </h3>
                    <button 
                        type="button" 
                        wire:click="closeRequestModal" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm cursor-pointer p-1"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Target Party *
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                wire:click="$set('requestTarget', 'buyer')"
                                class="p-2.5 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ $requestTarget === 'buyer' ? 'border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}"
                            >
                                Buyer ({{ $buyerName }})
                            </button>
                            <button
                                type="button"
                                wire:click="$set('requestTarget', 'seller')"
                                class="p-2.5 rounded-xl border text-center font-bold text-xs transition cursor-pointer {{ $requestTarget === 'seller' ? 'border-slate-900 bg-slate-100 text-slate-900 dark:border-white dark:bg-slate-800 dark:text-white' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }}"
                            >
                                Seller ({{ $sellerName }})
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Requisition Title / Requested Item *
                        </label>
                        <input
                            type="text"
                            wire:model="requestTitle"
                            placeholder="e.g. Macro photo of connector pins, pre-shipping test log..."
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                        />
                        @error('requestTitle') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Detailed Inspection Instructions
                        </label>
                        <textarea
                            wire:model="requestInstructions"
                            rows="3"
                            placeholder="Specify requirements such as lighting, angle, video of wave output, or waybill receipt..."
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Response Target Deadline
                        </label>
                        <select
                            wire:model="requestDeadline"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                        >
                            <option value="12_hours">Urgent · Within 12 Hours</option>
                            <option value="24_hours">Standard · Within 24 Hours</option>
                            <option value="48_hours">Extended · Within 48 Hours</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        wire:click="closeRequestModal" 
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        wire:click="submitEvidenceRequest" 
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition cursor-pointer shadow-2xs inline-flex items-center gap-1.5"
                    >
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Dispatch Official Request</span>
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
