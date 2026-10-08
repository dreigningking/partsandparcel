<div class="min-h-screen bg-slate-50/60 dark:bg-slate-950 pb-20">

    <!-- HEADER COVER & BREADCRUMBS -->
    <div class="relative bg-gradient-to-r from-pp-900 via-pp-800 to-slate-900 text-white pt-10 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden shadow-md">
        <!-- Ambient decorative shapes -->
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-pp-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10 space-y-4">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-pp-200/80">
                <a href="{{ route('welcome') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <a href="{{ route('help') }}" class="hover:text-white transition">Help Center</a>
                <span>/</span>
                <span class="text-amber-400 font-bold">{{ $topic ?? 'General Guide' }}</span>
            </nav>

            <div class="max-w-4xl space-y-3 pt-2">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 backdrop-blur border border-amber-400/40 text-amber-300 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-circle-question text-[11px]"></i>
                        <span>{{ $topic ?? 'Help Article' }}</span>
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-pp-300"></i>
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                    </span>
                    <span class="text-xs text-pp-200 flex items-center gap-1.5">
                        <i class="fas fa-shield-halved text-emerald-400"></i>
                        Verified Platform Guide
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                    {{ $post->title }}
                </h1>
            </div>
        </div>
    </div>

    <!-- MAIN BODY & SIDEBAR CONTAINER -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT: MAIN ARTICLE CONTENT (8 COLS) -->
            <div class="lg:col-span-8 space-y-8">

                <!-- ARTICLE CARD -->
                <article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-10 space-y-8 shadow-soft">

                    <!-- FEATURED MEDIA: VIDEO OR IMAGE -->
                    @if ($featuredVideo)
                        <div class="rounded-2xl overflow-hidden bg-black aspect-16/9 shadow-inner border border-slate-900">
                            <video src="{{ $featuredVideo }}" controls class="w-full h-full object-contain"></video>
                        </div>
                    @elseif ($featuredImage)
                        <div class="rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 aspect-16/9 border border-slate-100 dark:border-slate-800 shadow-2xs">
                            <img src="{{ $featuredImage }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- EXCERPT CALLOUT -->
                    @if ($post->excerpt)
                        <div class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 leading-relaxed">
                            {{ $post->excerpt }}
                        </div>
                    @endif

                    <!-- MAIN RICH TEXT CONTENT -->
                    <div class="prose prose-slate dark:prose-invert prose-base sm:prose-lg max-w-none text-slate-800 dark:text-slate-200 leading-relaxed font-normal">
                        {!! $post->content !!}
                    </div>

                    <!-- HELPFUL FEEDBACK WIDGET -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                    Was this article helpful?
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Your anonymous feedback helps us improve our buyer and seller guides.
                                </p>
                            </div>

                            @if ($wasHelpful === null)
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="rateHelpful(true)"
                                        class="px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 text-slate-700 dark:text-slate-200 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer shadow-2xs hover:text-emerald-600"
                                    >
                                        <i class="fas fa-thumbs-up text-emerald-500"></i>
                                        <span>Yes</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="rateHelpful(false)"
                                        class="px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-rose-500 text-slate-700 dark:text-slate-200 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer shadow-2xs hover:text-rose-600"
                                    >
                                        <i class="fas fa-thumbs-down text-rose-500"></i>
                                        <span>No</span>
                                    </button>
                                </div>
                            @else
                                <div class="px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center gap-1.5">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Thank you for your feedback!</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- DIRECT CONTACT BRIDGE -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">Still have questions about this topic?</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Our customer support and escrow mediation specialists are on standby.</p>
                        </div>
                        <a
                            href="{{ route('contact') }}"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center justify-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <i class="fas fa-headset"></i>
                            <span>Contact Support</span>
                        </a>
                    </div>

                </article>

            </div>

            <!-- RIGHT: SIDEBAR (4 COLS) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- RELATED ARTICLES IN THIS TOPIC -->
                @if ($relatedArticles->count() > 0)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                            <i class="fas fa-book-open text-amber-500"></i>
                            <span>More in {{ $topic }}</span>
                        </h3>

                        <ul class="space-y-3">
                            @foreach ($relatedArticles as $related)
                                <li>
                                    <a
                                        href="{{ route('help.show', $related->slug) }}"
                                        class="group block p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition"
                                    >
                                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-pp-600 dark:group-hover:text-pp-400 transition leading-snug line-clamp-2">
                                            {{ $related->title }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 mt-1 block">
                                            {{ $related->published_at ? $related->published_at->format('M d, Y') : $related->created_at->format('M d, Y') }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- BROWSE ALL HELP TOPICS -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 space-y-4 shadow-soft">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <i class="fas fa-layer-group text-pp-600"></i>
                        <span>Explore Help Topics</span>
                    </h3>

                    <ul class="space-y-2 text-xs">
                        @foreach ($allTopics as $t)
                            <li>
                                <a
                                    href="{{ route('help') }}#{{ Str::slug($t) }}"
                                    class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition font-bold {{ $t === $topic ? 'text-pp-600 bg-pp-50/70 dark:bg-pp-950/40' : 'text-slate-600 dark:text-slate-300' }}"
                                >
                                    <span>{{ $t }}</span>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a
                            href="{{ route('help') }}"
                            class="text-xs font-extrabold text-pp-600 hover:text-pp-700 flex items-center gap-1.5"
                        >
                            <span>Back to Help Hub</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- URGENT DISPUTE ALERT -->
                <div class="p-6 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-slate-900 dark:text-white space-y-3">
                    <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-extrabold text-xs">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span>Urgent Dispute on Delivered Part?</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        If you received an incorrect part, file an instant claim on your Invoices dashboard within 48 hours to freeze the escrow release.
                    </p>
                    <a
                        href="{{ route('invoices') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 dark:text-amber-400 hover:underline"
                    >
                        <span>Manage Invoices</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

            </div>

        </div>
    </div>

</div>
