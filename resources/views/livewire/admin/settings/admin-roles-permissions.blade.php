<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">System Settings</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Roles &amp; Permissions</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Roles &amp; Permissions
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Define administrative access levels and assign discrete permission capabilities to staff roles.
            </p>
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <button
                type="button"
                wire:click="openCreateRole"
                class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-plus"></i>
                <span>Add New Role</span>
            </button>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
            <span class="text-2xs text-emerald-600 dark:text-emerald-400 uppercase tracking-widest font-extrabold">Success</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-pp-50 dark:bg-pp-900/30 text-pp-600 dark:text-pp-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Total Roles</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $roles->count() }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Active Roles</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $roles->where('is_active', true)->count() }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Available Permissions</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ count($availablePermissions) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Staff Assigned</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $roles->sum('users_count') }}</h3>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search roles by name or slug..."
                class="w-full pl-8 pr-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600"
            />
            @if ($search !== '')
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                >
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <a href="{{ route('admin.settings.staff') }}" class="text-xs font-bold text-pp-600 dark:text-pp-400 hover:underline flex items-center gap-1.5 shrink-0">
            <i class="fas fa-user-shield"></i>
            <span>View Staff Accounts</span>
        </a>
    </div>

    <!-- ROLES TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider text-3xs">
                    <tr>
                        <th class="px-5 py-3.5">Role Name</th>
                        <th class="px-4 py-3.5">Internal Slug</th>
                        <th class="px-4 py-3.5">Permissions Granted</th>
                        <th class="px-4 py-3.5 text-center">Assigned Staff</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($roles as $role)
                        <tr wire:key="role-row-{{ $role->id }}" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <!-- Role Name & Description -->
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-pp-50 dark:bg-pp-900/30 text-pp-600 dark:text-pp-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                                        <i class="fas fa-id-badge"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">
                                            {{ $role->name }}
                                        </h4>
                                        @if ($role->description)
                                            <p class="text-3xs text-slate-400 dark:text-slate-400 mt-0.5 max-w-sm line-clamp-1">
                                                {{ $role->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Slug Monospace -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono text-3xs font-bold">
                                    {{ $role->slug }}
                                </code>
                            </td>

                            <!-- Permissions Overview -->
                            <td class="px-4 py-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-2xs font-extrabold {{ $role->permission_count > 0 ? 'bg-pp-100 dark:bg-pp-950/60 text-pp-700 dark:text-pp-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ $role->permission_count }} / {{ count($availablePermissions) }} capabilities
                                        </span>
                                    </div>

                                    <!-- Key Permissions Preview -->
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        @php
                                            $perms = (array) ($role->permissions ?? []);
                                            $permKeys = array_is_list($perms) ? $perms : array_keys(array_filter($perms));
                                        @endphp
                                        @foreach (array_slice($permKeys, 0, 4) as $pKey)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-3xs font-medium">
                                                {{ $availablePermissions[$pKey]['label'] ?? $pKey }}
                                            </span>
                                        @endforeach
                                        @if (count($permKeys) > 4)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400 text-3xs font-bold">
                                                +{{ count($permKeys) - 4 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Assigned Staff Count -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-2xs font-extrabold {{ $role->users_count > 0 ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                                    <i class="fas fa-user-tie text-3xs"></i>
                                    <span>{{ $role->users_count }} staff</span>
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="toggleRoleStatus({{ $role->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-extrabold cursor-pointer transition {{ $role->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 hover:bg-slate-200' }}"
                                    title="Click to toggle status"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ $role->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $role->is_active ? 'Active' : 'Disabled' }}</span>
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditRole({{ $role->id }})"
                                        class="p-2 rounded-lg text-slate-500 hover:text-pp-600 hover:bg-pp-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Edit Role &amp; Permissions"
                                    >
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    @if ($role->users_count === 0 && $role->slug !== 'super_admin')
                                        <button
                                            type="button"
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="Are you sure you want to permanently delete the '{{ $role->name }}' role?"
                                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                            title="Delete Role"
                                        >
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                    <i class="fas fa-shield-alt text-sm"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No roles found</p>
                                <p class="text-3xs text-slate-400 mt-0.5">Try clearing the search filter or create a new role.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE / EDIT ROLE MODAL -->
    @if ($showRoleModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs overscroll-contain"
            wire:click.self="closeRoleModal"
            wire:key="role-modal-backdrop"
        >
            <div
                class="relative w-full max-w-3xl max-h-[92vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-6"
                role="dialog"
                aria-modal="true"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-pp-50 dark:bg-pp-900/40 text-pp-600 dark:text-pp-400 flex items-center justify-center text-base">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                {{ $editingRoleId ? __('Edit Role: ') . $name : __('Create New Staff Role') }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Customize administrative authority and assign granular permissions.
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeRoleModal"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <form wire:submit="saveRole" class="space-y-5">
                    
                    <!-- Basic Role Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                Role Display Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="e.g. Catalog Supervisor"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-semibold"
                            />
                            @error('name') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                Internal Identifier Slug
                            </label>
                            <input
                                type="text"
                                wire:model="roleSlug"
                                placeholder="e.g. catalog_supervisor (auto-generated if blank)"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-mono"
                            />
                            @error('roleSlug') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Role Description
                        </label>
                        <textarea
                            wire:model="description"
                            rows="2"
                            placeholder="Briefly describe the primary responsibilities and operational scope of this role..."
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600"
                        ></textarea>
                        @error('description') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Role Active Status Switch -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-toggle-on text-emerald-600 text-sm"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Role Status</h4>
                                <p class="text-3xs text-slate-400">Allow assigning this role to staff members</p>
                            </div>
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700"
                            />
                            <span>Active</span>
                        </label>
                    </div>

                    <!-- PERMISSIONS SECTION -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <div>
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Granted Capabilities &amp; Permissions
                                </h4>
                                <p class="text-3xs text-slate-400">
                                    Check each specific ability this role should have across the admin portal.
                                </p>
                            </div>

                            <!-- Bulk Select Tools -->
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-3xs font-extrabold px-2 py-0.5 rounded-full bg-pp-50 text-pp-700 dark:bg-pp-950/60 dark:text-pp-300">
                                    {{ count($selectedPermissions) }} / {{ count($availablePermissions) }} selected
                                </span>
                                <button
                                    type="button"
                                    wire:click="selectAllPermissions"
                                    class="text-3xs font-bold text-pp-600 hover:text-pp-700 dark:text-pp-400 hover:underline cursor-pointer"
                                >
                                    Select All
                                </button>
                                <span class="text-slate-300">|</span>
                                <button
                                    type="button"
                                    wire:click="deselectAllPermissions"
                                    class="text-3xs font-bold text-slate-500 hover:text-rose-600 dark:text-slate-400 hover:underline cursor-pointer"
                                >
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Grouped Permissions List -->
                        <div class="space-y-5 max-h-[380px] overflow-y-auto pr-1">
                            @foreach ($groupedPermissions as $groupName => $groupPermissions)
                                <div class="rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 p-3.5 space-y-2.5">
                                    <!-- Group Header with Group Toggle -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-pp-600"></span>
                                            <h5 class="text-2xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                                {{ $groupName }}
                                            </h5>
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="toggleGroupPermissions('{{ $groupName }}')"
                                            class="text-3xs font-bold text-slate-500 hover:text-pp-600 dark:text-slate-400 transition cursor-pointer"
                                        >
                                            Toggle Group
                                        </button>
                                    </div>

                                    <!-- Permission Cards -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach ($groupPermissions as $permKey => $permDetails)
                                            @php
                                                $isChecked = in_array($permKey, $selectedPermissions, true);
                                            @endphp
                                            <label
                                                class="flex items-start gap-3 p-2.5 rounded-xl border transition cursor-pointer {{ $isChecked ? 'bg-white dark:bg-slate-900 border-pp-500/50 shadow-2xs ring-1 ring-pp-500/30' : 'bg-white/80 dark:bg-slate-900/60 border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300' }}"
                                            >
                                                <input
                                                    type="checkbox"
                                                    value="{{ $permKey }}"
                                                    wire:model.live="selectedPermissions"
                                                    class="mt-1 w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 transition cursor-pointer"
                                                />
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                                            {{ $permDetails['label'] }}
                                                        </span>
                                                        <i class="{{ $permDetails['icon'] ?? 'fas fa-key' }} text-slate-400 text-3xs"></i>
                                                    </div>
                                                    <code class="text-3xs font-mono text-slate-400 block mt-0.5">
                                                        {{ $permKey }}
                                                    </code>
                                                    <p class="text-3xs text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                                                        {{ $permDetails['description'] }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            wire:click="closeRoleModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-check"></i>
                            <span wire:loading.remove wire:target="saveRole">
                                {{ $editingRoleId ? __('Update Role') : __('Create Role') }}
                            </span>
                            <span wire:loading wire:target="saveRole">Saving...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
