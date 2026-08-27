<div>
    <!-- CONVERSATION PANEL INSIDE DRAWER -->
    <div id="conversation" class="drawer fixed top-0 right-0 bottom-0 h-screen w-[420px] max-w-[92vw] bg-white z-[95] shadow-2xl border-l border-slate-200 flex flex-col {{ $isOpen ? 'open' : '' }}">
        <!-- DRAWER HEADER -->
        <div class="h-[76px] px-4 border-b border-slate-100 flex items-center gap-3 shrink-0 bg-white">
            <button wire:click="closeDrawer" class="w-9 h-9 rounded-lg hover:bg-slate-100 grid place-items-center font-bold text-slate-700 cursor-pointer" title="Back to inbox">
                <i class="fas fa-arrow-left"></i>
            </button>
            <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-bold grid place-items-center shrink-0">{{ $avatarLetter }}</span>
            <div class="flex-1 min-w-0">
                <div class="font-extrabold text-slate-900 text-sm truncate">{{ $recipientName }}</div>
                <div class="text-[10px] text-slate-400 truncate">{{ $itemTitle }}</div>
            </div>
            <a href="{{ route('messages') }}" class="text-[11px] font-bold text-pp-600 border border-pp-200 hover:bg-pp-50 rounded-lg px-2.5 py-1.5 transition cursor-pointer flex items-center gap-1">
                Open page <i class="fas fa-external-link-alt text-[9px]"></i>
            </a>
            <button wire:click="closeDrawer" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 font-bold text-lg cursor-pointer">×</button>
        </div>

        <!-- CHAT MESSAGES STREAM -->
        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-4 bg-slate-50">
            @foreach ($messages as $msg)
                @if ($msg['sender'] === 'them')
                    <div class="flex justify-start">
                        <div class="max-w-[78%]">
                            <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-xs px-3.5 py-3 text-sm text-slate-800 shadow-2xs leading-relaxed">
                                {{ $msg['text'] }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1">{{ $msg['time'] }}</div>
                        </div>
                    </div>
                @else
                    <div class="flex justify-end">
                        <div class="max-w-[78%]">
                            <div class="bg-pp-600 text-white rounded-2xl rounded-tr-xs px-3.5 py-3 text-sm shadow-2xs leading-relaxed">
                                {{ $msg['text'] }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1 text-right">{{ $msg['time'] }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- INPUT FOOTER WITH ATTACHMENT ICON ON THE LEFT -->
        <div class="p-3 border-t border-slate-200 bg-white shrink-0">
            <form onsubmit="event.preventDefault();" class="flex items-center gap-2">
                <label class="p-2.5 text-slate-400 hover:text-pp-600 hover:bg-pp-50 rounded-xl transition cursor-pointer shrink-0" title="Attach file">
                    <i class="fas fa-paperclip text-base"></i>
                    <input type="file" class="hidden" />
                </label>
                <input class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-pp-500 transition" placeholder="Write a message...">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-pp-600 text-white font-bold hover:bg-pp-700 transition cursor-pointer flex items-center gap-1.5 shrink-0">
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>