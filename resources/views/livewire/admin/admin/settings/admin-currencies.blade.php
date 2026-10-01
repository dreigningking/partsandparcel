<x-admin.page title="Currencies" description="Manage display codes and the single default checkout currency.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <button type="button" wire:click="openCreate" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add currency</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Symbol</th>
                    <th class="px-4 py-3">Default</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3">Gateway</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($currencies as $c)
                    <tr>
                        <td class="px-4 py-3 font-mono font-medium">{{ $c->code }}</td>
                        <td class="px-4 py-3">{{ $c->name }}</td>
                        <td class="px-4 py-3">{{ $c->symbol }}</td>
                        <td class="px-4 py-3">{{ $c->is_default ? 'Yes' : '—' }}</td>
                        <td class="px-4 py-3">{{ $c->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $c->payment_gateway ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if (!$c->is_default)
                                <button type="button" wire:click="setDefault({{ $c->id }})" class="text-emerald-600 hover:underline">Set default</button>
                            @endif
                            <button type="button" wire:click="openEdit({{ $c->id }})" class="ml-2 text-gray-700 hover:underline">Edit</button>
                            <button type="button" wire:click="deleteCurrency({{ $c->id }})" wire:confirm="{{ __('Delete this currency?') }}" class="ml-2 text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">No currencies.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="closeModal">
            <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingId ? __('Edit currency') : __('New currency') }}</h3>
                <form wire:submit="saveCurrency" class="mt-4 space-y-3">
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
                            <label class="text-sm font-medium text-gray-700">Slug (optional)</label>
                            <input type="text" wire:model="slug" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            @error('slug') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Symbol</label>
                            <input type="text" wire:model="symbol" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Symbol position</label>
                            <select wire:model="symbol_position" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                <option value="before">before</option>
                                <option value="after">after</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">1000s sep.</label>
                            <input type="text" wire:model="thousands_separator" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Dec. sep.</label>
                            <input type="text" wire:model="decimal_separator" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Multiplier</label>
                            <input type="number" wire:model="decimal_multiplier" min="1" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 text-sm" />
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="is_default" class="rounded border-gray-300" />
                        Default currency
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300" />
                        Active
                    </label>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment gateway</label>
                        <select wire:model="payment_gateway" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            <option value="">None</option>
                            <option value="paystack">Paystack</option>
                            <option value="flutterwave">Flutterwave</option>
                        </select>
                        @error('payment_gateway') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Checkout uses this gateway for payments in this currency. API keys stay in <code>.env</code>.</p>
                    </div>

                    {{-- Manual Payment Section --}}
                    <div class="border-t border-gray-100 pt-3 mt-1">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" wire:model.live="show_manual_payment" class="rounded border-gray-300 text-emerald-600" />
                            Enable Manual Payment Option
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Show bank transfer instructions to users at checkout.</p>
                    </div>

                    @if($show_manual_payment)
                        <div class="space-y-2 border border-amber-100 bg-amber-50 rounded-lg p-3">
                            <p class="text-xs font-semibold text-amber-700 mb-2">Bank Details for {{ $code ?: 'Currency' }}</p>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Bank Name</label>
                                <input type="text" wire:model="bank_name" placeholder="e.g. First Bank Nigeria" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm" />
                                @error('bank_name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Account Number</label>
                                <input type="text" wire:model="bank_account_number" placeholder="e.g. 0123456789" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm" />
                                @error('bank_account_number') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Account Name</label>
                                <input type="text" wire:model="account_name" placeholder="e.g. Propatis Ltd" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm" />
                                @error('account_name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Swift / Sort Code (optional)</label>
                                <input type="text" wire:model="swift_code" placeholder="e.g. FBNBNL1X" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm" />
                                @error('swift_code') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Payment Instructions (optional)</label>
                                <textarea wire:model="bank_instructions" rows="2" placeholder="e.g. Use your reference as payment description" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm resize-none"></textarea>
                                @error('bank_instructions') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Save</button>
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-gray-200 px-4 py-2 text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-admin.page>
