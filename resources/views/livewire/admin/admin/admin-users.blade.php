<x-admin.page title="Users" description="User management with subscriptions, seats and listing activity.">
    <div class="mb-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search name, email, phone..." class="w-full max-w-sm rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <button wire:click="sort('name')" type="button" class="rounded-lg border px-3 py-2 text-sm">Sort by Name</button>
        <button wire:click="sort('listings_count')" type="button" class="rounded-lg border px-3 py-2 text-sm">Sort by Listings</button>
        <button wire:click="sort('subscription_seats')" type="button" class="rounded-lg border px-3 py-2 text-sm">Sort by Seats</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Details</th>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">Listings</th>
                    <th class="px-4 py-3">Active Subscriptions</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $row->name }}</div>
                            <div class="text-xs text-gray-500">{{ $row->email }}</div>
                            <div class="text-xs text-gray-500">{{ $row->phone ?: 'No phone' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->country?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->listings_count }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $row->subscriptions->where('status', 'active')->count() }} subscriptions | {{ (int) ($row->subscription_seats ?? 0) }} seats
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', ['user' => $row->id]) }}" class="font-medium text-emerald-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-admin.page>
