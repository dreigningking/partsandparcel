<div class="min-h-screen bg-slate-50/60 pb-20">

    <!-- HERO HEADER -->
    <div class="relative bg-gradient-to-r from-pp-900 via-pp-800 to-slate-900 text-white pt-14 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden shadow-md">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-[1440px] mx-auto relative z-10 text-center space-y-4">
            <!-- Breadcrumb -->
            <nav class="flex items-center justify-center gap-2 text-xs text-pp-200/80 mb-2">
                <a href="{{ route('welcome') }}" class="hover:text-white transition">Marketplace</a>
                <span>/</span>
                <span class="text-white font-bold">Blog &amp; Knowledge Base</span>
            </nav>

            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                Technical Guides &amp; Insights
            </h1>
            <p class="text-xs sm:text-base text-pp-100 max-w-2xl mx-auto leading-relaxed">
                Step-by-step diagnostic checklists, donor part salvage techniques, automotive maintenance, and hardware repair wisdom.
            </p>

            <!-- SEARCH BAR IN HERO -->
            <div class="max-w-xl mx-auto pt-3">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Search repair guides, ECU diagnostics, gearboxes, batteries..."
                        class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white/95 backdrop-blur text-slate-900 text-xs sm:text-sm font-semibold placeholder:text-slate-400 outline-none shadow-xl border border-white/20 focus:ring-2 focus:ring-pp-400"
                    />
                    <i class="fas fa-search absolute left-4 top-4 text-slate-400 text-sm"></i>
                    @if ($search)
                        <button type="button" wire:click="$set('search', '')" class="absolute right-4 top-3.5 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20 space-y-8">

        <!-- CATEGORIES FILTER TABS BAR -->
        <div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-soft flex items-center gap-2 overflow-x-auto">
            <button
                type="button"
                wire:click="$set('category', null)"
                class="px-4 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition cursor-pointer {{ is_null($category) ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                All Categories
            </button>

            @foreach ($categories as $cat)
                <button
                    type="button"
                    wire:click="$set('category', {{ $cat->id }})"
                    class="px-3.5 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition cursor-pointer flex items-center gap-1.5 {{ $category === $cat->id ? 'bg-pp-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    <span>{{ $cat->name }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $category === $cat->id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $cat->posts_count }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- ACTIVE FILTER CHIPS (IF ANY) -->
        @if ($search || $category || $tag)
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="font-bold text-slate-400">Filtering by:</span>

                @if ($search)
                    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold border border-pp-200 flex items-center gap-1.5">
                        <span>Search: "{{ $search }}"</span>
                        <button type="button" wire:click="$set('search', '')" class="hover:text-pp-900 cursor-pointer"><i class="fas fa-times text-[10px]"></i></button>
                    </span>
                @endif

                @if ($activeCategory)
                    <span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold border border-pp-200 flex items-center gap-1.5">
                        <span>Category: {{ $activeCategory->name }}</span>
                        <button type="button" wire:click="$set('category', null)" class="hover:text-pp-900 cursor-pointer"><i class="fas fa-times text-[10px]"></i></button>
                    </span>
                @endif

                @if ($tag)
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-extrabold border border-emerald-200 flex items-center gap-1.5">
                        <span>Tag: #{{ $tag }}</span>
                        <button type="button" wire:click="$set('tag', '')" class="hover:text-emerald-950 cursor-pointer"><i class="fas fa-times text-[10px]"></i></button>
                    </span>
                @endif

                <button type="button" wire:click="clearFilters" class="text-xs font-bold text-rose-600 hover:underline cursor-pointer ml-1">
                    Reset All Filters
                </button>
            </div>
        @endif

        <!-- FEATURED HERO ARTICLE (IF ON PAGE 1 AND NO ACTIVE FILTERS) -->
        @if ($featuredPost && ! $search && ! $category && ! $tag)
            @php
                $fImg = $featuredPost->featured_image_url ?: asset('images/placeholder-part.png');
                $fVideo = $featuredPost->featured_video_url;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-soft hover:shadow-card transition duration-300">
                <div class="grid md:grid-cols-12 gap-0">
                    <div class="md:col-span-7 relative aspect-16/9 md:aspect-auto bg-slate-100 overflow-hidden">
                        <img src="{{ $fImg }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover">
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-pp-600 text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                            Featured Guide
                        </span>
                        @if ($fVideo)
                            <span class="absolute bottom-4 left-4 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold flex items-center gap-1.5">
                                <i class="fas fa-play text-[10px]"></i> Video Included
                            </span>
                        @endif
                    </div>

                    <div class="md:col-span-5 p-6 sm:p-8 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-pp-50 text-pp-700 border border-pp-100 uppercase">
                                    {{ $featuredPost->category?->name ?? 'Article' }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">
                                    {{ $featuredPost->published_at ? $featuredPost->published_at->format('M d, Y') : $featuredPost->created_at->format('M d, Y') }}
                                </span>
                            </div>

                            <a href="{{ route('blog.show', $featuredPost->slug) }}" class="text-xl sm:text-2xl font-black text-slate-900 hover:text-pp-600 transition block leading-tight">
                                {{ $featuredPost->title }}
                            </a>

                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">
                                {{ $featuredPost->excerpt ?: Str::limit(strip_tags($featuredPost->content), 160) }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-pp-100 text-pp-700 font-black text-xs grid place-items-center">
                                    {{ strtoupper(substr($featuredPost->user?->name ?? 'P', 0, 1)) }}
                                </div>
                                <span class="text-xs font-extrabold text-slate-800">{{ $featuredPost->user?->name ?? 'Parts & Parcel' }}</span>
                            </div>

                            <a href="{{ route('blog.show', $featuredPost->slug) }}" class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-xs">
                                <span>Read Article</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ARTICLES GRID -->
        @if ($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    @php
                        $cardImg = $post->featured_image_url ?: asset('images/placeholder-part.png');
                        $hasVideo = (bool) $post->featured_video_url;
                    @endphp
                    <div class="group bg-white rounded-3xl border border-slate-200 overflow-hidden hover:shadow-card hover:border-pp-300 transition duration-200 flex flex-col justify-between">
                        <div>
                            <!-- THUMBNAIL -->
                            <a href="{{ route('blog.show', $post->slug) }}" class="relative block aspect-16/9 bg-slate-100 overflow-hidden">
                                <img src="{{ $cardImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                
                                <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full bg-white/90 backdrop-blur text-pp-700 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                    {{ $post->category?->name ?? 'Article' }}
                                </span>

                                @if ($hasVideo)
                                    <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold flex items-center gap-1">
                                        <i class="fas fa-play text-[9px]"></i> Video
                                    </span>
                                @endif
                            </a>

                            <!-- BODY -->
                            <div class="p-5 space-y-2.5">
                                <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                                    <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                    <div class="flex items-center gap-3">
                                        <span title="Views"><i class="fas fa-eye mr-1"></i>{{ $post->views_count }}</span>
                                        <span title="Comments"><i class="fas fa-comment mr-1"></i>{{ $post->comments_count }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('blog.show', $post->slug) }}" class="font-black text-sm sm:text-base text-slate-900 group-hover:text-pp-600 transition line-clamp-2 block leading-snug" title="{{ $post->title }}">
                                    {{ $post->title }}
                                </a>

                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                                </p>

                                <!-- TAGS -->
                                @if (count($post->tags_list) > 0)
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach (array_slice($post->tags_list, 0, 3) as $t)
                                            <button type="button" wire:click="$set('tag', '{{ $t }}')" class="text-[10px] font-bold text-slate-400 hover:text-pp-600">
                                                #{{ $t }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="p-5 pt-0 border-t border-slate-100 mt-2 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">{{ $post->user?->name ?? 'Parts & Parcel' }}</span>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-xs font-black text-pp-600 group-hover:translate-x-0.5 transition flex items-center gap-1">
                                <span>Read</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-6">
                {{ $posts->links() }}
            </div>
        @else
            <div class="p-16 text-center bg-white rounded-3xl border border-slate-200 space-y-4 shadow-soft">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 text-slate-400 grid place-items-center text-2xl">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">No Articles Found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        We couldn't find any articles matching your search or filter. Try a different term or clear filters.
                    </p>
                </div>
                <button type="button" wire:click="clearFilters" class="px-5 py-2.5 rounded-xl bg-pp-600 text-white font-extrabold text-xs shadow-xs transition">
                    Clear Filters
                </button>
            </div>
        @endif

    </div>

</div>
