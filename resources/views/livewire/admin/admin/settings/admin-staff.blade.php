<x-admin.page title="Staff" description="Admin accounts and their assigned roles.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <button type="button" wire:click="openCreate" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add staff</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($staff as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($user->id !== auth()->id())
                                <button type="button" wire:click="openEdit({{ $user->id }})" class="text-emerald-600 hover:underline">{{ __('Edit') }}</button>
                                <button type="button" wire:click="deleteStaff({{ $user->id }})" wire:confirm="{{ __('Remove this staff member?') }}" class="ml-2 text-red-600 hover:underline">{{ __('Remove') }}</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No staff users.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="closeModal">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingUserId ? __('Edit staff') : __('New staff') }}</h3>
                <form wire:submit="saveStaff" class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="email" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Password {{ $editingUserId ? '(leave blank to keep)' : '' }}</label>
                        <input type="password" wire:model="password" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" autocomplete="new-password" />
                        @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Role</label>
                        <select wire:model="role_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            @foreach ($adminRoles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
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
