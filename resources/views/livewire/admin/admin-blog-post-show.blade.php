<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <a href="{{ route('admin.blog') }}" class="hover:text-pp-600 transition">Blog</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">View Article</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                {{ $post->title }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Authored by {{ $post->user?->name ?? 'System' }} · Category: {{ $post->category?->name ?? 'Uncategorized' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <a
                href="{{ route('blog.show', $post->slug) }}"
                target="_blank"
                class="px-3.5 py-2 rounded-xl bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition flex items-center gap-1.5 shadow-2xs"
            >
                <i class="fas fa-external-link-alt"></i>
                <span>Public Post ↗</span>
            </a>

            <a
                href="{{ route('admin.blog.edit', $post) }}"
                class="px-3.5 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-1.5"
            >
                <i class="fas fa-pencil-alt"></i>
                <span>Edit</span>
            </a>

            <button
                type="button"
                wire:click="togglePublish"
                class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition cursor-pointer"
            >
                @if ($post->status === 'published')
                    <i class="fas fa-eye-slash text-amber-500 mr-1"></i> Unpublish
                @else
                    <i class="fas fa-check text-emerald-500 mr-1"></i> Publish Now
                @endif
            </button>

            <button
                type="button"
                wire:click="delete"
                wire:confirm="Permanently delete this article?"
                class="px-3.5 py-2 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 font-extrabold text-xs transition cursor-pointer"
            >
                <i class="fas fa-trash-alt"></i>
            </button>

            <a
                href="{{ route('admin.blog') }}"
                class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition"
            >
                Back
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- STATS PILLS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Views</span>
            <div class="text-xl font-black text-slate-900 dark:text-white mt-1 flex items-center gap-1.5">
                <i class="fas fa-eye text-pp-600 text-base"></i>
                <span>{{ $viewsCount }}</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Watchers / Subscribed</span>
            <div class="text-xl font-black text-slate-900 dark:text-white mt-1 flex items-center gap-1.5">
                <i class="fas fa-bell text-amber-500 text-base"></i>
                <span>{{ $watchersCount }}</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Comments</span>
            <div class="text-xl font-black text-slate-900 dark:text-white mt-1 flex items-center gap-1.5">
                <i class="fas fa-comments text-blue-500 text-base"></i>
                <span>{{ $comments->count() }}</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Publication Status</span>
            <div class="mt-1">
                @if ($post->status === 'published')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800">
                        Published
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase bg-slate-100 text-slate-700">
                        {{ ucfirst($post->status) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN ARTICLE BODY & SIDEBAR -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT: CONTENT -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 space-y-6 shadow-soft">
                
                <!-- FEATURED VIDEO -->
                @if ($featuredVideo)
                    <div class="rounded-2xl overflow-hidden bg-black aspect-16/9 shadow-inner">
                        <video src="{{ $featuredVideo }}" controls class="w-full h-full object-contain"></video>
                    </div>
                @elseif ($featuredImage)
                    <div class="rounded-2xl overflow-hidden bg-slate-100 aspect-16/9 border border-slate-100">
                        <img src="{{ $featuredImage }}" alt="" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- EXCERPT -->
                @if ($post->excerpt)
                    <div class="p-4 rounded-2xl bg-pp-50/60 border border-pp-100 text-xs font-bold text-pp-900 leading-relaxed italic">
                        "{{ $post->excerpt }}"
                    </div>
                @endif

                <!-- RICH ARTICLE CONTENT -->
                <div class="prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 text-sm leading-relaxed">
                    {!! $post->content !!}
                </div>

                <!-- TAGS -->
                @if (count($post->tags_list) > 0)
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-400 mr-1">Tags:</span>
                        @foreach ($post->tags_list as $tag)
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[11px]">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

            </div>

            <!-- COMMENTS MODERATION CARD -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-5 shadow-soft">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-comments text-pp-600"></i>
                        <span>Comments on this Article ({{ $comments->count() }})</span>
                    </h3>

                    <a href="{{ route('admin.blog.comments', ['postId' => $post->id]) }}" class="text-xs font-bold text-pp-600 hover:underline">
                        Open In Moderation Console →
                    </a>
                </div>

                @if ($comments->count() > 0)
                    <div class="space-y-3">
                        @foreach ($comments as $comment)
                            @php $st = $comment->status; @endphp
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <b class="text-slate-900 dark:text-white font-extrabold">{{ $comment->name }}</b>
                                        <span class="text-[11px] text-slate-400 ml-1">({{ $comment->email }})</span>
                                        <span class="text-[10px] text-slate-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if ($st === 'approved')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                            Approved
                                        </span>
                                    @elseif ($st === 'rejected')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                                    {{ $comment->comment }}
                                </p>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    @if ($st !== 'approved')
                                        <button
                                            type="button"
                                            wire:click="approveComment({{ $comment->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] transition cursor-pointer"
                                        >
                                            <i class="fas fa-check mr-1"></i> Approve &amp; Notify
                                        </button>
                                    @endif

                                    @if ($st !== 'rejected')
                                        <button
                                            type="button"
                                            wire:click="rejectComment({{ $comment->id }})"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition cursor-pointer"
                                        >
                                            Reject
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        wire:click="deleteComment({{ $comment->id }})"
                                        wire:confirm="Delete this comment?"
                                        class="p-1 px-2 rounded-lg text-rose-600 hover:bg-rose-50 font-bold text-[11px] transition cursor-pointer"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 text-xs">
                        No comments submitted to this article yet.
                    </div>
                @endif
            </div>

        </div>

        <!-- RIGHT: METADATA & SIDEBAR -->
        <div class="lg:col-span-4 space-y-6 text-xs">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                <h3 class="font-extrabold text-slate-900 dark:text-white uppercase tracking-wider text-xs border-b border-slate-100 dark:border-slate-800 pb-3">
                    Article Metadata
                </h3>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-slate-400 font-bold">Category</dt>
                        <dd class="font-extrabold text-slate-800 dark:text-slate-200">{{ $post->category?->name ?? 'None' }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-slate-400 font-bold">Slug URL</dt>
                        <dd class="font-mono text-slate-600 dark:text-slate-400 truncate max-w-[160px]">{{ $post->slug }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-slate-400 font-bold">Author</dt>
                        <dd class="font-extrabold text-slate-800 dark:text-slate-200">{{ $post->user?->name ?? 'System' }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-slate-400 font-bold">Created</dt>
                        <dd class="font-bold text-slate-600 dark:text-slate-400">{{ $post->created_at->format('M d, Y H:i') }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-slate-400 font-bold">Published</dt>
                        <dd class="font-bold text-slate-600 dark:text-slate-400">
                            {{ $post->published_at ? $post->published_at->format('M d, Y H:i') : 'Not Published' }}
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>

</div>
