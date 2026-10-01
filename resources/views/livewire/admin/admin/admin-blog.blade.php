<x-admin.page title="Blog" description="Posts list with owner/category filters and status controls.">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.blog.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Create post</a>
    </div>
    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search owner or title..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
        <select wire:model.live="category" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All categories</option>
            @foreach ($categories as $categoryOption)
                <option value="{{ $categoryOption->id }}">{{ $categoryOption->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $statusOption)
                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Date Created</th>
                    <th class="px-4 py-3">Owner</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Category &amp; Tag</th>
                    <th class="px-4 py-3">Related Listing</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">View</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $post->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $post->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $post->title }}</td>
                        <td class="px-4 py-3">
                            {{ $post->category?->name ?? '—' }} /
                            @php $adminTags = $post->tags ? array_filter(array_map('trim', explode(',', $post->tags))) : []; @endphp
                            @if(count($adminTags) > 0)
                                @foreach($adminTags as $t)
                                    <a href="{{ route('blog', ['tag' => $t]) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">#{{ $t }}</a>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $post->listing?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $post->status }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.blog.show', $post) }}" class="font-medium text-emerald-600 hover:underline">View</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('admin.blog.edit', $post) }}" class="font-medium text-gray-600 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No posts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</x-admin.page>
