<div class="min-h-screen bg-slate-50/60 pb-20">

    <!-- ARTICLE HEADER COVER -->
    <div class="relative bg-gradient-to-r from-pp-900 via-pp-800 to-slate-900 text-white pt-12 pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden shadow-md">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-[1440px] mx-auto relative z-10 space-y-4">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-pp-200/80">
                <a href="{{ route('welcome') }}" class="hover:text-white transition">Marketplace</a>
                <span>/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
                <span>/</span>
                <span class="text-white font-bold">{{ $post->category?->name ?? 'Article' }}</span>
            </nav>

            <div class="max-w-4xl space-y-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3 py-0.5 rounded-full bg-pp-500/40 backdrop-blur border border-pp-400/40 text-pp-200 text-xs font-black uppercase tracking-wider">
                        {{ $post->category?->name ?? 'Guide' }}
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-pp-300"></i>
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-eye text-pp-300"></i>
                        {{ $viewsCount }} {{ Str::plural('view', $viewsCount) }}
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-bell text-amber-300"></i>
                        {{ $watchersCount }} {{ Str::plural('subscriber', $watchersCount) }}
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-thumbs-up text-pp-300"></i>
                        {{ $likesCount }} {{ Str::plural('helpful', $likesCount) }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                    {{ $post->title }}
                </h1>

                <div class="flex items-center gap-3 pt-2">
                    <div class="w-9 h-9 rounded-full bg-pp-600 text-white font-black text-xs grid place-items-center border-2 border-white/20">
                        {{ strtoupper(substr($post->user?->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-extrabold text-white">{{ $post->user?->name ?? 'Parts & Parcel' }}</div>
                        <div class="text-[11px] text-pp-200">{{ $post->user?->business_name ?: 'Verified Technical Contributor' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN ARTICLE BODY & SIDEBAR CONTAINER -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 -mt-14 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT: MAIN CONTENT & COMMENTS (8 COLS) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- ARTICLE CARD -->
                <article class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 space-y-8 shadow-soft">
                    
                    <!-- FEATURED MEDIA: VIDEO OR IMAGE -->
                    @if ($featuredVideo)
                        <div class="rounded-2xl overflow-hidden bg-black aspect-16/9 shadow-inner border border-slate-900">
                            <video src="{{ $featuredVideo }}" controls class="w-full h-full object-contain"></video>
                        </div>
                    @elseif ($featuredImage)
                        <div class="rounded-2xl overflow-hidden bg-slate-100 aspect-16/9 border border-slate-100 shadow-2xs">
                            <img src="{{ $featuredImage }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- EXCERPT CALLOUT -->
                    @if ($post->excerpt)
                        <div class="p-5 rounded-2xl bg-pp-50/70 border-l-4 border-pp-600 text-sm font-semibold text-slate-800 leading-relaxed">
                            {{ $post->excerpt }}
                        </div>
                    @endif

                    <!-- MAIN RICH TEXT CONTENT -->
                    <div class="prose prose-slate prose-lg max-w-none text-slate-800 leading-relaxed font-normal">
                        {!! $post->content !!}
                    </div>

                    <!-- TAGS PILLS -->
                    @if (count($post->tags_list) > 0)
                        <div class="pt-6 border-t border-slate-100 space-y-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 block">Related Topics</span>
                            <div class="flex flex-wrap items-center gap-2">
                                @foreach ($post->tags_list as $t)
                                    <a
                                        href="{{ route('blog.index', ['tag' => $t]) }}"
                                        class="px-3 py-1 rounded-xl bg-slate-100 hover:bg-pp-100 hover:text-pp-700 text-slate-700 font-extrabold text-xs transition"
                                    >
                                        #{{ $t }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- SHARE & REACTION BAR -->
                    <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-extrabold text-slate-500">Share this guide:</span>
                            <div class="flex items-center gap-2">
                                <a
                                    href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->url()) }}"
                                    target="_blank"
                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 grid place-items-center text-xs transition"
                                    title="Share on X"
                                >
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                                <a
                                    href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->url()) }}"
                                    target="_blank"
                                    class="w-8 h-8 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 grid place-items-center text-xs transition"
                                    title="Share on WhatsApp"
                                >
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <button
                                    type="button"
                                    onclick="navigator.clipboard.writeText(window.location.href); alert('Article link copied to clipboard!');"
                                    class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer flex items-center gap-1.5"
                                >
                                    <i class="fas fa-link text-[11px]"></i>
                                    <span>Copy Link</span>
                                </button>
                            </div>
                        </div>

                        @auth
                            <!-- HELPFUL REACTION BUTTON -->
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="toggleHelpful"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $isLiked ? 'bg-pp-100 text-pp-800 border border-pp-200 font-extrabold shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border border-transparent' }}"
                                    title="{{ $isLiked ? 'Marked as helpful (Click to unlike)' : 'Mark as helpful' }}"
                                >
                                    <i class="{{ $isLiked ? 'fas text-pp-600' : 'far text-slate-400' }} fa-thumbs-up text-xs"></i>
                                    <span>Helpful</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $isLiked ? 'bg-pp-200 text-pp-900' : 'bg-white text-slate-700 shadow-2xs' }}">
                                        {{ $likesCount }}
                                    </span>
                                </button>
                            </div>
                        @endauth
                    </div>
                </article>

                <!-- COMMENTS & DISCUSSION SECTION -->
                <section class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 space-y-8 shadow-soft">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <i class="fas fa-comments text-pp-600"></i>
                                <span>Discussion &amp; Comments ({{ $approvedComments->count() }})</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Share your real-world experience, ask technical questions, or give feedback.</p>
                        </div>

                        @if ($isSubscribed)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <i class="fas fa-bell"></i> Subscribed
                            </span>
                        @endif
                    </div>

                    <!-- FLASH STATUS ON COMMENT -->
                    @if (session('comment_status'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-2xs">
                            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                            <span>{{ session('comment_status') }}</span>
                        </div>
                    @endif

                    <!-- COMMENT SUBMISSION FORM -->
                    <form wire:submit.prevent="submitComment" class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-4 text-xs">
                        <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-pen text-pp-600"></i> Leave a Comment
                        </h4>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 block">Your Name <span class="text-rose-500">*</span></label>
                                <input
                                    type="text"
                                    wire:model="commentName"
                                    placeholder="e.g. Chidi Okonkwo"
                                    class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-900 outline-none focus:border-pp-500 transition"
                                />
                                @error('commentName') <span class="text-rose-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700 block">Email Address <span class="text-rose-500">*</span></label>
                                <input
                                    type="email"
                                    wire:model="commentEmail"
                                    placeholder="e.g. chidi@example.com"
                                    class="w-full p-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-900 outline-none focus:border-pp-500 transition"
                                />
                                @error('commentEmail') <span class="text-rose-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 block">Comment / Feedback <span class="text-rose-500">*</span></label>
                            <textarea
                                wire:model="commentBody"
                                rows="3"
                                placeholder="What are your thoughts on this guide? Any practical experience or questions to add?"
                                class="w-full p-3 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 outline-none focus:border-pp-500 transition resize-none"
                            ></textarea>
                            @error('commentBody') <span class="text-rose-600 text-[11px] font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[10px] text-slate-400">
                                Comments are reviewed to prevent spam before appearing publicly.
                            </span>

                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-black text-xs shadow-soft transition cursor-pointer flex items-center gap-1.5"
                            >
                                <i class="fas fa-paper-plane"></i>
                                <span>Post Comment</span>
                            </button>
                        </div>
                    </form>

                    <!-- APPROVED COMMENTS LIST -->
                    @if ($approvedComments->count() > 0)
                        <div class="space-y-4">
                            @foreach ($approvedComments as $comm)
                                <div class="p-5 rounded-2xl bg-white border border-slate-200/80 space-y-2 shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-pp-100 text-pp-700 font-black text-xs grid place-items-center shrink-0">
                                                {{ strtoupper(substr($comm->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h5 class="font-extrabold text-xs text-slate-900">{{ $comm->name }}</h5>
                                                <span class="text-[10px] text-slate-400">{{ $comm->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-700 leading-relaxed font-medium pl-10">
                                        {{ $comm->comment }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs">
                            <i class="fas fa-comment-dots text-2xl text-slate-300 block mb-1"></i>
                            Be the first to leave a comment on this article!
                        </div>
                    @endif

                </section>

            </div>

            <!-- RIGHT: SIDEBAR (4 COLS) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- 1. POST SUBSCRIPTION / WATCHLIST CARD (PRIMARY DIRECTIVE) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-soft">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-bell text-amber-500"></i>
                            <span>Post Notifications</span>
                        </h3>
                        @if ($isSubscribed)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="Active Subscription"></span>
                        @endif
                    </div>

                    <!-- FLASH MESSAGE ON SUBSCRIPTION TOGGLE -->
                    @if (session('subscription_status'))
                        <div class="p-3 rounded-xl bg-pp-50 border border-pp-200 text-pp-800 text-[11px] font-bold leading-relaxed">
                            {{ session('subscription_status') }}
                        </div>
                    @endif

                    @if ($isSubscribed)
                        <!-- SUBSCRIBED STATE -->
                        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2 text-xs">
                            <div class="flex items-center gap-2 text-emerald-800 font-extrabold">
                                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                <span>You are subscribed to this post</span>
                            </div>
                            <p class="text-[11px] text-emerald-700 leading-relaxed">
                                You will receive an instant notification whenever a new comment is approved on this discussion.
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="toggleSubscription"
                            class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-rose-600 font-bold text-xs transition cursor-pointer flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-bell-slash"></i>
                            <span>Unsubscribe</span>
                        </button>
                    @else
                        <!-- NOT SUBSCRIBED STATE -->
                        <div class="space-y-2 text-xs">
                            <p class="text-slate-600 leading-relaxed">
                                Want to follow this guide? Subscribe to get notified whenever other technicians or readers post approved replies.
                            </p>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                <i class="fas fa-shield-alt text-pp-600"></i>
                                <span>{{ $watchersCount }} members are watching this post</span>
                            </div>
                        </div>

                        @auth
                            <button
                                type="button"
                                wire:click="toggleSubscription"
                                class="w-full py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition cursor-pointer flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-bell"></i>
                                <span>Subscribe to this Post</span>
                            </button>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="w-full py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center justify-center gap-2 text-center"
                            >
                                <i class="fas fa-bell"></i>
                                <span>Sign In to Subscribe</span>
                            </a>
                        @endauth
                    @endif

                    @auth
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">Did you find this helpful?</span>
                            <button
                                type="button"
                                wire:click="toggleHelpful"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $isLiked ? 'bg-pp-100 text-pp-800 border border-pp-200 font-extrabold shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border border-transparent' }}"
                                title="{{ $isLiked ? 'Marked as helpful (Click to unlike)' : 'Mark as helpful' }}"
                            >
                                <i class="{{ $isLiked ? 'fas text-pp-600' : 'far text-slate-400' }} fa-thumbs-up text-xs"></i>
                                <span>Helpful</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $isLiked ? 'bg-pp-200 text-pp-900' : 'bg-white text-slate-700 shadow-2xs' }}">
                                    {{ $likesCount }}
                                </span>
                            </button>
                        </div>
                    @endauth
                </div>

                <!-- 2. AUTHOR CARD -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-soft">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                        About the Contributor
                    </h3>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-pp-100 text-pp-700 font-black text-base grid place-items-center shrink-0">
                            {{ strtoupper(substr($post->user?->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-slate-900">{{ $post->user?->name ?? 'Parts & Parcel' }}</h4>
                            <p class="text-xs text-slate-500 font-medium">{{ $post->user?->business_name ?: 'Verified Technical Yard' }}</p>
                        </div>
                    </div>

                    @if ($post->user?->bio)
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $post->user->bio }}
                        </p>
                    @endif

                    @if ($post->user)
                        <a
                            href="{{ route('user.profile', $post->user->id) }}"
                            class="block w-full py-2 rounded-xl bg-slate-50 hover:bg-pp-50 hover:text-pp-700 text-slate-700 font-bold text-xs transition border border-slate-200 text-center"
                        >
                            View Contributor Profile →
                        </a>
                    @endif
                </div>

                <!-- 2B. FEATURED PROMOTED LISTING -->
                <livewire:components.promotions.blog-page-promotion />

                <!-- 3. RELATED GUIDES IN SAME CATEGORY -->
                @if ($relatedPosts->count() > 0)
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-4 shadow-soft">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span>Related Guides</span>
                            <i class="fas fa-book-open text-pp-600"></i>
                        </h3>

                        <div class="divide-y divide-slate-100 space-y-3">
                            @foreach ($relatedPosts as $rel)
                                <div class="pt-3 first:pt-0 space-y-1">
                                    <span class="text-[10px] font-bold text-pp-600 uppercase">
                                        {{ $rel->category?->name }}
                                    </span>
                                    <a
                                        href="{{ route('blog.show', $rel->slug) }}"
                                        class="font-extrabold text-xs text-slate-900 hover:text-pp-600 transition line-clamp-2 block leading-snug"
                                    >
                                        {{ $rel->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 block">
                                        {{ $rel->published_at ? $rel->published_at->format('M d, Y') : $rel->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

</div>
