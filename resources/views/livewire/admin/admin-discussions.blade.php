<div class="space-y-6">
    <!-- TOP HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>›</span>
                <span class="text-pp-600 dark:text-pp-400">Marketplace</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2.5">
                <span>Discussions Moderation</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-extrabold bg-pp-100 text-pp-700 dark:bg-pp-900/40 dark:text-pp-300">
                    {{ $totalCount }} Topics
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Moderate community conversations, forum threads, technical Q&amp;A, and merchant discussions.
            </p>
        </div>

        <div class="w-full sm:w-72">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search topic or author..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pp-500"
            >
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- DISCUSSIONS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($discussions as $disc)
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft flex flex-col justify-between hover:border-pp-300 transition">
                <div>
                    <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                        <span class="font-extrabold text-pp-600 dark:text-pp-400">
                            {{ $disc->user?->name ?: 'Member' }}
                        </span>
                        <span>{{ $disc->created_at->diffForHumans() }}</span>
                    </div>

                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white line-clamp-2">
                        {{ $disc->title }}
                    </h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 mt-1.5 leading-relaxed">
                        {{ Str::limit(strip_tags($disc->body), 140) }}
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-bold text-[11px]">
                        💬 {{ $disc->responses->count() }} replies
                    </span>
                    <button
                        wire:click="showDiscussion({{ $disc->id }})"
                        class="px-3 py-1 rounded-lg bg-pp-50 hover:bg-pp-100 text-pp-700 dark:bg-pp-950 dark:text-pp-300 text-xs font-bold transition cursor-pointer"
                    >
                        Moderate
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400 text-xs bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                No discussion threads found.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $discussions->links() }}
    </div>

    <!-- MODAL -->
    @if($selectedDiscussion)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-xl w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-base">
                        {{ $selectedDiscussion->title }}
                    </h3>
                    <button wire:click="closeDiscussion" class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer">✕</button>
                </div>

                <div class="text-xs space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Posted by</span>
                        <p class="font-extrabold text-slate-800 dark:text-white">{{ $selectedDiscussion->user?->name }} ({{ $selectedDiscussion->user?->email }})</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Thread Content</span>
                        <p class="text-slate-700 dark:text-slate-300 mt-1 whitespace-pre-line">{{ $selectedDiscussion->body }}</p>
                    </div>

                    <!-- Replies -->
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 text-xs mb-2">Replies ({{ $selectedDiscussion->responses->count() }})</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @forelse($selectedDiscussion->responses as $reply)
                                <div class="p-2.5 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $reply->user?->name }}</span>
                                        <span>{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $reply->content }}</p>
                                </div>
                            @empty
                                <p class="text-slate-400 text-[11px]">No replies yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            wire:click="deleteDiscussion({{ $selectedDiscussion->id }})"
                            class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition cursor-pointer"
                        >
                            Delete Thread
                        </button>
                        <button
                            wire:click="closeDiscussion"
                            class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
