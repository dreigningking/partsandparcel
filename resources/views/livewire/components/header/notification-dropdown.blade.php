<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" 
            type="button"
            aria-label="Notifications" 
            class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer" 
            title="Notifications">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold grid place-items-center animate-in zoom-in-50 duration-200">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- NOTIFICATIONS DROPDOWN PANEL -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="absolute right-0 top-12 w-[350px] max-w-[calc(100vw-24px)] bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[80]">
        
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <div class="font-extrabold text-slate-900 text-sm">Notifications</div>
                <div class="text-[11px] text-slate-500">{{ $unreadCount }} unread {{ Str::plural('alert', $unreadCount) }}</div>
            </div>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <button type="button" wire:click="markAllAsRead" class="text-[10px] font-bold text-slate-500 hover:text-slate-800 transition">
                        Mark read
                    </button>
                    <span class="text-slate-300">·</span>
                @endif
                <a href="{{ route('notifications') }}" class="text-xs font-bold text-pp-600 hover:underline">See all</a>
            </div>
        </div>

        <div class="max-h-[360px] overflow-y-auto custom-scrollbar divide-y divide-slate-100 bg-white">
            @forelse($notifications as $rNotif)
                <a href="{{ $rNotif['action_url'] }}" 
                   wire:click="markAsRead('{{ $rNotif['id'] }}')" 
                   class="flex gap-3 p-4 {{ $rNotif['is_unread'] ? 'bg-pp-50/60' : 'hover:bg-slate-50' }} transition">
                    <span class="w-9 h-9 rounded-lg bg-pp-100 text-pp-700 grid place-items-center shrink-0 font-bold text-xs">
                        <i class="{{ $rNotif['icon'] }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ $rNotif['title'] }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $rNotif['message'] }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $rNotif['time_ago'] }}</div>
                    </div>
                    @if($rNotif['is_unread'])
                        <span class="w-2 h-2 rounded-full bg-pp-600 self-center shrink-0"></span>
                    @endif
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        <a href="{{ route('notifications') }}" class="block text-center p-3 text-xs font-bold text-pp-600 hover:bg-slate-50 border-t border-slate-100 bg-white">
            View all notifications →
        </a>
    </div>
</div>
