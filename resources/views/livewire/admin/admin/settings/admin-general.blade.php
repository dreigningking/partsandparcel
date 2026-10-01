<x-admin.page title="General settings" description="Content generation and AI defaults for promotions and editorial workflows.">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="max-w-4xl space-y-5">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button type="button" wire:click.prevent="$set('activeTab','content')" class="px-1 py-4 text-sm font-medium {{ $activeTab === 'content' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Content
                </button>
                <button type="button" wire:click.prevent="openFieldsTab" class="px-1 py-4 text-sm font-medium {{ $activeTab === 'fields' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Listing Fields
                </button>
            </nav>
        </div>

        {{-- Content tab (compact) --}}
        @if($activeTab === 'content')
            <form wire:submit="save" class="max-w-4xl space-y-5">
                <div class="rounded-lg border border-gray-200 p-4">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="ai_enabled" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Enable AI features
                    </label>
                    @error('ai_enabled') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-gray-500">API keys remain in <code>.env</code>. Register new providers in <code>config/ai.php</code>.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Content generation</label>
                    <select wire:model="generation_mode" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="manual">Manual</option>
                        <option value="ai">AI</option>
                    </select>
                    @error('generation_mode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Manual timeframe (hours)</label>
                    <input type="number" wire:model="manual_timeframe_hours" min="1" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                    @error('manual_timeframe_hours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Minimum word count</label>
                        <input type="number" wire:model="min_word_count" min="50" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('min_word_count') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Maximum word count</label>
                        <input type="number" wire:model="max_word_count" min="50" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('max_word_count') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">AI providers &amp; models</h3>
                            <p class="text-xs text-gray-500 mt-1">Configure multiple provider + model pairs. One must be marked as default for promotion content generation.</p>
                        </div>
                        <button type="button" wire:click="addContentProvider" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Add provider
                        </button>
                    </div>

                    @error('contentProviders') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <div class="hidden md:grid md:grid-cols-[1fr_1fr_1fr_auto_auto] gap-3 bg-gray-50 px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wide">
                            <span>Provider</span>
                            <span>Model</span>
                            <span>Label (optional)</span>
                            <span>Default</span>
                            <span></span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach ($contentProviders as $index => $row)
                                <div class="p-4 grid grid-cols-1 md:grid-cols-[1fr_1fr_1fr_auto_auto] gap-3 items-start" wire:key="ai-provider-{{ $row['id'] }}">
                                    <div>
                                        <label class="md:sr-only text-xs text-gray-500">Provider</label>
                                        <select wire:model.live="contentProviders.{{ $index }}.provider" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                            @foreach ($this->providerRegistry as $key => $provider)
                                                <option value="{{ $key }}" @disabled(! $provider['compatible'])>
                                                    {{ $provider['label'] }}
                                                    @if ($provider['configured'])
                                                        (key set)
                                                    @else
                                                        (no key)
                                                    @endif
                                                    @if (! $provider['compatible'])
                                                        — not supported yet
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('contentProviders.'.$index.'.provider') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="md:sr-only text-xs text-gray-500">Model</label>
                                        <input
                                            type="text"
                                            wire:model="contentProviders.{{ $index }}.model"
                                            list="models-{{ $row['id'] }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                                            placeholder="e.g. gpt-4o-mini"
                                        />
                                        <datalist id="models-{{ $row['id'] }}">
                                            @foreach ($this->providerRegistry[$row['provider']]['models'] ?? [] as $model)
                                                <option value="{{ $model }}"></option>
                                            @endforeach
                                        </datalist>
                                        @error('contentProviders.'.$index.'.model') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="md:sr-only text-xs text-gray-500">Label</label>
                                        <input type="text" wire:model="contentProviders.{{ $index }}.label" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="e.g. Fast drafts" />
                                        @error('contentProviders.'.$index.'.label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex items-center pt-2 md:pt-0">
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                            <input
                                                type="radio"
                                                name="default_content_provider"
                                                wire:click="setDefaultContentProvider({{ $index }})"
                                                @checked($row['is_default'] ?? false)
                                                class="text-emerald-600 focus:ring-emerald-500"
                                            >
                                            Default
                                        </label>
                                    </div>
                                    <div class="flex items-center pt-1 md:pt-0">
                                        @if (count($contentProviders) > 1)
                                            <button type="button" wire:click="removeContentProvider({{ $index }})" class="text-xs text-red-600 hover:underline">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-3 text-xs text-gray-600 space-y-1">
                        <p class="font-medium text-gray-700">Env key status</p>
                        @foreach ($this->providerRegistry as $key => $provider)
                            <p>
                                <span class="font-mono">{{ $provider['env_key'] }}</span>:
                                @if ($provider['configured'])
                                    <span class="text-emerald-700">configured</span>
                                @else
                                    <span class="text-gray-400">not set</span>
                                @endif
                            </p>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Temperature (0 to 2)</label>
                        <input type="number" step="0.1" wire:model="ai_temperature" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('ai_temperature') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max tokens</label>
                        <input type="number" wire:model="ai_max_tokens" min="50" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('ai_max_tokens') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Timeout (seconds)</label>
                        <input type="number" wire:model="ai_timeout_seconds" min="5" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('ai_timeout_seconds') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Daily AI limit per user</label>
                        <input type="number" wire:model="ai_rate_limit_per_user_per_day" min="1" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                        @error('ai_rate_limit_per_user_per_day') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Save</button>
            </form>
        @endif

        {{-- Fields tab --}}
        @if($activeTab === 'fields')
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900">Category Fields</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Edit in a modal · options = one label per line</p>
                    </div>
                    <button type="button" wire:click="openAddField" class="shrink-0 px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800">Add Field</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500">
                                <th class="p-2">Label</th>
                                <th class="p-2">Key</th>
                                <th class="p-2">Type</th>
                                <th class="p-2">Required</th>
                                <th class="p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->fields() as $f)
                                <tr class="border-t">
                                    <td class="p-2">{{ $f->label }}</td>
                                    <td class="p-2">{{ $f->key }}</td>
                                    <td class="p-2">{{ $f->data_type }}</td>
                                    <td class="p-2">{{ $f->is_required ? 'Yes' : 'No' }}</td>
                                    <td class="p-2">
                                        <button wire:click="openEditField({{ $f->id }})" class="text-emerald-600 mr-2">Edit</button>
                                        <button wire:click="deleteField({{ $f->id }})" class="text-red-600">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Add Field Modal -->
                @if($showAddModal)
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overscroll-contain"
                        wire:click.self="$set('showAddModal', false)"
                        wire:key="add-modal-backdrop"
                    >
                        <div
                            class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="add-field-title"
                            @click.stop
                        >
                            <button
                                type="button"
                                wire:click="$set('showAddModal', false)"
                                class="absolute right-4 top-4 rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                aria-label="Close"
                            >
                                <i class="ri-close-line text-xl"></i>
                            </button>

                            <h3 id="add-field-title" class="text-lg font-semibold text-gray-900 pr-10 mb-1">New field</h3>
                            <p class="text-xs font-mono text-gray-500 mb-4 break-all">Key will be used in imports/exports; keep lowercase with dashes.</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Key <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="field_key" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                    @error('field_key') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Label <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="field_label" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                    @error('field_label') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Data type</label>
                                    <select wire:model="field_data_type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                                        @foreach($this->dataTypeOptions() as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" wire:model="field_is_required" class="rounded border-gray-300 text-emerald-600">
                                        Required
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Options (one per line)</label>
                                    <textarea wire:model="field_optionsLines" rows="5" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-mono" placeholder="One option per line for select fields"></textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-4">
                                <button type="button" wire:click="$set('showAddModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="button" wire:click="saveNewField" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">Save field</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Edit Field Modal -->
                @if($showEditModal)
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overscroll-contain"
                        wire:click.self="$set('showEditModal', false)"
                        wire:key="edit-modal-backdrop"
                    >
                        <div
                            class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="edit-field-title"
                            @click.stop
                        >
                            <button
                                type="button"
                                wire:click="$set('showEditModal', false)"
                                class="absolute right-4 top-4 rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                aria-label="Close"
                            >
                                <i class="ri-close-line text-xl"></i>
                            </button>

                            <h3 id="edit-field-title" class="text-lg font-semibold text-gray-900 pr-10 mb-1">Edit field</h3>
                            <p class="text-xs font-mono text-gray-500 mb-4 break-all">{{ $field_key }}</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Label <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="field_label" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                    @error('field_label') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Data type</label>
                                    <select wire:model="field_data_type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                                        @foreach($this->dataTypeOptions() as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" wire:model="field_is_required" class="rounded border-gray-300 text-emerald-600">
                                        Required
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Options (one per line)</label>
                                    <textarea wire:model="field_optionsLines" rows="5" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-mono"></textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-4">
                                <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="button" wire:click="saveEditField" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">Save changes</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-admin.page>
