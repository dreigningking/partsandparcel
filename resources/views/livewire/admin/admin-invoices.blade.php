<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Marketplace</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Invoices</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">₦{{ number_format($totalVolume, 2) }} Settled</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                All marketplace transactions, escrow invoices, direct settlements, and commissions.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200">
                Paid: {{ $paidCount }}
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs border border-amber-200">
                Pending: {{ $pendingCount }}
            </span>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search invoice #, buyer, seller..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="paymentMethod"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Payment Types</option>
                <option value="platform">Platform Escrow</option>
                <option value="direct">Direct Bank Transfer</option>
            </select>

            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="issued">Issued / Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
    </div>

    <!-- INVOICES TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Buyer</th>
                        <th class="py-3 px-4">Seller</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4">Total Amount</th>
                        <th class="py-3 px-4">Commission</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900 dark:text-white">
                                {{ $inv->invoice_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $inv->buyer?->name }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $inv->seller?->name }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $inv->payment_method === 'platform' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $inv->payment_method === 'platform' ? 'Platform Escrow' : 'Direct Transfer' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900 dark:text-white">
                                ₦{{ number_format($inv->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-emerald-600 font-bold">
                                ₦{{ number_format($inv->commission ?: 0, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $statusBadge = match($inv->status) {
                                        'paid', 'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'refunded' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
                                        'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusBadge }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $inv->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a
                                    href="{{ route('admin.invoices.show', $inv) }}"
                                    class="px-3 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:hover:bg-pp-900 text-xs font-bold transition inline-flex items-center gap-1"
                                >
                                    <span>Manage</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                No invoices found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($selectedInvoice)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        Invoice {{ $selectedInvoice->invoice_number }}
                    </h3>
                    <button wire:click="closeInvoice" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Buyer</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedInvoice->buyer?->name }} ({{ $selectedInvoice->buyer?->email }})</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Seller</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedInvoice->seller?->name }} ({{ $selectedInvoice->seller?->email }})</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Subtotal / Discount</span>
                            <p class="font-bold text-slate-800 dark:text-white">₦{{ number_format($selectedInvoice->subtotal, 2) }} / -₦{{ number_format($selectedInvoice->discount, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Total / Commission</span>
                            <p class="font-extrabold text-slate-900 dark:text-white">₦{{ number_format($selectedInvoice->total, 2) }} (Comm: ₦{{ number_format($selectedInvoice->commission, 2) }})</p>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div>
                        <h4 class="font-extrabold text-slate-700 dark:text-slate-300 text-xs mb-2">Order Line Items</h4>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden">
                            @foreach($selectedInvoice->items as $item)
                                <div class="p-2.5 flex items-center justify-between text-xs">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white">{{ $item->item?->name ?: 'Item #' . $item->item_id }}</p>
                                        <p class="text-[10px] text-slate-400">Qty: {{ $item->quantity }} @ ₦{{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <span class="font-extrabold text-slate-900 dark:text-white">
                                        ₦{{ number_format($item->total_price, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
