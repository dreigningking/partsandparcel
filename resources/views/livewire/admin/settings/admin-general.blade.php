<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700 dark:text-pp-400">System Settings</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">General Settings</span>
            </div>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                    General Platform Settings
                </h1>
                <div class="flex items-center gap-3 self-start sm:self-auto">
                    <button
                        type="button"
                        wire:click="resetToDefaults"
                        wire:confirm="Are you sure you want to reset all platform settings to their default values? Custom changes will be overwritten."
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs transition flex items-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-undo-alt text-slate-400"></i>
                        <span>Restore Defaults</span>
                    </button>

                    <button
                        type="button"
                        wire:click="saveSettings"
                        class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-save"></i>
                        <span wire:loading.remove wire:target="saveSettings">Save All Settings</span>
                        <span wire:loading wire:target="saveSettings">Saving...</span>
                    </button>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure core marketplace parameters, media limits, promotional thresholds, and order transaction timelines.
            </p>
        </div>

        <!-- ACTIONS -->
        
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-bold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
            <span class="text-2xs text-emerald-600 dark:text-emerald-400 uppercase tracking-widest font-extrabold">Saved</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- SEGMENT TABS & SEARCH BAR -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Segment Navigation Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <button
                type="button"
                wire:click="setSegment('marketplace')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeSegment === 'marketplace' ? 'bg-pp-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-store text-2xs"></i>
                <span>Marketplace</span>
                <span class="px-1.5 py-0.5 rounded-full text-2xs font-extrabold {{ $activeSegment === 'marketplace' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                    {{ $counts['marketplace'] ?? 0 }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setSegment('media')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeSegment === 'media' ? 'bg-pp-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-photo-video text-2xs"></i>
                <span>Media &amp; Files</span>
                <span class="px-1.5 py-0.5 rounded-full text-2xs font-extrabold {{ $activeSegment === 'media' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                    {{ $counts['media'] ?? 0 }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setSegment('promotions')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeSegment === 'promotions' ? 'bg-pp-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-bullhorn text-2xs"></i>
                <span>Promotions</span>
                <span class="px-1.5 py-0.5 rounded-full text-2xs font-extrabold {{ $activeSegment === 'promotions' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                    {{ $counts['promotions'] ?? 0 }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setSegment('timelines')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeSegment === 'timelines' ? 'bg-pp-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-clock text-2xs"></i>
                <span>Timelines</span>
                <span class="px-1.5 py-0.5 rounded-full text-2xs font-extrabold {{ $activeSegment === 'timelines' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                    {{ $counts['timelines'] ?? 0 }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setSegment('all')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer {{ $activeSegment === 'all' ? 'bg-pp-600 text-white shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-sliders-h text-2xs"></i>
                <span>All Settings</span>
                <span class="px-1.5 py-0.5 rounded-full text-2xs font-extrabold {{ $activeSegment === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </button>
        </div>
    </div>
    <!-- Search Bar -->
    <div class="relative min-w-[220px] p-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-medium rounded-xl">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Filter settings..."
            class="w-full pl-8 pr-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600"
        />
        @if ($search !== '')
            <button
                type="button"
                wire:click="$set('search', '')"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                title="Clear filter"
            >
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>

    <!-- SETTINGS LIST -->
    <div class="space-y-4">
        @forelse ($settingsList as $setting)
            @php
                $meta = $metadata[$setting->name] ?? [
                    'label' => \Illuminate\Support\Str::headline($setting->name),
                    'description' => 'Configure platform setting value for ' . \Illuminate\Support\Str::headline($setting->name) . '.',
                    'icon' => 'fas fa-cog',
                ];
                $type = $setting->type;
            @endphp

            <div
                wire:key="setting-card-{{ $setting->id }}"
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:border-pp-300 dark:hover:border-slate-700 transition"
            >
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                    
                    <!-- Left: Metadata, Labels, Descriptions -->
                    <div class="lg:col-span-6 space-y-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-pp-50 dark:bg-pp-900/30 text-pp-600 dark:text-pp-400 flex items-center justify-center shrink-0 text-sm">
                                <i class="{{ $meta['icon'] ?? 'fas fa-cog' }}"></i>
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                    {{ $meta['label'] }}
                                </h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <code class="text-xs font-mono px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        {{ $setting->name }}
                                    </code>
                                    <span class="text-xs uppercase font-extrabold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $setting->segment }}
                                    </span>
                                    {{-- <span class="text-3xs uppercase font-extrabold tracking-wider px-1.5 py-0.5 rounded {{ $type === 'boolean' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : ($type === 'array' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400' : ($type === 'integer' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400')) }}">
                                        {{ $type }}
                                    </span> --}}
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 pl-10 leading-relaxed">
                            {{ $meta['description'] }}
                        </p>
                    </div>

                    <!-- Right: Input Element Dependent on Type -->
                    <div class="lg:col-span-6 lg:pl-4 lg:border-l border-slate-100 dark:border-slate-800">
                        
                        {{-- 1. BOOLEAN TYPE: RADIO BUTTONS --}}
                        @if ($type === 'boolean')
                            <div class="space-y-2">
                                <span class="block text-2xs font-extrabold uppercase tracking-wider text-slate-400">Selection</span>
                                <div class="flex flex-wrap items-center gap-6 pt-1">
                                    <label class="inline-flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 dark:text-slate-200 group">
                                        <input
                                            type="radio"
                                            wire:model="settings.{{ $setting->name }}"
                                            value="1"
                                            name="radio_{{ $setting->name }}"
                                            class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800 transition cursor-pointer"
                                        />
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">Yes / Enabled</span>
                                        </span>
                                    </label>

                                    <label class="inline-flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 dark:text-slate-200 group">
                                        <input
                                            type="radio"
                                            wire:model="settings.{{ $setting->name }}"
                                            value="0"
                                            name="radio_{{ $setting->name }}"
                                            class="w-4 h-4 text-slate-600 focus:ring-slate-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800 transition cursor-pointer"
                                        />
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                            <span class="group-hover:text-slate-900 dark:group-hover:text-white transition">No / Disabled</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                        {{-- 2. ARRAY TYPE: TAGS INPUT --}}
                        @elseif ($type === 'array')
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-2xs font-extrabold uppercase tracking-wider text-slate-400">Configured Options (Tags)</span>
                                    <span class="text-3xs text-slate-400 font-semibold">
                                        {{ count($settings[$setting->name] ?? []) }} added
                                    </span>
                                </div>

                                <!-- Current Tags Container -->
                                <div class="flex flex-wrap items-center gap-1.5 min-h-[42px] p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                    @forelse ($settings[$setting->name] ?? [] as $tagIndex => $tagValue)
                                        <span wire:key="tag-{{ $setting->name }}-{{ $tagIndex }}-{{ $tagValue }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold transition hover:bg-pp-600/20"
                                        >
                                            <span>{{ $tagValue }}</span>
                                            <button
                                                type="button"
                                                wire:click="removeTag('{{ $setting->name }}', {{ $tagIndex }})"
                                                class="text-pp-700 dark:text-pp-300 hover:text-rose-600 dark:hover:text-rose-400 transition ml-0.5 focus:outline-hidden cursor-pointer"
                                                title="Remove '{{ $tagValue }}'"
                                            >
                                                <i class="fas fa-times text-2xs"></i>
                                            </button>
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic px-1">
                                            No options added yet. Type below to add options.
                                        </span>
                                    @endforelse
                                </div>

                                <!-- Add New Tag Input -->
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <input
                                            type="text"
                                            wire:model="newTag.{{ $setting->name }}"
                                            wire:keydown.enter.prevent="addTag('{{ $setting->name }}')"
                                            placeholder="Type an option and press Enter..."
                                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-medium"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="addTag('{{ $setting->name }}')"
                                        class="px-3.5 py-2 rounded-xl bg-slate-900 dark:bg-slate-100 hover:bg-pp-600 dark:hover:bg-pp-600 text-white dark:text-slate-900 dark:hover:text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shrink-0"
                                    >
                                        <i class="fas fa-plus text-2xs"></i>
                                        <span>Add Tag</span>
                                    </button>
                                </div>
                            </div>

                        {{-- 3. INTEGER TYPE: NUMBER INPUT --}}
                        @elseif ($type === 'integer')
                            <div class="space-y-1.5">
                                <label for="input_{{ $setting->name }}" class="block text-2xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Numeric Value
                                </label>
                                <div class="relative max-w-xs">
                                    <input
                                        id="input_{{ $setting->name }}"
                                        type="number"
                                        wire:model="settings.{{ $setting->name }}"
                                        min="0"
                                        step="1"
                                        class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-bold tracking-wide"
                                    />
                                    @if (!empty($meta['unit']))
                                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none uppercase">
                                            {{ $meta['unit'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                        {{-- 4. STRING / TEXT TYPE --}}
                        @else
                            <div class="space-y-1.5">
                                <label for="input_{{ $setting->name }}" class="block text-2xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Value
                                </label>
                                <input
                                    id="input_{{ $setting->name }}"
                                    type="text"
                                    wire:model="settings.{{ $setting->name }}"
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-pp-600 font-medium"
                                />
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-sliders-h text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No settings found</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    No settings matched the selected segment or search criteria.
                </p>
                <div class="mt-4 flex items-center justify-center gap-2">
                    @if ($search !== '')
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition cursor-pointer"
                        >
                            Clear Search Filter
                        </button>
                    @endif
                    <button
                        type="button"
                        wire:click="setSegment('all')"
                        class="px-3.5 py-2 rounded-xl bg-pp-600 text-white text-xs font-bold hover:bg-pp-700 transition cursor-pointer"
                    >
                        View All Settings
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- BOTTOM SAVE BAR -->
    @if ($settingsList->isNotEmpty())
        <div class="sticky bottom-4 z-20 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i class="fas fa-info-circle text-pp-600"></i>
                <span>Changes will be applied immediately across the marketplace and services.</span>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    wire:click="saveSettings"
                    class="px-6 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-check"></i>
                    <span wire:loading.remove wire:target="saveSettings">Save Settings</span>
                    <span wire:loading wire:target="saveSettings">Saving...</span>
                </button>
            </div>
        </div>
    @endif

</div>
