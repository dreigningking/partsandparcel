<!-- GLOBAL OVERLAY -->
<div id="globalOverlay" class="overlay fixed inset-0 bg-slate-950/40 z-[60]" onclick="closeAll()"></div>

<!-- MESSAGE DRAWER -->
<aside id="messageDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-[420px] max-w-[92vw] bg-white z-[90] shadow-2xl border-l border-slate-200 flex flex-col">
    <div class="h-[76px] px-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
        <div>
            <div class="font-extrabold text-lg text-slate-900">Messages</div>
            <div class="text-[11px] text-slate-400">3 unread conversations</div>
        </div>
        <button onclick="closeMessages()" class="w-9 h-9 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-xl font-bold cursor-pointer">×</button>
    </div>
    <div class="p-4 border-b border-slate-100 bg-white shrink-0">
        <div class="relative">
            <span class="absolute left-3 top-2.5 text-slate-400">⌕</span>
            <input class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 outline-none text-sm focus:border-pp-500 transition" placeholder="Search messages">
        </div>
    </div>
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar bg-white">
        <button onclick="openConversation('adam')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 bg-pp-50/40 transition cursor-pointer">
            <span class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center shrink-0">A</span>
            <span class="flex-1 min-w-0">
                <span class="flex justify-between gap-2">
                    <span class="font-bold text-sm text-slate-900">Adam</span>
                    <span class="text-[10px] text-slate-400">5m</span>
                </span>
                <span class="block text-xs text-slate-500 mt-1 truncate">HP EliteBook 840 G5</span>
                <span class="block text-xs font-semibold text-slate-800 mt-1 truncate">I can accept ₦480,000 if...</span>
            </span>
            <span class="w-2 h-2 rounded-full bg-pp-600 mt-2 shrink-0"></span>
        </button>
        <button onclick="openConversation('abel')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 transition cursor-pointer">
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
        <button onclick="openConversation('seth')" class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 border-b border-slate-100 transition cursor-pointer">
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
        <a href="#" class="block w-full text-center py-3 rounded-xl bg-pp-600 text-white font-bold text-sm hover:bg-pp-700 transition">Open Messages Page</a>
    </div>
</aside>

<!-- CONVERSATION PANEL INSIDE DRAWER -->
<div id="conversation" class="drawer fixed top-0 right-0 bottom-0 h-screen w-[420px] max-w-[92vw] bg-white z-[95] shadow-2xl border-l border-slate-200 flex flex-col">
    <div class="h-[76px] px-4 border-b border-slate-100 flex items-center gap-3 shrink-0 bg-white">
        <button onclick="backToInbox()" class="w-9 h-9 rounded-lg hover:bg-slate-100 grid place-items-center font-bold text-slate-700 cursor-pointer">←</button>
        <span id="convAvatar" class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-bold grid place-items-center shrink-0">A</span>
        <div class="flex-1 min-w-0">
            <div id="convName" class="font-extrabold text-slate-900 text-sm truncate">Adam</div>
            <div id="convSubject" class="text-[10px] text-slate-400 truncate">HP EliteBook 840 G5</div>
        </div>
        <button class="text-[11px] font-bold text-pp-600 border border-pp-200 hover:bg-pp-50 rounded-lg px-2.5 py-1.5 transition cursor-pointer">Open page ↗</button>
        <button onclick="closeMessages()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg cursor-pointer">×</button>
    </div>
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-4 bg-slate-50">
        <div class="flex justify-start">
            <div class="max-w-[78%]">
                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-md px-3.5 py-3 text-sm text-slate-800 shadow-xs">I can accept ₦480,000 if you can arrange pickup.</div>
                <div class="text-[10px] text-slate-400 mt-1">2:14 PM</div>
            </div>
        </div>
        <div class="flex justify-end">
            <div class="max-w-[78%]">
                <div class="bg-pp-600 text-white rounded-2xl rounded-tr-md px-3.5 py-3 text-sm shadow-xs">Can I test the laptop before payment?</div>
                <div class="text-[10px] text-slate-400 mt-1 text-right">2:17 PM</div>
            </div>
        </div>
        <div class="flex justify-start">
            <div class="max-w-[78%]">
                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-md px-3.5 py-3 text-sm text-slate-800 shadow-xs">Yes. You can test it at my location before completing the purchase.</div>
                <div class="text-[10px] text-slate-400 mt-1">2:19 PM</div>
            </div>
        </div>
    </div>
    <div class="p-3 border-t border-slate-200 bg-white shrink-0">
        <div class="flex gap-2">
            <input class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-pp-500 transition" placeholder="Write a message...">
            <button class="w-11 rounded-xl bg-pp-600 text-white font-bold hover:bg-pp-700 transition cursor-pointer">↑</button>
        </div>
    </div>
</div>
