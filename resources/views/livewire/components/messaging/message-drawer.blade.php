<div>
    @if ($isOpen)
        <!-- GLOBAL OVERLAY -->
        <div wire:click="closeDrawer" class="fixed inset-0 bg-slate-950/40 z-[90] transition backdrop-blur-2xs"></div>

        <!-- MESSAGE DRAWER -->
        <aside id="messageDrawer" class="drawer fixed top-0 right-0 bottom-0 h-screen w-[420px] max-w-[92vw] bg-white z-[92] shadow-2xl border-l border-slate-200 flex flex-col open">
            <div class="h-[76px] px-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <div class="font-extrabold text-lg text-slate-900">Messages</div>
                    <div class="text-[11px] text-slate-400">
                        {{ $unreadCount }} unread {{ \Illuminate\Support\Str::plural('conversation', $unreadCount) }}
                    </div>
                </div>
                <button wire:click="closeDrawer" class="w-9 h-9 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 text-xl font-bold cursor-pointer">×</button>
            </div>

            <!-- SEARCH BAR -->
            <div class="p-4 border-b border-slate-100 bg-white shrink-0">
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400"><i class="fas fa-search text-xs"></i></span>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 outline-none text-sm focus:border-pp-500 transition"
                           placeholder="Search conversations...">
                </div>
            </div>

            <!-- CONVERSATIONS LIST -->
            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar bg-white divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    <button wire:click="openConversation({{ $conv['id'] }})"
                            class="w-full text-left p-4 flex gap-3 hover:bg-slate-50 {{ $conv['unread'] ? 'bg-pp-50/40' : '' }} transition cursor-pointer">
                        <span class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center shrink-0">
                            {{ $conv['avatar'] }}
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="flex justify-between gap-2">
                                <span class="font-bold text-sm text-slate-900 truncate">{{ $conv['peer_name'] }}</span>
                                <span class="text-[10px] text-slate-400 shrink-0">{{ $conv['time'] }}</span>
                            </span>
                            <span class="block text-xs text-slate-500 mt-0.5 truncate">{{ $conv['title'] }}</span>
                            <span class="block text-xs {{ $conv['unread'] ? 'font-bold text-slate-900' : 'text-slate-600' }} mt-1 truncate">
                                {{ $conv['snippet'] }}
                            </span>
                        </span>
                        @if($conv['unread'])
                            <span class="w-2.5 h-2.5 rounded-full bg-pp-600 mt-2 shrink-0"></span>
                        @endif
                    </button>
                @empty
                    <div class="p-10 text-center text-slate-400 text-xs">
                        <i class="far fa-comments text-2xl text-slate-300 block mb-2"></i>
                        No conversations found.
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-100 bg-white shrink-0">
                <a href="{{ route('messages') }}" class="block w-full text-center py-3 rounded-xl bg-pp-600 text-white font-bold text-sm hover:bg-pp-700 transition">
                    Open Messages Page
                </a>
            </div>
        </aside>
    @endif
</div>