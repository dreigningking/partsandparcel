<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">Content</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Blog Articles</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Blog &amp; Knowledge Base
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Publish articles, maintenance guides, repair breakdowns, and inspect user comment discussions.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 self-start sm:self-auto">
            <a
                href="{{ route('admin.blog.comments') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs transition flex items-center gap-2 shadow-2xs"
            >
                <i class="fas fa-comments text-amber-500"></i>
                <span>Comments</span>
                @if ($pendingCommentsCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white font-black text-[10px]">
                        {{ $pendingCommentsCount }}
                    </span>
                @endif
            </a>

            <a
                href="{{ route('blog.index') }}"
                target="_blank"
                class="px-4 py-2.5 rounded-xl border border-pp-200 bg-pp-50 hover:bg-pp-100 text-pp-700 font-extrabold text-xs transition flex items-center gap-2 shadow-2xs"
            >
                <i class="fas fa-external-link-alt text-pp-600"></i>
                <span>Public Blog ↗</span>
            </a>

            <a
                href="{{ route('admin.blog.create') }}"
                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2"
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

    <!-- KPI STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</span>
                <span class="w-8 h-8 rounded-xl bg-pp-100 dark:bg-pp-900/60 text-pp-700 dark:text-pp-300 grid place-items-center text-xs"><i class="fas fa-layer-group"></i></span>
            </div>
            <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $totalPosts }}</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Blog Posts</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 grid place-items-center text-xs"><i class="fas fa-newspaper"></i></span>
            </div>
            <div class="mt-2 text-2xl font-black text-indigo-600">{{ $blogPostsCount }}</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Help Guides</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 grid place-items-center text-xs"><i class="fas fa-circle-question"></i></span>
            </div>
            <div class="mt-2 text-2xl font-black text-amber-600">{{ $helpPostsCount }}</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Published</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 grid place-items-center text-xs"><i class="fas fa-check"></i></span>
            </div>
            <div class="mt-2 text-2xl font-black text-emerald-600">{{ $publishedPosts }}</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pending Comments</span>
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 grid place-items-center text-xs"><i class="fas fa-comments"></i></span>
            </div>
            <div class="mt-2 text-2xl font-black text-rose-600">{{ $pendingCommentsCount }}</div>
        </div>
    </div>

    <!-- SEARCH & FILTERS -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by title, excerpt, tag..."
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500"
                />
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select wire:model.live="type" class="w-full p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500 cursor-pointer">
                <option value="">All Types (Blog &amp; Help)</option>
                <option value="blog">Blog Articles Only</option>
                <option value="help">Help Center Guides Only</option>
            </select>

            <select wire:model.live="category" class="w-full p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500 cursor-pointer">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="status" class="w-full p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-900 dark:text-white outline-none focus:border-pp-500 cursor-pointer">
                <option value="">All Statuses</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- POSTS TABLE -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-[11px] font-extrabold uppercase text-slate-400">
                    <tr>
                        <th class="px-5 py-4">Article</th>
                        <th class="px-4 py-4">Category</th>
                        <th class="px-4 py-4">Author</th>
                        <th class="px-4 py-4">Comments</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">Date</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse ($posts as $post)
                        @php
                            $featuredImg = $post->featured_image_url;
                            $postTags = $post->tags_list;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/80">
                                        @if ($featuredImg)
                                            <img src="{{ $featuredImg }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full grid place-items-center text-slate-400 text-base">
                                                <i class="fas fa-newspaper"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-sm">
                                        <a href="{{ route('admin.blog.show', $post) }}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-pp-600 transition block truncate" title="{{ $post->title }}">
                                            {{ $post->title }}
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-1 overflow-hidden">
                                            @foreach (array_slice($postTags, 0, 3) as $tag)
                                                <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold">
                                                    #{{ $tag }}
                                                </span>
                                            @endforeach
                                            @if (count($postTags) > 3)
                                                <span class="text-[10px] text-slate-400">+{{ count($postTags) - 3 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if ($post->is_help)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        <i class="fas fa-circle-question text-[10px]"></i>
                                        <span>{{ $post->help_topic ?? 'Help' }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-pp-50 text-pp-700 border border-pp-100">
                                        {{ $post->category?->name ?? 'Uncategorized' }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-slate-800 dark:text-slate-200 font-bold">
                                {{ $post->user?->name ?? 'System' }}
                            </td>

                            <td class="px-4 py-4">
                                <a href="{{ route('admin.blog.comments', ['postId' => $post->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition">
                                    <i class="fas fa-comment text-slate-400 text-[11px]"></i>
                                    <span>{{ $post->comments_count }}</span>
                                </a>
                            </td>

                            <td class="px-4 py-4">
                                @if ($post->status === 'published')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                        Published
                                    </span>
                                @elseif ($post->status === 'draft')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-700">
                                        Draft
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800">
                                        {{ $post->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-slate-400 text-[11px]">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.blog.show', $post) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition" title="Preview Article">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.edit', $post) }}" class="p-2 rounded-lg hover:bg-pp-50 text-pp-600 hover:text-pp-700 transition" title="Edit Article">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="Are you sure you want to delete this post?"
                                        class="p-2 rounded-lg hover:bg-rose-50 text-rose-500 hover:text-rose-700 transition cursor-pointer"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 text-slate-400 grid place-items-center text-xl mb-2">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <span class="font-extrabold text-slate-900 dark:text-white block text-sm">No blog posts found</span>
                                <span class="text-xs text-slate-500">Create your first article or adjust filters.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $posts->links() }}
        </div>
    </div>

</div>
