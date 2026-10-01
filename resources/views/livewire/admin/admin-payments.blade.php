<x-admin.page title="Payments" description="Payment records with quick detail modal.">
    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search reference or user..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <input wire:model.live="dateFrom" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <input wire:model.live="dateTo" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <select wire:model.live="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $statusOption)
                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Reference</th>
                    <th class="px-4 py-3">Details</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">View More</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $payment->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $payment->reference }}</td>
                        <td class="px-4 py-3">{{ $payment->details ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $payment->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $payment->currency?->symbol ?? '' }}{{ number_format((float) $payment->total, 2) }}</td>
                        <td class="px-4 py-3">{{ $payment->status }}</td>
                        <td class="px-4 py-3"><button wire:click="showPayment({{ $payment->id }})" class="font-medium text-emerald-600 hover:underline">View</button></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>

    @if ($selectedPayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Payment information</h3>
                    <button wire:click="closePayment" class="text-gray-500 hover:text-gray-700">Close</button>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium">Reference:</span> {{ $selectedPayment->reference }}</p>
                    <p><span class="font-medium">User:</span> {{ $selectedPayment->user?->name ?? '—' }}</p>
                    <p><span class="font-medium">Amount:</span> {{ $selectedPayment->currency?->symbol ?? '' }}{{ number_format((float) $selectedPayment->total, 2) }}</p>
                    <p><span class="font-medium">Status:</span> <span class="capitalize font-semibold px-2 py-0.5 rounded text-xs {{ $selectedPayment->status === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $selectedPayment->status }}</span></p>
                    <p><span class="font-medium">Gateway:</span> {{ $selectedPayment->gateway ?? '—' }}</p>
                    <p><span class="font-medium">Details:</span> {{ $selectedPayment->details ?? '—' }}</p>

                    @if ($selectedPayment->receipt)
                        <div class="pt-3 border-t border-gray-100 mt-2">
                            <span class="font-medium block mb-1 text-gray-900">Uploaded Receipt:</span>
                            <a href="{{ asset('storage/' . $selectedPayment->receipt) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View full receipt
                            </a>
                            <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden max-h-60 bg-gray-50 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $selectedPayment->receipt) }}" class="max-h-60 object-contain w-full" alt="Receipt Preview">
                            </div>
                        </div>
                    @endif
                </div>

                @if ($selectedPayment->status !== 'success')
                    <div class="mt-5 pt-3 border-t border-gray-100">
                        <button wire:click="confirmPayment({{ $selectedPayment->id }})" wire:confirm="Are you sure you want to confirm this payment? This will mark it as success and activate the listing/promotion." class="w-full rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 text-sm text-center">
                            Confirm Bank Payment
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-admin.page>
