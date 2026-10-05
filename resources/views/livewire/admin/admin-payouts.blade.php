<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Finance</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Payouts &amp; Settlements</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    ₦{{ number_format($totalPaidOut, 2) }} Disbursed
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Release seller escrow funds, review payout batches, and monitor pending merchant disbursements.
            </p>
        </div>

        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
            <button
                wire:click="setTab('payouts')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $activeTab === 'payouts' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:white' }}"
            >
                Payout Batches ({{ $payoutsCount }})
            </button>
            <button
                wire:click="setTab('settlements')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $activeTab === 'settlements' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:white' }}"
            >
                Settlements Queue ({{ $settlementsCount }})
            </button>
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
                placeholder="Search ref, merchant, invoice..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="eligible">Eligible</option>
                <option value="paid">Paid / Settled</option>
                <option value="failed">Failed</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        @if($activeTab === 'payouts')
                            <th class="py-3 px-4">Payout Ref</th>
                            <th class="py-3 px-4">Merchant (Seller)</th>
                            <th class="py-3 px-4">Net Amount</th>
                            <th class="py-3 px-4">Provider / Channel</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Date Disbursed</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        @else
                            <th class="py-3 px-4">Settlement # &amp; Inv</th>
                            <th class="py-3 px-4">Merchant</th>
                            <th class="py-3 px-4">Gross</th>
                            <th class="py-3 px-4">Commission</th>
                            <th class="py-3 px-4">Net Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($items as $row)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            @if($activeTab === 'payouts')
                                <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900 dark:text-white">
                                    {{ $row->reference }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $row->seller?->name }}
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-emerald-600">
                                    ₦{{ number_format($row->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 uppercase text-[11px]">
                                    {{ $row->provider ?: 'Bank Transfer' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $row->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                    {{ $row->paid_at ? $row->paid_at->format('M d, Y') : 'Pending' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($row->status !== 'paid')
                                        <button
                                            wire:click="markPayoutCompleted({{ $row->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition cursor-pointer"
                                        >
                                            Confirm Paid
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Completed</span>
                                    @endif
                                </td>
                            @else
                                <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900 dark:text-white">
                                    #SET-{{ $row->id }} ({{ $row->invoice?->invoice_number }})
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $row->seller?->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                    ₦{{ number_format($row->gross_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    -₦{{ number_format($row->commission, 2) }}
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-emerald-600">
                                    ₦{{ number_format($row->net_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $row->status === 'settled' ? 'bg-emerald-100 text-emerald-800' : ($row->status === 'eligible' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($row->status === 'pending')
                                        <button
                                            wire:click="markSettlementEligible({{ $row->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition cursor-pointer"
                                        >
                                            Mark Eligible
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[11px]">{{ ucfirst($row->status) }}</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No records found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
    </div>
</div>
