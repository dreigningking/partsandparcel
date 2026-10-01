<x-admin.page title="Promotion plans" description="Blog, featured, and newsletter promotion packages with default prices.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <button type="button" wire:click="openCreate" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add plan</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Features</th>
                    <th class="px-4 py-3">Default prices</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($plans as $plan)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $plan->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $planTypes[$plan->type] ?? $plan->type }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 space-y-1">
                            @if (!empty($plan->features['clicks']))
                                <div>Clicks: {{ $plan->features['clicks'] }}</div>
                            @endif
                            @if (!empty($plan->features['posts']))
                                <div>Posts: {{ $plan->features['posts'] }}</div>
                            @endif
                            @if (!empty($plan->features['emails']))
                                <div>Emails: {{ $plan->features['emails'] }}</div>
                            @endif
                            @if (!empty($plan->features['recipients']))
                                <div>Recipients: {{ $plan->features['recipients'] }}</div>
                            @endif
                            @if (empty($plan->features['clicks']) && empty($plan->features['posts']) && empty($plan->features['emails']) && empty($plan->features['recipients']))
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @foreach ($plan->prices as $p)
                                <div>{{ $p->currency?->code }} {{ $p->amount }}</div>
                            @endforeach
                            @if ($plan->prices->isEmpty()) — @endif
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="openEdit({{ $plan->id }})" class="text-emerald-600 hover:underline">Edit</button>
                            <button type="button" wire:click="deletePlan({{ $plan->id }})" wire:confirm="{{ __('Delete this plan?') }}" class="ml-2 text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No promotion plans yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="closeModal">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingId ? __('Edit promotion plan') : __('New promotion plan') }}</h3>
                <form wire:submit="savePlan" class="mt-4 space-y-4">
                    <!-- Plan Listings Section -->
                    <div class="space-y-3 border-b border-gray-200 pb-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Name</label>
                            <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Slug (optional)</label>
                            <input type="text" wire:model="slug" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            @error('slug') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Type</label>
                            <select wire:model="type" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                @foreach ($planTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="space-y-3 border-b border-gray-200 pb-4">
                        <label class="text-sm font-medium text-gray-700">Features</label>
                        @foreach ($featureRows as $i => $row)
                            <div class="grid grid-cols-2 gap-3" wire:key="fr-{{ $i }}">
                                <div>
                                    <label class="text-xs font-medium text-gray-600">{{ ucwords(str_replace('_', ' ', $row['key'])) }}</label>
                                    <div class="mt-1 text-xs text-gray-500">Key: {{ $row['key'] }}</div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600">Value</label>
                                    <input type="text" wire:model="featureRows.{{ $i }}.value" placeholder="Enter feature value" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 text-sm" />
                                </div>
                            </div>
                        @endforeach
                        @error('featureRows') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prices Section -->
                    <div class="space-y-3 pb-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Default prices (global)</label>
                            <button type="button" wire:click="addPriceRow" class="text-xs text-emerald-600 hover:underline">Add row</button>
                        </div>
                        @foreach ($priceRows as $i => $row)
                            <div class="mt-2 flex flex-wrap gap-2" wire:key="ppr-{{ $i }}">
                                <select wire:model="priceRows.{{ $i }}.currency_id" class="rounded-lg border border-gray-200 px-2 py-1 text-sm">
                                    @foreach ($currencies as $c)
                                        <option value="{{ $c->id }}">{{ $c->code }}</option>
                                    @endforeach
                                </select>
                                <input type="text" wire:model="priceRows.{{ $i }}.amount" class="w-28 rounded-lg border border-gray-200 px-2 py-1 text-sm" />
                                <button type="button" wire:click="removePriceRow({{ $i }})" class="text-xs text-red-600 hover:underline">Remove</button>
                            </div>
                        @endforeach
                        @error('priceRows') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
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
