@props([
    'options' => [],
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search options...',
    'name' => null,
    'id' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'clearable' => true,
    'class' => '',
    'buttonClass' => '',
])

@php
    $id = $id ?? 'searchable-select-' . uniqid();
    $wireModel = $attributes->wire('model');
    $wireModelValue = $wireModel ? $wireModel->value() : null;

    // Normalize options into: [['value' => ..., 'label' => ..., 'subtitle' => ...]]
    $normalizedOptions = collect($options)->map(function ($item, $key) {
        if (is_array($item)) {
            $val = $item['id'] ?? $item['value'] ?? $key;
            $lbl = $item['name'] ?? $item['label'] ?? $item['title'] ?? (string) $val;
            $sub = $item['subtitle'] ?? $item['code'] ?? null;
            return ['value' => (string) $val, 'label' => (string) $lbl, 'subtitle' => $sub ? (string) $sub : null];
        } elseif (is_object($item)) {
            $val = $item->id ?? $item->value ?? $key;
            $lbl = $item->name ?? $item->label ?? $item->title ?? (string) $val;
            $sub = $item->subtitle ?? $item->code ?? null;
            return ['value' => (string) $val, 'label' => (string) $lbl, 'subtitle' => $sub ? (string) $sub : null];
        } else {
            return ['value' => (string) $key, 'label' => (string) $item, 'subtitle' => null];
        }
    })->values()->all();
@endphp

<div
    x-data="{
        open: false,
        search: '',
        @if($wireModelValue)
            selected: @entangle($attributes->wire('model')),
        @else
            selected: @js($value),
        @endif
        initialWasNull: false,
        options: @js($normalizedOptions),
        clearable: {{ $clearable ? 'true' : 'false' }},
        disabled: {{ $disabled ? 'true' : 'false' }},

        init() {
            if (this.selected === null || this.selected === undefined) {
                this.initialWasNull = true;
            }
        },

        get hasSelection() {
            return this.selected !== null && this.selected !== undefined && this.selected !== '';
        },

        get filteredOptions() {
            if (!this.search || !this.search.trim()) return this.options;
            const q = this.search.toLowerCase().trim();
            return this.options.filter(opt => 
                (opt.label && opt.label.toLowerCase().includes(q)) ||
                (opt.subtitle && opt.subtitle.toLowerCase().includes(q))
            );
        },

        get selectedLabel() {
            if (!this.hasSelection) {
                return '{{ $placeholder }}';
            }
            const found = this.options.find(opt => String(opt.value) === String(this.selected));
            return found ? found.label : '{{ $placeholder }}';
        },

        select(val) {
            if (this.clearable && String(this.selected) === String(val)) {
                this.clear();
                this.open = false;
                return;
            }
            this.selected = val;
            this.open = false;
            this.search = '';
            this.$dispatch('input', val);
            this.$dispatch('change', val);
        },

        clear() {
            if (this.disabled) return;
            const resetVal = this.initialWasNull ? null : '';
            this.selected = resetVal;
            this.search = '';
            this.$dispatch('input', resetVal);
            this.$dispatch('change', resetVal);
        },

        onOpen() {
            if (this.disabled) return;
            this.open = !this.open;
            if (this.open) {
                this.search = '';
                this.$nextTick(() => {
                    if (this.$refs.searchInput) this.$refs.searchInput.focus();
                });
            }
        }
    }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative w-full {{ $class }}"
>
    @if($name)
        <input type="hidden" name="{{ $name }}" :value="hasSelection ? selected : ''">
    @endif

    <!-- TRIGGER BUTTON -->
    <div
        role="button"
        tabindex="0"
        @click="onOpen()"
        @keydown.enter.prevent="onOpen()"
        @keydown.space.prevent="onOpen()"
        :class="{ 'opacity-60 cursor-not-allowed pointer-events-none': disabled }"
        class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition flex items-center justify-between text-left shadow-2xs cursor-pointer select-none {{ $buttonClass }}"
    >
        <span class="truncate pr-2" :class="{ 'text-slate-400 font-normal': !hasSelection, 'text-slate-900 font-bold': hasSelection }" x-text="selectedLabel">
            {{ $placeholder }}
        </span>

        <div class="flex items-center gap-1.5 shrink-0 ml-auto">
            <!-- CLEAR BUTTON (X) -->
            <button
                type="button"
                x-show="clearable && hasSelection && !disabled"
                x-cloak
                @click.stop="clear()"
                title="Clear selection"
                class="w-5 h-5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- CHEVRON ICON -->
            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 pointer-events-none" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
            </svg>
        </div>
    </div>

    <!-- DROPDOWN PANEL -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 right-0 mt-1.5 z-50 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 space-y-1.5"
    >
        <!-- SEARCH INPUT -->
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            <input
                type="text"
                x-ref="searchInput"
                x-model="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-pp-500 transition"
                @keydown.enter.prevent="if (filteredOptions.length > 0) select(filteredOptions[0].value)"
            />
            <button
                type="button"
                x-show="search.length > 0"
                @click="search = ''; $refs.searchInput.focus()"
                class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 text-xs"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- CLEAR / RESET OPTION IN DROPDOWN -->
        <template x-if="clearable && hasSelection && (!search || '{{ strtolower(addslashes($placeholder)) }}'.includes(search.toLowerCase().trim()) || 'clear'.includes(search.toLowerCase().trim()))">
            <div class="border-b border-slate-100 pb-1 mb-1">
                <button
                    type="button"
                    @click="clear(); open = false;"
                    class="w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between text-slate-500 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer font-medium group/clear"
                >
                    <span class="flex items-center gap-2 truncate">
                        <i class="fas fa-times-circle text-xs text-rose-500 group-hover/clear:scale-110 transition-transform"></i>
                        <span class="truncate">
                            <span>Clear selection</span>
                            <span class="text-slate-400 font-normal ml-1">({{ $placeholder }})</span>
                        </span>
                    </span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Reset</span>
                </button>
            </div>
        </template>

        <!-- OPTIONS LIST -->
        <div class="max-h-56 overflow-y-auto space-y-0.5 divide-y divide-transparent pr-1">
            <template x-for="opt in filteredOptions" :key="opt.value">
                <button
                    type="button"
                    @click="select(opt.value)"
                    class="w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between transition cursor-pointer"
                    :class="String(selected) === String(opt.value) ? 'bg-pp-50 text-pp-800 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium'"
                >
                    <span class="truncate pr-2">
                        <span x-text="opt.label"></span>
                        <template x-if="opt.subtitle">
                            <span class="ml-1.5 text-[10px] text-slate-400 font-normal" x-text="'(' + opt.subtitle + ')'"></span>
                        </template>
                    </span>

                    <template x-if="String(selected) === String(opt.value)">
                        <i class="fas fa-check text-pp-600 text-[10px] shrink-0"></i>
                    </template>
                </button>
            </template>

            <!-- EMPTY RESULTS -->
            <div x-show="filteredOptions.length === 0" class="py-4 text-center text-slate-400 text-xs font-medium">
                No matching options found
            </div>
        </div>
    </div>
</div>
