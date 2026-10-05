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
                <span>Shipments &amp; Tracking</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">{{ $totalCount }} Total</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Monitor platform dispatches, delivery statuses, carriers, and transit milestones.
            </p>
        </div>

        <!-- STATS BADGES -->
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs border border-amber-200">
                Pending: {{ $pendingCount }}
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 font-extrabold text-xs border border-blue-200">
                In Transit: {{ $dispatchedCount }}
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200">
                Delivered: {{ $deliveredCount }}
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
                placeholder="Search tracking #, provider, sender, buyer..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
                wire:model.live="status"
                class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="pending">Pending Dispatch</option>
                <option value="dispatched">Dispatched / In Transit</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- SHIPMENTS TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Tracking &amp; Carrier</th>
                        <th class="py-3 px-4">Sender (Seller)</th>
                        <th class="py-3 px-4">Receiver (Buyer)</th>
                        <th class="py-3 px-4">Route</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-white font-mono">
                                    {{ $shipment->tracking_number ?: ('#SHP-' . $shipment->id) }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium">
                                    {{ $shipment->provider_name ?: 'Standard Courier' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $shipment->sender?->name ?: 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $shipment->sender?->email }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $shipment->receiver?->name ?: 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $shipment->receiver?->email }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-700 dark:text-slate-300">
                                    {{ $shipment->origin_city ?: ($shipment->originLocation?->city ?: 'Origin') }}
                                    <span class="text-slate-400">→</span>
                                    {{ $shipment->destination_city ?: ($shipment->destinationLocation?->city ?: 'Dest.') }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $badge = match($shipment->status) {
                                        'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'dispatched' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                                        'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $badge }}">
                                    {{ $shipment->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $shipment->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button
                                    wire:click="showShipment({{ $shipment->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:hover:bg-pp-900 text-xs font-bold transition cursor-pointer"
                                >
                                    Inspect
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No shipments found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $shipments->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($selectedShipment)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        Shipment #{{ $selectedShipment->tracking_number ?: $selectedShipment->id }}
                    </h3>
                    <button wire:click="closeShipment" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Carrier</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedShipment->provider_name ?: 'Standard' }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Fee</span>
                            <p class="font-extrabold text-emerald-600">₦{{ number_format($selectedShipment->fee ?: 0, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Sender</span>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $selectedShipment->sender?->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold text-[10px] uppercase">Receiver</span>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $selectedShipment->receiver?->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[11px] font-bold text-slate-500">Update Status:</span>
                        <button wire:click="updateStatus({{ $selectedShipment->id }}, 'dispatched')" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition cursor-pointer">
                            Mark Dispatched
                        </button>
                        <button wire:click="updateStatus({{ $selectedShipment->id }}, 'delivered')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition cursor-pointer">
                            Mark Delivered
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
