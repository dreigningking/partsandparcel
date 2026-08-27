<div wire:ignore>
    <!-- GLOBAL OVERLAY -->
    <div id="globalOverlay" class="overlay fixed inset-0 bg-slate-950/40 z-[60]" onclick="closeAll()"></div>

    <!-- MESSAGE DRAWER -->
    <aside id="messageDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-[420px] max-w-[92vw] bg-white z-[90] shadow-2xl border-l border-slate-200 flex flex-col">
        <div class="h-[76px] px-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
            <div>
                <div class="font-extrabold text-lg text-slate-900">Messages</div>
                <div class="text-[11px] text-slate-400">2 unread conversations</div>
            </div>
            <button onclick="closeMessages()" class="w-9 h-9 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-xl font-bold cursor-pointer">×</button>
        </div>
        <div class="p-4 border-b border-slate-100 bg-white shrink-0">
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-slate-400"><i class="fas fa-search text-xs"></i></span>
                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 outline-none text-sm focus:border-pp-500 transition" placeholder="Search messages">
            </div>
        </div>
        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar bg-white">
            <button wire:click="openConversation('adam')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 bg-pp-50/40 transition cursor-pointer">
                <span class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center shrink-0">A</span>
                <span class="flex-1 min-w-0">
                    <span class="flex justify-between gap-2">
                        <span class="font-bold text-sm text-slate-900">Adam</span>
                        <span class="text-[10px] text-slate-400">5m</span>
                    </span>
                    <span class="block text-xs text-slate-500 mt-1 truncate">HP EliteBook 840 G5</span>
                    <span class="block text-xs font-semibold text-slate-800 mt-1 truncate">I can accept ₦480,000 if...</span>
                </span>
                <span class="w-2.5 h-2.5 rounded-full bg-pp-600 mt-2 shrink-0"></span>
            </button>
            <button wire:click="openConversation('abel')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 transition cursor-pointer">
                <span class="w-11 h-11 rounded-full bg-amber-100 text-amber-800 font-extrabold grid place-items-center shrink-0">A</span>
                <span class="flex-1 min-w-0">
                    <span class="flex justify-between gap-2">
                        <span class="font-bold text-sm text-slate-900">Abel</span>
                        <span class="text-[10px] text-slate-400">24m</span>
                    </span>
                    <span class="block text-xs text-slate-500 mt-1 truncate">Dell Latitude motherboard</span>
                    <span class="block text-xs text-slate-600 mt-1 truncate">Can you confirm the processor...</span>
                </span>
            </button>
            <button wire:click="openConversation('seth')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 transition cursor-pointer">
                <span class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-extrabold grid place-items-center shrink-0">S</span>
                <span class="flex-1 min-w-0">
                    <span class="flex justify-between gap-2">
                        <span class="font-bold text-sm text-slate-900">Seth</span>
                        <span class="text-[10px] text-slate-400">1h</span>
                    </span>
                    <span class="block text-xs text-slate-500 mt-1 truncate">HP EliteBook Battery</span>
                    <span class="block text-xs text-slate-600 mt-1 truncate">Yes, I have 3 available.</span>
                </span>
            </button>
        </div>
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
            <a href="{{ route('messages') }}" class="block w-full text-center py-3 rounded-xl bg-pp-600 text-white font-bold text-sm hover:bg-pp-700 transition">Open Messages Page</a>
        </div>
    </asid>
</div>