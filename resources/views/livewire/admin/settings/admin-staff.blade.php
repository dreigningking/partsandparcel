<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">System Settings</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Staff Management</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Administrative Staff
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage team members, administrative privileges, and staff role assignments.
            </p>
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <button
                type="button"
                wire:click="openCreate"
                class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
            >
                <i class="fas fa-user-plus"></i>
                <span>Invite Staff Member</span>
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

    <!-- OVERVIEW STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-pp-50 dark:bg-pp-900/30 text-pp-600 dark:text-pp-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Total Staff Accounts</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $totalStaffCount }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-id-badge"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Available Roles</p>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $totalRolesCount }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <p class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Your Status</p>
                <h3 class="text-sm font-extrabold text-emerald-700 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ auth()->user()?->role?->name ?? 'Administrator' }}</span>
                </h3>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3 flex-1">
            <!-- Search -->
            <div class="relative flex-1 max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search staff by name or email..."
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

            <!-- Role Filter -->
            <div class="min-w-[160px]">
                <select
                    wire:model.live="roleFilter"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:outline-hidden focus:ring-2 focus:ring-pp-600"
                >
                    <option value="">All Roles</option>
                    @foreach ($adminRoles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <a href="{{ route('admin.settings.roles') }}" class="text-xs font-bold text-pp-600 dark:text-pp-400 hover:underline flex items-center gap-1.5 shrink-0 self-end sm:self-auto">
            <i class="fas fa-shield-alt"></i>
            <span>Manage Roles &amp; Permissions</span>
        </a>
    </div>

    <!-- STAFF TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider text-3xs">
                    <tr>
                        <th class="px-5 py-3.5">Staff Member</th>
                        <th class="px-4 py-3.5">Email Address</th>
                        <th class="px-4 py-3.5">Assigned Role</th>
                        <th class="px-4 py-3.5">Capabilities</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse ($staff as $user)
                        <tr wire:key="staff-row-{{ $user->id }}" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition {{ $user->id === auth()->id() ? 'bg-pp-50/30 dark:bg-pp-950/20' : '' }}">
                            <!-- Staff Name & Avatar -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-pp-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">
                                                {{ $user->name }}
                                            </h4>
                                            @if ($user->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-3xs font-extrabold bg-pp-100 dark:bg-pp-900 text-pp-700 dark:text-pp-300 uppercase">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-3xs text-slate-400">
                                            Added {{ $user->created_at?->format('M d, Y') ?? 'Recently' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $user->email }}
                                </span>
                            </td>

                            <!-- Assigned Role -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($user->role)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-extrabold {{ $user->role->slug === 'super_admin' ? 'bg-pp-100 text-pp-800 dark:bg-pp-950/60 dark:text-pp-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <i class="fas fa-shield-alt text-3xs"></i>
                                        <span>{{ $user->role->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-2xs">No Role Assigned</span>
                                @endif
                            </td>

                            <!-- Capabilities / Permissions -->
                            <td class="px-4 py-4">
                                @if ($user->role)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-2xs font-extrabold text-slate-600 dark:text-slate-400">
                                            {{ $user->role->permission_count }} capabilities
                                        </span>
                                        @if ($user->role->slug === 'super_admin')
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-3xs font-extrabold uppercase">
                                                Full Root
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-2xs font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Active</span>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $user->id }})"
                                        class="p-2 rounded-lg text-slate-500 hover:text-pp-600 hover:bg-pp-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Edit Staff Member"
                                    >
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    @if ($user->id !== auth()->id())
                                        <button
                                            type="button"
                                            wire:click="deleteStaff({{ $user->id }})"
                                            wire:confirm="Are you sure you want to revoke administrative privileges for '{{ $user->name }}'?"
                                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                            title="Revoke Admin Access"
                                        >
                                            <i class="fas fa-user-minus text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                    <i class="fas fa-users-slash text-sm"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No staff members found</p>
                                <p class="text-3xs text-slate-400 mt-0.5">Try clearing the search filter or invite a new staff member.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE / EDIT STAFF MODAL -->
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs overscroll-contain"
            wire:click.self="closeModal"
            wire:key="staff-modal-backdrop"
        >
            <div
                class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-6"
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
                                {{ $editingUserId ? __('Edit Staff: ') . $name : __('Invite New Staff Member') }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $editingUserId ? __('Update role assignment or credentials') : __('Assign an administrative role and send credentials') }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <form wire:submit="saveStaff" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="e.g. Samuel Adeyemi"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-semibold"
                        />
                        @error('name') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="email"
                            wire:model="email"
                            placeholder="staff@partsandparcel.com"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-medium"
                        />
                        @error('email') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Password {{ $editingUserId ? '(leave blank to keep current)' : '' }} <span class="{{ $editingUserId ? 'hidden' : 'text-rose-500' }}">*</span>
                            </label>
                        </div>
                        <input
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            placeholder="{{ $editingUserId ? '••••••••' : 'Minimum 8 characters' }}"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-mono"
                        />
                        @if (! $editingUserId)
                            <p class="text-3xs text-slate-400 mt-1">
                                An invite notification with this temporary password will be queued for the user.
                            </p>
                        @endif
                        @error('password') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Administrative Role <span class="text-rose-500">*</span>
                        </label>
                        <select
                            wire:model="role_id"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-semibold"
                        >
                            @foreach ($adminRoles as $r)
                                <option value="{{ $r->id }}">
                                    {{ $r->name }} ({{ $r->permission_count }} capabilities)
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="mt-1 text-2xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-check"></i>
                            <span wire:loading.remove wire:target="saveStaff">
                                {{ $editingUserId ? __('Update Staff') : __('Save &amp; Invite') }}
                            </span>
                            <span wire:loading wire:target="saveStaff">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
