<x-admin.page title="Coupons" description="Coupon stock and usage management.">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.coupons.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Create coupon</a>
    </div>
    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search coupon code..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <select wire:model.live="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="setSort('qty_remaining')" class="rounded-lg border px-3 py-2 text-sm">Sort Qty Remaining</button>
            <button type="button" wire:click="setSort('qty_used')" class="rounded-lg border px-3 py-2 text-sm">Sort Qty Used</button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Details</th>
                    <th class="px-4 py-3">Total Count</th>
                    <th class="px-4 py-3">Qty Used</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Edit/Delete</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $coupon->code }}</td>
                        <td class="px-4 py-3">
                            {{ $coupon->name }} · {{ $coupon->discount.'%' }}
                        </td>
                        <td class="px-4 py-3">{{ $coupon->quantity }}</td>
                        <td class="px-4 py-3">{{ $coupon->qty_used }}</td>
                        <td class="px-4 py-3">{{ $coupon->is_published ? 'Published' : 'Draft' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs font-medium text-emerald-600 hover:underline">Edit</a>
                                <button type="button" wire:click="toggleStatus({{ $coupon->id }})" class="text-xs font-medium text-gray-600 hover:underline">Toggle status</button>
                                <button type="button" wire:click="deleteCoupon({{ $coupon->id }})" wire:confirm="Delete coupon?" class="text-xs font-medium text-red-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No coupons found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $coupons->links() }}</div>
</x-admin.page>
