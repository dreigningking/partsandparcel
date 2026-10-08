<div class="space-y-8">
  <div class="bg-white rounded-3xl border border-slate-200 shadow-soft overflow-hidden flex flex-col lg:flex-row h-[calc(100vh-140px)] min-h-[680px]">
    
    <!-- LEFT PANEL: MESSAGES LIST -->
    <div class="w-full lg:w-[380px] border-r border-slate-200 flex flex-col shrink-0 bg-white {{ $activeConversationId ? 'hidden lg:flex' : 'flex' }}">
      
      <!-- HEADER -->
      <div class="p-4 border-b border-slate-100 space-y-3 shrink-0 bg-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-extrabold text-slate-950">Messages</h1>
            <p class="text-[11px] text-slate-400">
              {{ $unreadTotal > 0 ? "{$unreadTotal} unread message(s)" : "All conversations up to date" }}
            </p>
          </div>
        </div>

        <!-- SEARCH INPUT -->
        <div class="relative">
          <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
          <input type="text" wire:model.live="searchQuery" placeholder="Search conversations..." class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs outline-none focus:border-pp-500 focus:bg-white transition" />
        </div>
      </div>

      <!-- CONVERSATIONS SCROLLABLE LIST -->
      <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
        @forelse($conversations as $conv)
          @php
            $isSupport = $conv->isSupport();
            $title = $isSupport ? 'Customer Support' : ($conv->contextable->name ?? 'Conversation #' . $conv->id);
            $latest = $conv->latestMessage;
            $isSelected = (int) $activeConversationId === (int) $conv->id;
            $unread = $conv->unread_count ?? 0;
          @endphp
          <button
            type="button"
            wire:click="selectConversation({{ $conv->id }})"
            class="w-full text-left p-4 flex gap-3 transition cursor-pointer select-none {{ $isSelected ? 'bg-pp-50/80 border-l-4 border-pp-600' : 'hover:bg-slate-50' }} {{ $unread > 0 ? 'bg-amber-50/20' : '' }}"
          >
            <div class="relative shrink-0">
              @if($isSupport)
                <span class="w-11 h-11 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-base shadow-2xs">🎧</span>
              @else
                <span class="w-11 h-11 rounded-full bg-slate-100 text-slate-700 font-extrabold grid place-items-center text-sm shadow-2xs">
                  {{ strtoupper(substr($title, 0, 1)) }}
                </span>
              @endif

              @if($unread > 0)
                <span class="w-3 h-3 rounded-full bg-rose-500 border-2 border-white absolute bottom-0 right-0"></span>
              @endif
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-1">
                <span class="font-bold text-xs text-slate-900 truncate">{{ $title }}</span>
                @if($latest)
                  <span class="text-[10px] font-semibold text-slate-400 shrink-0">
                    {{ $latest->created_at->diffForHumans(null, true) }}
                  </span>
                @endif
              </div>

              <div class="flex items-center gap-1.5 mt-0.5">
                @if($isSupport)
                  <span class="px-1.5 py-0.2 rounded bg-pp-100 text-pp-800 text-[9px] font-extrabold uppercase">OFFICIAL SUPPORT</span>
                @endif
              </div>

              <p class="text-xs font-semibold text-slate-600 mt-1 truncate">
                @if($latest)
                  @if($latest->sender_id === auth()->id())
                    <span class="text-pp-600">You: </span>
                  @endif
                  {{ $latest->body }}
                @else
                  <span class="italic text-slate-400">No messages yet</span>
                @endif
              </p>
            </div>

            @if($unread > 0)
              <span class="w-2.5 h-2.5 rounded-full bg-pp-600 mt-1.5 shrink-0 self-center"></span>
            @endif
          </button>
        @empty
          <div class="p-8 text-center text-slate-400 space-y-2">
            <span class="text-2xl block">💬</span>
            <p class="text-xs font-bold text-slate-600">No conversations yet</p>
            <p class="text-[11px]">When you receive messages or order updates, they will appear here.</p>
          </div>
        @endforelse
      </div>
    </div>

    <!-- RIGHT PANEL: ACTIVE CONVERSATION WINDOW OR EMPTY PROMPT -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50 {{ $activeConversationId ? 'flex' : 'hidden lg:flex' }}">
      @if ($activeConversation)
        @php
          $isSupport = $activeConversation->isSupport();
          $chatTitle = $isSupport ? 'Customer Support' : ($activeConversation->contextable->name ?? 'Conversation');
        @endphp
        
        <!-- CHAT HEADER BAR -->
        <div class="h-16 px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
          <div class="flex items-center gap-3 min-w-0">
            <!-- MOBILE BACK BUTTON -->
            <button wire:click="clearConversation" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition cursor-pointer" title="Back to Inbox">
              <i class="fas fa-arrow-left text-sm"></i>
            </button>

            <div class="relative shrink-0">
              @if($isSupport)
                <span class="w-10 h-10 rounded-full bg-pp-100 text-pp-700 font-extrabold grid place-items-center text-base shadow-2xs">🎧</span>
              @else
                <span class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-extrabold grid place-items-center text-sm shadow-2xs">
                  {{ strtoupper(substr($chatTitle, 0, 1)) }}
                </span>
              @endif
            </div>

            <div class="min-w-0">
              <div class="flex items-center gap-1.5">
                <h3 class="font-extrabold text-sm text-slate-950 truncate">{{ $chatTitle }}</h3>
                @if($isSupport)
                  <span class="px-1.5 py-0.2 rounded bg-pp-100 text-pp-800 text-[9px] font-extrabold uppercase">OFFICIAL</span>
                @endif
              </div>
              <p class="text-[11px] text-slate-500 truncate">
                {{ $isSupport ? '24/7 Parts & Parcel Helpdesk' : 'Direct conversation' }}
              </p>
            </div>
          </div>
        </div>

        @if($isSupport)
          <!-- SUPPORT HELP BANNER -->
          <div class="px-4 sm:px-6 py-2.5 bg-pp-50 border-b border-pp-100 flex items-center justify-between gap-3 text-xs shrink-0">
            <div class="flex items-center gap-2 text-slate-700 font-medium">
              <span class="text-base">🛡️</span>
              <span>Need help with an escrow invoice, order delivery, or listing? Ask our support team anytime.</span>
            </div>
          </div>
        @endif

        <!-- CHAT MESSAGES STREAM -->
        <div class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scrollbar">
          @forelse($activeMessages as $msg)
            @php
              $isMe = $msg->sender_id === auth()->id();
            @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
              <div class="max-w-[85%] sm:max-w-[70%] space-y-1">
                <div class="p-3.5 text-xs shadow-2xs leading-relaxed rounded-2xl {{ $isMe ? 'bg-pp-600 text-white rounded-tr-xs' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-xs' }}">
                  @if(! $isMe)
                    <div class="text-[10px] font-bold text-slate-400 mb-1">
                      {{ $msg->sender->name ?? 'Support' }}
                    </div>
                  @endif
                  <div class="whitespace-pre-line">{{ $msg->body }}</div>
                </div>
                <span class="text-[10px] text-slate-400 block px-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                  {{ $msg->created_at->format('h:i A') }}
                </span>
              </div>
            </div>
          @empty
            <div class="text-center py-12 text-slate-400">
              <p class="text-xs font-bold">No messages yet in this conversation.</p>
              <p class="text-[11px]">Send a message below to start chatting!</p>
            </div>
          @endforelse
        </div>

        <!-- CHAT INPUT FOOTER -->
        <div class="p-4 border-t border-slate-200 bg-white shrink-0">
          <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
            <!-- MESSAGE TEXT INPUT -->
            <input
              type="text"
              wire:model="messageText"
              placeholder="{{ $isSupport ? 'Write a message to Customer Support...' : 'Write a message...' }}"
              class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-pp-500 focus:bg-white transition"
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
          @error('messageText')
            <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span>
          @enderror
        </div>

      @else
        <!-- BLANK DESKTOP PROMPT WHEN NO CONVERSATION IS SELECTED -->
        <div class="flex-1 grid place-items-center p-8 text-center bg-slate-50/50">
          <div class="max-w-sm space-y-3">
            <div class="w-16 h-16 rounded-3xl bg-pp-50 text-pp-600 grid place-items-center text-2xl mx-auto shadow-2xs">
              <i class="fas fa-comments"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-900">No Conversation Selected</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Select a conversation from the list to view and reply to messages.
            </p>
          </div>
        </div>
      @endif

    </div>

  </div>
</div>