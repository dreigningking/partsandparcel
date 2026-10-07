<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Trust &amp; Resolution</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Disputes Arbitration</span>
                @if($openCount > 0)
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                        {{ $openCount }} Action Required
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Formal disputes escalated for platform mediation, escrow release, or buyer refund rulings.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 font-extrabold text-xs border border-rose-200">
                Open: {{ $openCount }}
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200">
                Resolved: {{ $resolvedCount }}
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search dispute reason, party, invoice #..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="opened">Opened</option>
                <option value="escalated">Escalated</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Dispute # &amp; Inv</th>
                        <th class="py-3 px-4">Opened By</th>
                        <th class="py-3 px-4">Counterparty</th>
                        <th class="py-3 px-4">Dispute Reason</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Opened At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($disputes as $disp)
                        @php
                            $buyer = $disp->issue?->invoice?->buyer;
                            $seller = $disp->issue?->invoice?->seller;
                            $isBuyerOpener = $disp->opened_by === $buyer?->id;
                            $counterparty = $isBuyerOpener ? $seller : $buyer;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900 dark:text-white">
                                #DSP-{{ $disp->id }}
                                @if($disp->issue?->invoice)
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $disp->issue->invoice->invoice_number }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $disp->opener?->name ?: 'Member' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $counterparty?->name ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                {{ $disp->reason ?: ($disp->issue?->description ?: 'Escalated order issue') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $dColor = match($disp->status) {
                                        'resolved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'escalated', 'opened' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $dColor }}">
                                    {{ $disp->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $disp->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                <a
                                    href="{{ route('admin.disputes.show', $disp->id) }}"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-xs font-bold transition inline-block"
                                >
                                    Inspect Case
                                </a>
                                <button
                                    wire:click="showDispute({{ $disp->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:hover:bg-pp-900 text-xs font-bold transition cursor-pointer"
                                >
                                    Arbitrate
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No disputes found matching filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $disputes->links() }}
        </div>
    </div>

    <!-- ARBITRATION MODAL -->
    @if($selectedDispute)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        Arbitrate Dispute #DSP-{{ $selectedDispute->id }}
                    </h3>
                    <button wire:click="closeDispute" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Claimant</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedDispute->opener?->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Invoice Total</span>
                            <p class="font-extrabold text-emerald-600">₦{{ number_format($selectedDispute->issue?->invoice?->total ?: 0, 2) }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Reason &amp; Description</span>
                            <p class="text-slate-700 dark:text-slate-300 mt-0.5">{{ $selectedDispute->reason ?: ($selectedDispute->issue?->description ?: 'No description provided') }}</p>
                        </div>
                    </div>

                    @if($selectedDispute->status !== 'resolved')
                        <div class="space-y-2 pt-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 text-xs">
                                Arbitration Decision Note (Optional)
                            </label>
                            <textarea
                                wire:model="resolutionNote"
                                placeholder="Explain ruling, return instructions, or settlement findings..."
                                rows="3"
                                class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none"
                            ></textarea>

                            <div class="flex items-center gap-2 pt-2">
                                <button
                                    wire:click="resolveDispute('Refund Approved for Buyer')"
                                    class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs transition cursor-pointer text-center"
                                >
                                    Rule: Refund Buyer
                                </button>
                                <button
                                    wire:click="resolveDispute('Funds Released to Seller')"
                                    class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition cursor-pointer text-center"
                                >
                                    Rule: Release to Seller
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs">
                            Resolved: {{ $selectedDispute->resolution }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
