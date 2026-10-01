<x-admin.page :title="$heading" description="Compose the post, category, optional listing link, and featured image.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="max-w-3xl space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model="title" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Excerpt</label>
            <textarea wire:model="excerpt" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
            @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Content</label>
            <textarea wire:model="content" rows="12" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm"></textarea>
            @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model="category_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Related listing</label>
                <select wire:model="listing_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="">None</option>
                    @foreach ($listings as $prop)
                        <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                    @endforeach
                </select>
                @error('listing_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tags (comma-separated)</label>
            <input type="text" wire:model="tagsInput" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="market, tips, lagos" />
            @error('tagsInput') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model="status" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="draft">draft</option>
                    <option value="published">published</option>
                    <option value="archived">archived</option>
                </select>
                @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Content source</label>
                <select wire:model="content_source" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="manual">manual</option>
                    <option value="ai">ai</option>
                </select>
                @error('content_source') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Featured image</label>
                <input type="file" wire:model="featuredImage" accept="image/*" class="mt-1 block w-full text-sm text-gray-600" />
                @error('featuredImage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="featuredImage" class="mt-1 text-xs text-gray-500">Uploading…</div>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">{{ $submitLabel }}</button>
            <a href="{{ route('admin.blog') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to list</a>
        </div>
    </form>
</x-admin.page>
