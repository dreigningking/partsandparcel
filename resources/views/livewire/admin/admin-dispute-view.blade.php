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
                <span>Returns &amp; Replacements</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">
                    {{ $returnsCount }} Returns • {{ $replacementsCount }} Replacements
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Oversee return authorizations, defective part shipping, and component replacement fulfillment.
            </p>
        </div>

        <!-- TABS SWITCHER -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
            <button
                wire:click="setTab('returns')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $activeTab === 'returns' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:white' }}"
            >
                Returns ({{ $returnsCount }})
            </button>
            <button
                wire:click="setTab('replacements')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $activeTab === 'replacements' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:white' }}"
            >
                Replacements ({{ $replacementsCount }})
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
                placeholder="Search invoice #, buyer, seller..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="requested">Requested</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="received">Received</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">ID &amp; Invoice</th>
                        <th class="py-3 px-4">Buyer</th>
                        <th class="py-3 px-4">Seller</th>
                        <th class="py-3 px-4">Items Count</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-white font-mono">
                                    #{{ $activeTab === 'returns' ? 'RET' : 'REP' }}-{{ $rec->id }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    Inv: {{ $rec->invoice?->invoice_number }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $rec->buyer?->name }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $rec->seller?->name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                {{ $rec->items?->count() ?: 1 }} items
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $bColor = match($rec->status) {
                                        'completed', 'accepted', 'received' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'rejected', 'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $bColor }}">
                                    {{ $rec->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $rec->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button
                                    wire:click="showRecord({{ $rec->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:hover:bg-pp-900 text-xs font-bold transition cursor-pointer"
                                >
                                    Inspect
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No {{ $activeTab }} records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $records->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($selectedRecord)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        {{ ucfirst($activeTab) }} Record #{{ $selectedRecord->id }}
                    </h3>
                    <button wire:click="closeRecord" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Buyer</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedRecord->buyer?->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Seller</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedRecord->seller?->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Invoice</span>
                            <p class="font-mono font-bold text-slate-800 dark:text-white">{{ $selectedRecord->invoice?->invoice_number }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Status</span>
                            <p class="font-extrabold text-pp-600 uppercase">{{ $selectedRecord->status }}</p>
                        </div>
                    </div>

                    @if($selectedRecord->notes)
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Notes</span>
                            <p class="text-slate-700 dark:text-slate-300 mt-0.5">{{ $selectedRecord->notes }}</p>
                        </div>
                    @endif

                    @if($activeTab === 'returns')
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-bold text-slate-500">Actions:</span>
                            <button wire:click="updateReturnStatus({{ $selectedRecord->id }}, 'received')" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition cursor-pointer">
                                Mark Received
                            </button>
                            <button wire:click="updateReturnStatus({{ $selectedRecord->id }}, 'accepted')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition cursor-pointer">
                                Accept &amp; Complete
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
