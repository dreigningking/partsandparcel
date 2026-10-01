<x-admin.page title="Roles &amp; permissions" description="Define roles and attach permission slugs. Omit legacy inspection permissions.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Roles</h3>
                <button type="button" wire:click="openCreateRole" class="text-sm font-medium text-emerald-600 hover:underline">Add role</button>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Slug</th>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($roles as $role)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $role->name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $role->slug }}</td>
                                <td class="px-3 py-2">{{ $role->permissionCount }}</td>
                                <td class="px-3 py-2">
                                    <button type="button" wire:click="openEditRole({{ $role->id }})" class="text-emerald-600 hover:underline">Edit</button>
                                    <button type="button" wire:click="deleteRole({{ $role->id }})" wire:confirm="{{ __('Delete this role?') }}" class="ml-2 text-red-600 hover:underline">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @if ($showRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="closeRoleModal">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">{{ $editingRoleId ? __('Edit role') : __('New role') }}</h3>
                <form wire:submit="saveRole" class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Slug (optional)</label>
                        <input type="text" wire:model="roleSlug" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm" />
                        @error('roleSlug') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Permissions</label>
                        <div class="mt-2 max-h-64 space-y-4 overflow-y-auto rounded-lg border border-gray-100 p-3">
                            @foreach (App\Models\Role::PERMISSION_GROUPS as $group => $actions)
                                <div>
                                    <p class="mb-1 text-xs font-semibold uppercase text-gray-500">{{ str_replace('_permission', '', $group) }}</p>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($actions as $action)
                                            <label class="flex items-center gap-1 text-sm">
                                                <input type="checkbox" value="{{ $action }}" wire:model="permissionGroups.{{ $group }}" class="rounded border-gray-300" />
                                                <span class="font-mono text-xs">{{ $action }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Save</button>
                        <button type="button" wire:click="closeRoleModal" class="rounded-lg border border-gray-200 px-4 py-2 text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-admin.page>
