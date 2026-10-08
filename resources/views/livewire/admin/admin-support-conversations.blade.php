<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-pp-100 text-pp-700 font-bold flex items-center justify-center text-lg shadow-2xs">
                    🎧
                </span>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer Support Desk</h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Manage user inquiry chats, answer onboarding questions, and provide escrow/order assistance.
                    </p>
                </div>
            </div>
        </div>

        @if($totalUnreadCount > 0)
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>{{ $totalUnreadCount }} {{ Str::plural('unread user message', $totalUnreadCount) }}</span>
            </div>
        @endif
    </div>

    <!-- MAIN CHAT CONTAINER -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-soft overflow-hidden flex flex-col lg:flex-row h-[calc(100vh-210px)] min-h-[620px]">
        <!-- LEFT PANEL: CONVERSATION LIST -->
        <div class="w-full lg:w-[380px] border-r border-slate-200 flex flex-col shrink-0 bg-white {{ $selectedConversationId ? 'hidden lg:flex' : 'flex' }}">
            <!-- SEARCH & STATS -->
            <div class="p-4 border-b border-slate-100 space-y-3 shrink-0 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-500">Inquiries ({{ $conversations->count() }})</span>
                    @if($totalUnreadCount > 0)
                        <span class="text-[10px] bg-rose-100 text-rose-700 font-extrabold px-2 py-0.5 rounded-full">
                            {{ $totalUnreadCount }} unread
                        </span>
                    @endif
                </div>

                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 text-xs">🔍</span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search user name or email..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-slate-200 text-xs outline-none focus:border-pp-500 focus:ring-2 focus:ring-pp-100 transition shadow-2xs"
                    />
                </div>
            </div>

            <!-- SCROLLABLE USER CONVERSATIONS -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                @forelse($conversations as $conv)
                    @php
                        $user = $conv->customer_user;
                        $latest = $conv->latestMessage;
                        $isSelected = $selectedConversationId === $conv->id;
                        $unread = $conv->unread_count ?? 0;
                    @endphp
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conv->id }})"
                        class="w-full text-left p-4 flex gap-3 transition cursor-pointer select-none {{ $isSelected ? 'bg-pp-50/80 border-l-4 border-pp-600' : 'hover:bg-slate-50/80' }} {{ $unread > 0 ? 'bg-amber-50/30' : '' }}"
                    >
                        <div class="relative shrink-0">
                            @if($user && $user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-11 h-11 rounded-full object-cover border border-slate-200">
                            @else
                                <span class="w-11 h-11 rounded-full bg-slate-100 text-slate-700 font-extrabold flex items-center justify-center text-sm border border-slate-200">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                            @if($unread > 0)
                                <span class="w-3 h-3 rounded-full bg-rose-500 border-2 border-white absolute bottom-0 right-0"></span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-bold text-xs text-slate-900 truncate">
                                    {{ $user->name ?? 'Customer' }}
                                </span>
                                @if($latest)
                                    <span class="text-[10px] font-semibold text-slate-400 shrink-0">
                                        {{ $latest->created_at->diffForHumans(null, true) }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-400 truncate">
                                {{ $user->email ?? '' }}
                            </p>

                            <p class="text-xs text-slate-600 mt-1 truncate font-medium">
                                @if($latest)
                                    @if($latest->sender_id === auth()->id() || ($latest->sender && $latest->sender->isAdmin()))
                                        <span class="text-pp-600 font-bold">You:</span>
                                    @endif
                                    {{ $latest->body }}
                                @else
                                    <span class="italic text-slate-400">No messages yet</span>
                                @endif
                            </p>
                        </div>

                        @if($unread > 0)
                            <div class="shrink-0 self-center">
                                <span class="text-[10px] bg-rose-600 text-white font-extrabold px-1.5 py-0.5 rounded-full">
                                    {{ $unread }}
                                </span>
                            </div>
                        @endif
                    </button>
                @empty
                    <div class="p-8 text-center text-slate-400 space-y-2">
                        <span class="text-2xl block">💬</span>
                        <p class="text-xs font-bold text-slate-600">No support conversations found</p>
                        <p class="text-[11px]">When users register or reach out for help, their threads will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT PANEL: ACTIVE CONVERSATION THREAD -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50/40 {{ $selectedConversationId ? 'flex' : 'hidden lg:flex' }}">
            @if($activeConversation)
                @php
                    $customer = $activeConversation->customer_user;
                @endphp
                <!-- HEADER BAR -->
                <div class="h-16 px-5 border-b border-slate-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            wire:click="closeConversation"
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                            title="Back to conversation list"
                        >
                            ←
                        </button>

                        @if($customer && $customer->avatar)
                            <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                        @else
                            <span class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-extrabold flex items-center justify-center text-sm border border-slate-200">
                                {{ strtoupper(substr($customer->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-extrabold text-sm text-slate-900 truncate">{{ $customer->name ?? 'Customer' }}</h3>
                                @if($customer && $customer->is_verified)
                                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.2 rounded-md">Verified</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">{{ $customer->email ?? '' }}</p>
                        </div>
                    </div>

                    @if($customer)
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('admin.users.show', $customer->id) }}"
                                class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition shadow-2xs"
                                target="_blank"
                            >
                                View User Profile ↗
                            </a>
                        </div>
                    @endif
                </div>

                <!-- MESSAGES STREAM -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar">
                    <div class="text-center my-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-slate-200/80 text-slate-600 px-3 py-1 rounded-full">
                            Support Ticket Thread Initiated {{ $activeConversation->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    @foreach($messages as $msg)
                        @php
                            $isStaff = $msg->sender && $msg->sender->isAdmin();
                        @endphp
                        <div class="flex flex-col {{ $isStaff ? 'items-end' : 'items-start' }}">
                            <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[70%]">
                                @if(! $isStaff)
                                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($msg->sender->name ?? 'U', 0, 1)) }}
                                    </span>
                                @endif

                                <div class="p-3.5 rounded-2xl text-xs leading-relaxed shadow-2xs {{ $isStaff ? 'bg-pp-600 text-white rounded-br-none' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-none' }}">
                                    <div class="text-[10px] font-bold mb-1 opacity-80 flex items-center justify-between gap-3">
                                        <span>{{ $isStaff ? 'Customer Support Agent' : ($msg->sender->name ?? 'Customer') }}</span>
                                        <span>{{ $msg->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="whitespace-pre-line">{{ $msg->body }}</div>
                                </div>

                                @if($isStaff)
                                    <span class="w-7 h-7 rounded-full bg-pp-100 text-pp-700 font-bold text-[11px] flex items-center justify-center shrink-0">
                                        🎧
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- REPLY COMPOSER -->
                <div class="p-4 bg-white border-t border-slate-200">
                    <form wire:submit.prevent="sendReply" class="space-y-3">
                        <div class="relative">
                            <textarea
                                wire:model="replyText"
                                rows="3"
                                placeholder="Type your response to {{ $customer->name ?? 'this user' }}..."
                                class="w-full p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-900 outline-none focus:border-pp-500 focus:bg-white transition resize-none shadow-2xs"
                            ></textarea>
                            @error('replyText')
                                <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                Message will be sent as <strong>Customer Support</strong>
                            </span>

                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 active:bg-pp-800 text-white font-extrabold text-xs transition shadow-soft flex items-center gap-2 cursor-pointer"
                            >
                                <span>Send Reply</span>
                                <span>➤</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- EMPTY STATE -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 flex items-center justify-center text-3xl mb-4">
                        🎧
                    </div>
                    <h3 class="font-bold text-slate-700 text-sm">Select a support ticket to start responding</h3>
                    <p class="text-xs text-slate-400 max-w-sm mt-1">
                        Choose an active customer conversation from the left panel to review message histories and assist users.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
