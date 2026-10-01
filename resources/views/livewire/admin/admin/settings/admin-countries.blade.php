<x-admin.page title="Countries &amp; regions" description="Supported locations, default country, and currency binding.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <button type="button" wire:click="openCreate" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add country</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Currency</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3">Default</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($countries as $country)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $country->name }}
                            @if ($country->is_default)
                                <span class="ml-2 rounded bg-emerald-100 px-2 py-0.5 text-xs text-emerald-800 font-semibold">Default</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $country->code ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $country->currency?->code ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $country->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3">
                            @if ($country->is_default)
                                <span class="text-emerald-700 font-medium">Yes</span>
                            @else
                                <button type="button" wire:click="setDefault({{ $country->id }})" class="text-xs text-gray-500 hover:text-emerald-600 underline">Set default</button>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="openEdit({{ $country->id }})" class="text-emerald-600 hover:underline">Edit</button>
                            <button type="button" wire:click="deleteCountry({{ $country->id }})" wire:confirm="{{ __('Delete this country?') }}" class="ml-2 text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No countries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="closeModal">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingId ? __('Edit country') : __('New country') }}</h3>
                <form wire:submit="saveCountry" class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Code</label>
                            <input type="text" wire:model="code" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm uppercase" />
                            @error('code') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" wire:model="slug" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            @error('slug') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Currency</label>
                        <select wire:model="currency_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            <option value="">—</option>
                            @foreach ($currencies as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('currency_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Phone code</label>
                            <input type="text" wire:model="phone_code" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Language</label>
                            <input type="text" wire:model="language_code" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Flag (emoji or code)</label>
                        <input type="text" wire:model="flag" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                    </div>
                    <div class="flex items-center gap-4 pt-1">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            Active
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="is_default" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            Default country
                        </label>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Save</button>
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-gray-200 px-4 py-2 text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-admin.page>
