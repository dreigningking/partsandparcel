<x-admin.page
    title="Categories &amp; field definitions"
    description="Manage listing categories and attach/detach dynamic fields used on seller listing forms."
>
    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid min-w-0 grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)] gap-8">
        <div class="min-w-0">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Categories</h3>
            <div class="rounded-xl border border-gray-200 overflow-hidden">
                @forelse ($categories as $cat)
                    <button
                        type="button"
                        wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                        class="w-full text-left px-4 py-3 text-sm border-b border-gray-100 last:border-0 transition
                            {{ (int) $selectedCategoryId === (int) $cat->id ? 'bg-emerald-50 text-emerald-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                    >
                        <span class="block truncate">{{ $cat->name }}</span>
                        <span class="block text-xs text-gray-400 mt-0.5 truncate">{{ $cat->fields_count }} fields · {{ $cat->slug }}</span>
                    </button>
                @empty
                    <p class="px-4 py-6 text-sm text-gray-500">No categories found. Run <code class="text-xs bg-gray-100 px-1 rounded">php artisan db:seed --class=CategoriesSeeder</code>.</p>
                @endforelse
            </div>
        </div>

        <div class="min-w-0 space-y-8">
            @if ($selectedCategory)
                <div class="min-w-0 rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Category</h3>
                    <p class="text-sm text-gray-500 mb-4">Slug is fixed for routing and seeds; display name can change.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 min-w-0">
                        <div class="min-w-0">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Slug</label>
                            <input
                                type="text"
                                readonly
                                value="{{ $categorySlug }}"
                                class="w-full min-w-0 px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-600"
                            />
                        </div>
                        <div class="min-w-0">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Display name</label>
                            <div class="flex flex-wrap gap-2">
                                <input
                                    type="text"
                                    wire:model.live="categoryName"
                                    class="min-w-0 flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                />
                                <button
                                    type="button"
                                    wire:click="saveCategoryMeta"
                                    class="shrink-0 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700"
                                >
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="min-w-0 rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">Attached Fields</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Attach or remove fields for this category. Field definitions are edited under <a href="{{ route('admin.settings.general') }}" class="text-emerald-600 hover:underline">General Settings > Listing Fields</a>.</p>
                        </div>
                        <button
                            type="button"
                            wire:click="openAttachModal"
                            class="shrink-0 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800"
                        >
                            Attach field
                        </button>
                    </div>

                    <ul class="divide-y divide-gray-100">
                        @if ($selectedCategory->fields->isEmpty())
                            <li class="px-4 sm:px-6 py-8 text-center text-sm text-gray-500">
                                No field definitions attached yet. Click “Attach field” to add one.
                            </li>
                        @endif
                        @foreach ($selectedCategory->fields as $field)
                            <li class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3 min-w-0" wire:key="field-row-{{ $field->id }}">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 truncate">{{ $field->label }}</p>
                                    <p class="font-mono text-xs text-gray-500 truncate">{{ $field->key }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5">{{ $field->data_type }}</span>
                                        @if ($field->is_required)
                                            <span class="text-red-600">· required</span>
                                        @endif
                                        <span class="text-gray-400">· sort {{ $field->pivot->sort_order }}</span>
                                    </p>
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <button
                                        type="button"
                                        wire:click="detachField({{ $field->id }})"
                                        wire:confirm="Remove {{ $field->label }} from {{ $categoryName }}?"
                                        class="px-3 py-1.5 rounded-lg border border-red-200 text-sm font-medium text-red-700 hover:bg-red-50"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500">Select a category to manage its fields.</p>
            @endif
        </div>
    </div>

    {{-- Attach field modal --}}
    @if ($showAttachModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overscroll-contain"
            wire:click.self="closeAttachModal"
            wire:key="attach-modal-backdrop"
        >
            <div
                class="relative w-full max-w-md max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="attach-field-title"
                @click.stop
            >
                <button
                    type="button"
                    wire:click="closeAttachModal"
                    class="absolute right-4 top-4 rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Close"
                >
                    <i class="ri-close-line text-xl"></i>
                </button>
                <h3 id="attach-field-title" class="text-lg font-semibold text-gray-900 pr-10 mb-1">Attach field to {{ $categoryName }}</h3>
                <p class="text-xs text-gray-500 mb-4">Select an unattached field to add to this category.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Select Field <span class="text-red-500">*</span></label>
                        <select wire:model.live="selectedFieldToAttach" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                            <option value="">-- Choose a field --</option>
                            @foreach ($availableFields as $f)
                                <option value="{{ $f->id }}">{{ $f->label }} ({{ $f->key }}) [{{ $f->data_type }}]</option>
                            @endforeach
                        </select>
                        @error('selectedFieldToAttach') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        @if ($availableFields->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">All available fields are already attached to this category.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sort order</label>
                        <input type="number" wire:model.live="attachSortOrder" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" min="0" />
                        @error('attachSortOrder') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" wire:click="closeAttachModal" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" wire:click="attachField" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700" @disabled(!$selectedFieldToAttach)>
                        Attach field
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-admin.page>
