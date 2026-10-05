@props([
    'options' => [],
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search options...',
    'name' => null,
    'id' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
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
        options: @js($normalizedOptions),
        get filteredOptions() {
            if (!this.search || !this.search.trim()) return this.options;
            const q = this.search.toLowerCase().trim();
            return this.options.filter(opt => 
                (opt.label && opt.label.toLowerCase().includes(q)) ||
                (opt.subtitle && opt.subtitle.toLowerCase().includes(q))
            );
        },
        get selectedLabel() {
            if (this.selected === null || this.selected === undefined || this.selected === '') {
                return '{{ $placeholder }}';
            }
            const found = this.options.find(opt => String(opt.value) === String(this.selected));
            return found ? found.label : '{{ $placeholder }}';
        },
        select(val) {
            this.selected = val;
            this.open = false;
            this.search = '';
            this.$dispatch('input', val);
            this.$dispatch('change', val);
        },
        onOpen() {
            if ({{ $disabled ? 'true' : 'false' }}) return;
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
        <input type="hidden" name="{{ $name }}" :value="selected">
    @endif

    <!-- TRIGGER BUTTON -->
    <button
        type="button"
        @click="onOpen()"
        :disabled="{{ $disabled ? 'true' : 'false' }}"
        class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-800 outline-none focus:border-pp-500 transition flex items-center justify-between text-left shadow-2xs cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed {{ $buttonClass }}"
    >
        <span class="truncate" :class="{ 'text-slate-400 font-normal': selected === null || selected === undefined || selected === '', 'text-slate-900 font-bold': selected !== null && selected !== undefined && selected !== '' }" x-text="selectedLabel">
            {{ $placeholder }}
        </span>

        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
        </svg>
    </button>

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
