@php
    $featuredUrl = $featured?->url;
@endphp
<x-admin.page :title="$post->title" description="Admin view: promotion ties, listing link, and actions.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.blog.edit', $post) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Edit</a>
        @if ($post->content_source === 'ai')
            <button type="button" wire:click="regenerate" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50">Regenerate</button>
        @endif
        <button type="button" wire:click="delete" wire:confirm="{{ __('Delete this post?') }}" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">Delete</button>
        <a href="{{ route('admin.blog') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to list</a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <section>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Content</h3>
                @if ($featuredUrl)
                    <img src="{{ $featuredUrl }}" alt="" class="mt-2 max-h-64 w-full rounded-lg object-cover border border-gray-100" />
                @endif
                <div class="prose prose-sm mt-4 max-w-none text-gray-800">{!! nl2br(e($post->content)) !!}</div>
            </section>

            <section class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Promotion records</h3>
                @if ($post->promotions->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">No promotions linked to this post.</p>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 text-sm">
                        @foreach ($post->promotions as $promo)
                            <li class="py-2 flex flex-wrap justify-between gap-2">
                                <span class="font-medium text-gray-800">{{ $promo->plan?->name ?? 'Plan #' . $promo->promotion_plan_id }}</span>
                                <span class="text-gray-500">{{ $promo->status }} · {{ optional($promo->start_at)->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Analytics</h3>
                <p class="mt-2 text-sm text-gray-500">Post-level analytics (views, engagement) can be wired when tracking is available.</p>
            </section>
        </div>

        <div class="space-y-6 text-sm">
            <section class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-xs font-semibold uppercase text-gray-500">Meta</h3>
                <dl class="mt-2 space-y-1 text-gray-700">
                    <div><dt class="text-gray-500">Status</dt><dd>{{ $post->status }}</dd></div>
                    <div><dt class="text-gray-500">Source</dt><dd>{{ $post->content_source }}{{ $post->ai_generated_at ? ' · ' . $post->ai_generated_at->format('Y-m-d H:i') : '' }}</dd></div>
                    <div><dt class="text-gray-500">Tags</dt><dd>{{ $post->tags ?: '—' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-xs font-semibold uppercase text-gray-500">Related listing</h3>
                @if ($post->listing)
                    <a href="{{ route('admin.listings.show', $post->listing) }}" class="mt-2 inline-block font-medium text-emerald-600 hover:underline">{{ $post->listing->name }}</a>
                @else
                    <p class="mt-2 text-gray-500">None</p>
                @endif
            </section>

            <section class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-xs font-semibold uppercase text-gray-500">Author</h3>
                <p class="mt-2 font-medium text-gray-900">{{ $post->user?->name ?? '—' }}</p>
                <p class="text-gray-500">{{ $post->user?->email }}</p>
            </section>
        </div>
    </div>
</x-admin.page>
