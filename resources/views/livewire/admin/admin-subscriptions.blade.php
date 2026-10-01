<x-admin.page title="Subscriptions" description="Subscription records and invoice lookup.">
    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-5">
        <label class="block">
            <span class="text-xs font-medium text-gray-500 mb-1 block">Search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search user..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        </label>

        <label class="block">
            <span class="text-xs font-medium text-gray-500 mb-1 block">Date from</span>
            <input wire:model.live="dateFrom" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        </label>

        <label class="block">
            <span class="text-xs font-medium text-gray-500 mb-1 block">Date to</span>
            <input wire:model.live="dateTo" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        </label>

        <label class="block">
            <span class="text-xs font-medium text-gray-500 mb-1 block">Status</span>
            <select wire:model.live="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                @endforeach
            </select>
        </label>

        <label class="block">
            <span class="text-xs font-medium text-gray-500 mb-1 block">Sort</span>
            <select wire:model.live="sortBy" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                <option value="end_at">Expiry date</option>
                <option value="start_at">Purchase date</option>
                
            </select>
        </label>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Date Purchased</th>
                    <th class="px-4 py-3">Subscription ID</th>
                    <th class="px-4 py-3">Plan</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Seats</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Expiry Date</th>
                    <th class="px-4 py-3">View Invoice</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ optional($subscription->start_at)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 font-mono text-xs">SUB-{{ $subscription->id }}</td>
                        <td class="px-4 py-3">{{ $subscription->plan?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $subscription->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $subscription->subscribed_listings_count }} of {{ $subscription->seats }}</td>
                        <td class="px-4 py-3">{{ $subscription->status }}</td>
                        <td class="px-4 py-3">{{ optional($subscription->end_at)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="showInvoice({{ $subscription->id }})" type="button" class="font-medium text-emerald-600 hover:underline">View</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No subscriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $subscriptions->links() }}</div>

    @if ($invoicePayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice payment info</h3>
                    <button wire:click="closeInvoice" class="text-gray-500 hover:text-gray-700">Close</button>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium">Reference:</span> {{ $invoicePayment->reference }}</p>
                    <p><span class="font-medium">User:</span> {{ $invoicePayment->user?->name ?? '—' }}</p>
                    <p><span class="font-medium">Amount:</span> {{ $invoicePayment->currency?->symbol ?? '' }}{{ number_format((float) $invoicePayment->total, 2) }}</p>
                    <p><span class="font-medium">Gateway:</span> {{ $invoicePayment->gateway ?? '—' }}</p>
                    <p><span class="font-medium">Status:</span> {{ $invoicePayment->status }}</p>
                    <p><span class="font-medium">Details:</span> {{ $invoicePayment->details ?? '—' }}</p>
                </div>
            </div>
        </div>
    @endif
</x-admin.page>
