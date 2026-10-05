<div>
    <button wire:click="openDrawer" aria-label="Messages" class="relative p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition cursor-pointer flex items-center justify-center" title="Messages">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 min-w-4 h-4 px-1 rounded-full bg-pp-600 text-white text-[10px] font-bold grid place-items-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>
</div>
