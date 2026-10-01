<x-admin.page title="Advertisements" description="Manage advertisement banner requests, view uploaded payment receipts, and approve placements.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <button wire:click="openCreateAd" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
            Create Advertisement
        </button>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search company or email..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <select wire:model.live="placementFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All Placements</option>
            @foreach ($placements as $place)
                <option value="{{ $place }}">{{ str_replace('_', ' ', $place) }}</option>
            @endforeach
        </select>
        <select wire:model.live="statusFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Company</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Placement</th>
                    <th class="px-4 py-3">Duration</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($ads as $ad)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $ad->company }}</td>
                        <td class="px-4 py-3">{{ $ad->email }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $ad->placement) }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if ($ad->start_date && $ad->end_date)
                                {{ $ad->start_date->format('M d, Y') }} — {{ $ad->end_date->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">₦{{ number_format((float) $ad->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="capitalize text-xs px-2 py-0.5 rounded font-medium {{ $ad->payment_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $ad->payment_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="showAd({{ $ad->id }})" class="font-medium text-emerald-600 hover:underline">View Details</button>
                            <button wire:click="deleteAd({{ $ad->id }})" wire:confirm="Are you sure you want to delete this ad request?" class="ml-2 text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No advertisements found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ads->links() }}</div>

    <!-- Detail Modal -->
    @if ($selectedAd)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between border-b pb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Advertisement Details</h3>
                    <button wire:click="closeAd" class="text-gray-500 hover:text-gray-700">Close</button>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-700">
                    <div class="space-y-3">
                        <p><span class="font-medium text-gray-500 block">Company:</span> <strong class="text-gray-900">{{ $selectedAd->company }}</strong></p>
                        <p><span class="font-medium text-gray-500 block">Contact Email:</span> {{ $selectedAd->email }}</p>
                        <p><span class="font-medium text-gray-500 block">Placement:</span> <span class="capitalize">{{ str_replace('_', ' ', $selectedAd->placement) }}</span></p>
                        <p><span class="font-medium text-gray-500 block">Amount:</span> ₦{{ number_format((float) $selectedAd->amount, 2) }}</p>
                        <p><span class="font-medium text-gray-500 block">Payment Status:</span> <span class="capitalize font-semibold px-2 py-0.5 rounded text-xs {{ $selectedAd->payment_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $selectedAd->payment_status }}</span></p>
                        <p><span class="font-medium text-gray-500 block">Description / Target URL:</span> {{ $selectedAd->description ?? '—' }}</p>

                        <!-- Change Dates form -->
                        <form wire:submit="saveDates" class="pt-3 border-t space-y-3">
                            <h4 class="font-semibold text-gray-800">Adjust Run Dates</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-gray-500">Start Date</label>
                                    <input type="date" wire:model="edit_start_date" class="w-full rounded border-gray-300 px-2 py-1 text-xs" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">End Date</label>
                                    <input type="date" wire:model="edit_end_date" class="w-full rounded border-gray-300 px-2 py-1 text-xs" />
                                </div>
                            </div>
                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded transition">Update Dates</button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @if ($selectedAd->image)
                            <div>
                                <span class="font-medium text-gray-500 block mb-1">Ad Banner Image:</span>
                                <a href="{{ asset('storage/' . $selectedAd->image) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">View original banner</a>
                                <div class="mt-1 border border-gray-200 rounded-lg overflow-hidden max-h-40 bg-gray-50 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $selectedAd->image) }}" class="max-h-40 object-contain w-full" alt="Ad Banner">
                                </div>
                            </div>
                        @endif

                        @if ($selectedAd->receipt)
                            <div class="pt-3 border-t">
                                <span class="font-medium text-gray-500 block mb-1">Payment Receipt:</span>
                                <a href="{{ asset('storage/' . $selectedAd->receipt) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">View original receipt</a>
                                <div class="mt-1 border border-gray-200 rounded-lg overflow-hidden max-h-40 bg-gray-50 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $selectedAd->receipt) }}" class="max-h-40 object-contain w-full" alt="Payment Receipt">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-3 border-t flex justify-end gap-2">
                    <button wire:click="closeAd" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Close</button>
                    @if ($selectedAd->payment_status !== 'completed')
                        <button wire:click="confirmPayment({{ $selectedAd->id }})" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                            Verify & Approve Ad
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Create Ad Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between border-b pb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Create Advertisement</h3>
                    <button wire:click="closeCreateAd" class="text-gray-500 hover:text-gray-700">Close</button>
                </div>
                
                <form wire:submit="createAd" class="space-y-4 text-sm text-gray-700">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Company *</label>
                            <input type="text" wire:model="new_company" class="w-full rounded-lg border-gray-300 px-3 py-2" required />
                            @error('new_company') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" wire:model="new_email" class="w-full rounded-lg border-gray-300 px-3 py-2" required />
                            @error('new_email') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Placement *</label>
                            <select wire:model="new_placement" class="w-full rounded-lg border-gray-300 px-3 py-2" required>
                                @foreach ($placements as $place)
                                    <option value="{{ $place }}">{{ str_replace('_', ' ', $place) }}</option>
                                @endforeach
                            </select>
                            @error('new_placement') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Amount (₦) *</label>
                            <input type="number" step="0.01" wire:model="new_amount" class="w-full rounded-lg border-gray-300 px-3 py-2" required />
                            @error('new_amount') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Description / Target URL</label>
                        <textarea wire:model="new_description" rows="2" class="w-full rounded-lg border-gray-300 px-3 py-2"></textarea>
                        @error('new_description') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" wire:model="new_start_date" class="w-full rounded-lg border-gray-300 px-3 py-2" />
                            @error('new_start_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" wire:model="new_end_date" class="w-full rounded-lg border-gray-300 px-3 py-2" />
                            @error('new_end_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Payment Status *</label>
                            <select wire:model="new_payment_status" class="w-full rounded-lg border-gray-300 px-3 py-2" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                            @error('new_payment_status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Banner Image *</label>
                            <input type="file" wire:model="new_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" required />
                            <div wire:loading wire:target="new_image" class="mt-1 text-xs text-blue-600">Uploading...</div>
                            @error('new_image') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            
                            @if ($new_image)
                                <div class="mt-2 text-xs text-emerald-600">Image selected.</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t flex justify-end gap-2">
                        <button type="button" wire:click="closeCreateAd" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold" wire:loading.attr="disabled" wire:target="createAd">
                            Create Ad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-admin.page>
