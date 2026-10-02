<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <a href="{{ route('admin.blog') }}" class="hover:text-pp-600 transition">Blog</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Comments Moderation</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Post Comments Moderation
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Review, approve, or reject comments submitted to blog articles. Approving a comment notifies all subscribed users.
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.blog') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs transition flex items-center gap-2"
            >
                <i class="fas fa-newspaper text-pp-600"></i>
                <span>All Articles</span>
            </a>
            <a
                href="{{ route('admin.blog.create') }}"
                class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2"
            >
                <i class="fas fa-plus"></i>
                <span>Create Post</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- TABS / FILTERS -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-px">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button
                type="button"
                wire:click="$set('status', 'pending')"
                class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $status === 'pending' ? 'border-pp-600 text-pp-700 bg-white dark:bg-slate-900 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900' }}"
            >
                <i class="fas fa-clock text-amber-500"></i>
                <span>Pending Approval</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $pendingCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="$set('status', 'approved')"
                class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $status === 'approved' ? 'border-pp-600 text-pp-700 bg-white dark:bg-slate-900 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900' }}"
            >
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span>Approved</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $approvedCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="$set('status', 'rejected')"
                class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $status === 'rejected' ? 'border-pp-600 text-pp-700 bg-white dark:bg-slate-900 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900' }}"
            >
                <i class="fas fa-times-circle text-rose-500"></i>
                <span>Rejected</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $rejectedCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="$set('status', 'all')"
                class="px-4 py-2.5 text-xs font-extrabold flex items-center gap-2 whitespace-nowrap transition border-b-2 cursor-pointer {{ $status === 'all' ? 'border-pp-600 text-pp-700 bg-white dark:bg-slate-900 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-900' }}"
            >
                <i class="fas fa-list text-slate-400"></i>
                <span>All Comments</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600">
                    {{ $totalCount }}
                </span>
            </button>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search comments, authors, post..."
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500"
                />
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select
                wire:model.live="postId"
                class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500"
            >
                <option value="">All Articles</option>
                @foreach ($posts as $p)
                    <option value="{{ $p->id }}">{{ Str::limit($p->title, 35) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- COMMENTS LIST / TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-soft">
        @if ($comments->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($comments as $comment)
                    @php
                        $cStatus = $comment->status;
                        $post = $comment->post;
                    @endphp
                    <div class="p-6 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-pp-100 text-pp-700 font-black text-xs grid place-items-center shrink-0">
                                    {{ strtoupper(substr($comment->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $comment->name }}</h4>
                                        <span class="text-xs text-slate-400 font-medium">&lt;{{ $comment->email }}&gt;</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">
                                        Submitted {{ $comment->created_at->diffForHumans() }} ({{ $comment->created_at->format('M d, Y H:i') }})
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($cStatus === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 flex items-center gap-1">
                                        <i class="fas fa-clock"></i> Pending Review
                                    </span>
                                @elseif ($cStatus === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </span>
                                @elseif ($cStatus === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 flex items-center gap-1">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- TARGET ARTICLE -->
                        @if ($post)
                            <div class="text-xs font-bold text-pp-600 flex items-center gap-1.5">
                                <i class="fas fa-newspaper text-slate-400 text-xs"></i>
                                <span>Article:</span>
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="hover:underline">
                                    {{ $post->title }}
                                </a>
                            </div>
                        @endif

                        <!-- COMMENT BODY -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                            {{ $comment->comment }}
                        </div>

                        <!-- ACTIONS -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <div class="text-[11px] text-slate-400">
                                @if ($comment->latestModeration && $comment->latestModeration->moderator)
                                    <span>Moderated by {{ $comment->latestModeration->moderator->name }}</span>
                                @endif
                                @if ($comment->latestModeration && $comment->latestModeration->reason)
                                    <span class="text-rose-500 font-semibold ml-2">Reason: {{ $comment->latestModeration->reason }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($cStatus !== 'approved')
                                    <button
                                        type="button"
                                        wire:click="approveComment({{ $comment->id }})"
                                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition cursor-pointer flex items-center gap-1 shadow-2xs"
                                    >
                                        <i class="fas fa-check"></i>
                                        <span>Approve &amp; Notify Watchers</span>
                                    </button>
                                @endif

                                @if ($cStatus !== 'rejected')
                                    <button
                                        type="button"
                                        wire:click="rejectComment({{ $comment->id }})"
                                        class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-300 font-bold text-xs transition cursor-pointer flex items-center gap-1"
                                    >
                                        <i class="fas fa-ban text-rose-500"></i>
                                        <span>Reject</span>
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    wire:click="deleteComment({{ $comment->id }})"
                                    wire:confirm="Permanently delete this comment?"
                                    class="p-1.5 px-2.5 rounded-xl hover:bg-rose-50 text-rose-600 font-bold text-xs transition cursor-pointer"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $comments->links() }}
            </div>
        @else
            <div class="p-12 text-center space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 grid place-items-center text-xl">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">No Comments Found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        There are currently no comments matching the selected status or filter.
                    </p>
                </div>
            </div>
        @endif
    </div>

</div>
