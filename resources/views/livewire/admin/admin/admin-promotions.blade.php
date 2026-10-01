<x-admin.page title="Promotions" description="Promotions with analytics by listing and campaign type.">
    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-5">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by user..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <input wire:model.live="dateFrom" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <input wire:model.live="dateTo" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <select wire:model.live="type" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All types</option>
            @foreach ($types as $typeOption)
                <option value="{{ $typeOption }}">{{ class_basename($typeOption) }}</option>
            @endforeach
        </select>
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
                    <th class="px-4 py-3">Date Purchased</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Listing</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">View Analytics</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($promotions as $promotion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ optional($promotion->start_at)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $promotion->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ class_basename($promotion->promotable_type ?: $promotion->listing_type ?? $promotion->plan?->type ?? 'listing') }}</td>
                        <td class="px-4 py-3">
                            @if ($promotion->listing)
                                <a href="{{ route('admin.listings.show', ['listing' => $promotion->listing->id]) }}" class="text-emerald-600 hover:underline">{{ $promotion->listing->name }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $promotion->status }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="openAnalytics({{ $promotion->id }})" class="font-medium text-emerald-600 hover:underline">View</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No promotions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $promotions->links() }}</div>

    @if ($analytics)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Promotion analytics</h3>
                    <button wire:click="closeAnalytics" class="text-gray-500 hover:text-gray-700">Close</button>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium">Views:</span> {{ $analytics['views'] }}</p>
                    <p><span class="font-medium">Listing clicks:</span> {{ $analytics['clicks'] }}</p>
                    <p><span class="font-medium">Clicked actions:</span> {{ $analytics['clicked_actions'] }}</p>
                    <p><span class="font-medium">Target:</span> {{ $analytics['target_type'] ?? '—' }} ({{ $analytics['target_count'] ?? 0 }})</p>
                </div>
            </div>
        </div>
    @endif
</x-admin.page>
