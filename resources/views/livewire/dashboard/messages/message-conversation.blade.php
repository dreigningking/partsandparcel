<div class="space-y-8">
  <div class="bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden flex flex-col h-[calc(100vh-140px)] min-h-[680px]">
    
    <!-- CHAT HEADER BAR -->
    <div class="h-16 px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
      <div class="flex items-center gap-3 min-w-0">
        <!-- BACK TO MESSAGES INBOX BUTTON -->
        <a href="{{ route('messages') }}" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5 font-extrabold text-xs">
          <i class="fas fa-arrow-left"></i>
          <span>Inbox</span>
        </a>

        <div class="relative shrink-0">
          @if($isSupport)
            <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-base shadow-2xs">🎧</span>
          @else
            <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-sm shadow-2xs">
              {{ $avatarLetter }}
            </span>
          @endif
        </div>

        <div class="min-w-0">
          <div class="flex items-center gap-1.5">
            <h3 class="font-extrabold text-sm text-slate-950 truncate">{{ $otherPartyName }}</h3>
            @if($isSupport)
              <span class="px-1.5 py-0.2 rounded bg-pp-100 text-pp-800 text-[9px] font-extrabold uppercase">OFFICIAL SUPPORT</span>
            @endif
          </div>
          <p class="text-[11px] text-slate-500 truncate">{{ $contextTitle }}</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        @if($conversation && $conversation->contextable instanceof \App\Models\Listing)
          <a href="{{ route('listing-details', $conversation->contextable->id) }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-bold text-xs transition">
            <i class="fas fa-box text-pp-600"></i> View Listing
          </a>
        @elseif($conversation && $conversation->contextable instanceof \App\Models\Discussion)
          <a href="{{ route('community.request', $conversation->contextable->id) }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-pp-300 text-slate-700 font-bold text-xs transition">
            <i class="fas fa-comments text-pp-600"></i> View Request
          </a>
        @endif
      </div>
    </div>

    <!-- ITEM CONTEXT BANNER -->
    @if($isSupport)
      <div class="px-4 sm:px-6 py-2.5 bg-pp-50 border-b border-pp-100 flex items-center justify-between gap-3 text-xs shrink-0">
        <div class="flex items-center gap-2 text-slate-700 font-medium">
          <span class="text-base">🛡️</span>
          <span>Official Customer Support. Our support specialists are here to assist with escrow, disputes, or requests.</span>
        </div>
      </div>
    @elseif($conversation && $conversation->contextable)
      <div class="px-4 sm:px-6 py-2.5 bg-pp-50/80 border-b border-pp-100 flex items-center justify-between gap-3 text-xs shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
          <span class="text-xl">💬</span>
          <div class="truncate">
            <span class="font-extrabold text-slate-900">{{ $conversation->contextable->title ?? 'Item Inquiry' }}</span>
          </div>
        </div>
      </div>
    @endif

    <!-- CHAT MESSAGES STREAM (ARRANGED IN ORDER OF ENTRY) -->
    <div class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/50 custom-scrollbar">
      @forelse($messages as $msg)
        @php
          $isMe = ($msg['sender'] === 'me');
        @endphp
        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
          <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
            <div class="p-3.5 text-xs shadow-2xs leading-relaxed rounded-2xl {{ $isMe ? 'bg-pp-600 text-white rounded-tr-xs' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-xs' }}">
              @if(! $isMe)
                <div class="text-[10px] font-bold text-slate-400 mb-1">
                  {{ $msg['sender_name'] ?? $otherPartyName }}
                </div>
              @endif
              <div class="whitespace-pre-line">{{ $msg['body'] ?? $msg['text'] ?? '' }}</div>
            </div>
            <span class="text-[10px] text-slate-400 block px-1 {{ $isMe ? 'text-right' : 'text-left' }}">
              {{ $msg['time'] ?? 'Just now' }}
            </span>
          </div>
        </div>
      @empty
        <div class="p-12 text-center text-slate-400 text-xs">
          <i class="far fa-comments text-3xl text-slate-300 block mb-2"></i>
          No messages yet. Send a message to start this conversation!
        </div>
      @endforelse
    </div>

    <!-- CHAT INPUT FOOTER WITH ATTACHMENT ICON ON THE LEFT -->
    <div class="p-4 border-t border-slate-200 bg-white shrink-0">
      <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
        <!-- MESSAGE TEXT INPUT -->
        <input
          type="text"
          wire:model="messageText"
          placeholder="Write a message to {{ $otherPartyName }}..."
          class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-pp-500 focus:bg-white transition"
          autocomplete="off"
        />

        <!-- SEND BUTTON -->
        <button
          type="submit"
          class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs"
        >
          <i class="fas fa-paper-plane text-xs"></i>
          <span class="hidden sm:inline">Send</span>
        </button>
      </form>
    </div>

  </div>
</div>