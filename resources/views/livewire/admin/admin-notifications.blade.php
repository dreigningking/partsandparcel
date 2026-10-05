<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">System</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Notifications Center</span>
                @if($unreadCount > 0)
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                        {{ $unreadCount }} Unread
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Admin alerts, escalation notifications, dispute flags, and platform activity logs.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="px-3 py-1.5 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 text-xs font-bold transition cursor-pointer"
                >
                    Mark All As Read
                </button>
            @endif
            <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
                <button
                    wire:click="setFilter('all')"
                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer {{ $filter === 'all' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400' }}"
                >
                    All ({{ $totalCount }})
                </button>
                <button
                    wire:click="setFilter('unread')"
                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer {{ $filter === 'unread' ? 'bg-white dark:bg-slate-900 text-pp-600 dark:text-pp-400 shadow-2xs' : 'text-slate-600 dark:text-slate-400' }}"
                >
                    Unread ({{ $unreadCount }})
                </button>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- NOTIFICATIONS LIST -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $data = $notif->data ?? [];
                $title = $data['title'] ?? ($data['message'] ?? 'Platform Notification');
                $body = $data['body'] ?? ($data['description'] ?? null);
            @endphp
            <div class="p-4 flex items-start justify-between gap-4 transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40 {{ $isUnread ? 'bg-pp-50/20 dark:bg-pp-950/20' : '' }}">
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-xl {{ $isUnread ? 'bg-pp-100 text-pp-700 dark:bg-pp-900/60' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }} grid place-items-center text-sm shrink-0 mt-0.5">
                        🔔
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $title }}</h4>
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-pp-500"></span>
                            @endif
                        </div>
                        @if($body)
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">{{ $body }}</p>
                        @endif
                        <span class="text-[10px] text-slate-400 mt-1 block">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($isUnread)
                        <button
                            wire:click="markAsRead('{{ $notif->id }}')"
                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold transition cursor-pointer"
                        >
                            Mark Read
                        </button>
                    @endif
                    <button
                        wire:click="deleteNotification('{{ $notif->id }}')"
                        class="p-1 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 text-xs transition cursor-pointer"
                        title="Delete Notification"
                    >
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                No notifications found.
            </div>
        @endforelse
    </div>

    @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
