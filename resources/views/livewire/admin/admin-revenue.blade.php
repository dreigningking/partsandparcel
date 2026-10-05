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
                <span>Platform Revenue</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    ₦{{ number_format($totalRevenue, 2) }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Direct accounting of escrow fee retained, subscription billing, and platform revenue items.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <div class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                <span class="text-slate-400 font-bold">Commissions:</span>
                <span class="font-extrabold text-pp-600">₦{{ number_format($commissionRevenue, 2) }}</span>
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                <span class="text-slate-400 font-bold">Subs:</span>
                <span class="font-extrabold text-indigo-600">₦{{ number_format($subscriptionRevenue, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search invoice # or payment ref..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="typeFilter"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Revenue Streams</option>
                <option value="commission">Escrow Commissions</option>
                <option value="subscription">Subscriptions</option>
                <option value="service_fee">Service Fees</option>
                <option value="other">Other Fees</option>
            </select>
        </div>
    </div>

    <!-- REVENUE LEDGER TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Stream Type</th>
                        <th class="py-3 px-4">Invoice / Reference</th>
                        <th class="py-3 px-4">Buyer</th>
                        <th class="py-3 px-4">Seller</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Recorded At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($revenues as $rev)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $rev->type === 'commission' ? 'bg-pp-100 text-pp-700' : ($rev->type === 'subscription' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $rev->type)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800 dark:text-white">
                                {{ $rev->invoice?->invoice_number ?: ($rev->payment?->reference ?: '#REV-' . $rev->id) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $rev->invoice?->buyer?->name ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $rev->invoice?->seller?->name ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-emerald-600 text-sm">
                                ₦{{ number_format($rev->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $rev->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No revenue records found matching filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $revenues->links() }}
        </div>
    </div>
</div>
