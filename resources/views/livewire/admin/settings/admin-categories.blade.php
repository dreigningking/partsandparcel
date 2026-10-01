<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pp-600 transition">Admin Console</a>
                <span>/</span>
                <span class="text-pp-700">Catalog Management</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Brands, Categories &amp; Device Models
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure marketplace taxonomies, manufacturers, hardware categories, and specific device models.
            </p>
        </div>

        <!-- PRIMARY ADD BUTTON DYNAMIC TO ACTIVE TAB -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
            @if ($activeTab === 'brands')
                <button
                    type="button"
                    wire:click="openCreateBrandModal"
                    class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-plus"></i> Add New Brand
                </button>
            @elseif ($activeTab === 'categories')
                <button
                    type="button"
                    wire:click="openCreateCategoryModal"
                    class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-plus"></i> Add New Category
                </button>
            @else
                <button
                    type="button"
                    wire:click="openCreateModelModal"
                    class="px-4 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-soft transition flex items-center gap-2 cursor-pointer"
                >
                    <i class="fas fa-plus"></i> Add New Device Model
                </button>
            @endif
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
            <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- TABBED NAVIGATION SELECTOR -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 overflow-x-auto custom-scrollbar">
        <button
            type="button"
            wire:click="setTab('brands')"
            class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'brands' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fas fa-trademark"></i>
            <span>1. Brands</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'brands' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ number_format($brandsCount) }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('categories')"
            class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'categories' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fas fa-sitemap"></i>
            <span>2. Categories</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'categories' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ number_format($categoriesCount) }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('models')"
            class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'models' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fas fa-laptop"></i>
            <span>3. Device Models</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'models' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ number_format($modelsCount) }}
            </span>
        </button>
    </div>

    <!-- ========================================================================================= -->
    <!-- SECTION 1: BRANDS -->
    <!-- ========================================================================================= -->
    @if ($activeTab === 'brands')
        <div class="space-y-4">
            <!-- TOOLBAR -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="brandSearch"
                        placeholder="Search brands by name or slug..."
                        class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                    />
                    @if ($brandSearch)
                        <button type="button" wire:click="$set('brandSearch', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>

                <div class="text-xs text-slate-500 font-bold">
                    Showing {{ $brands->firstItem() ?? 0 }}-{{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} brands
                </div>
            </div>

            <!-- BRANDS TABLE -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-soft">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 uppercase font-black text-[10px] tracking-wider">
                            <tr>
                                <th class="p-4">Brand</th>
                                <th class="p-4">Slug Identifier</th>
                                <th class="p-4 text-center">Device Models</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                            @forelse ($brands as $brand)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-pp-500 to-pp-700 text-white font-black text-xs grid place-items-center uppercase shadow-2xs">
                                                {{ substr($brand->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <b class="font-extrabold text-slate-900 dark:text-white block">{{ $brand->name }}</b>
                                                <span class="text-[10px] text-slate-400">ID: #{{ $brand->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-slate-600 dark:text-slate-400">
                                        {{ $brand->slug }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 font-extrabold text-[11px] text-slate-800 dark:text-slate-200">
                                            {{ $brand->device_models_count }} models
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button
                                            type="button"
                                            wire:click="toggleBrandStatus({{ $brand->id }})"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer {{ $brand->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                            title="Click to toggle status"
                                        >
                                            <i class="fas fa-circle text-[8px] mr-1"></i>
                                            {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="openEditBrandModal({{ $brand->id }})"
                                                class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center gap-1"
                                            >
                                                <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deleteBrand({{ $brand->id }})"
                                                wire:confirm="Are you sure you want to delete brand '{{ $brand->name }}'?"
                                                class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                title="Delete brand"
                                            >
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 font-medium">
                                        No brands match your search query. Click "+ Add New Brand" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($brands->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $brands->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- SECTION 2: CATEGORIES -->
    <!-- ========================================================================================= -->
    @if ($activeTab === 'categories')
        <div class="space-y-4">
            <!-- TOOLBAR -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    <div class="relative flex-1 max-w-md">
                        <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="categorySearch"
                            placeholder="Search categories by name or slug..."
                            class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @if ($categorySearch)
                            <button type="button" wire:click="$set('categorySearch', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>

                    <select wire:model.live="categoryFilterParent" class="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-pp-500 bg-white dark:bg-slate-800">
                        <option value="">-- All Hierarchy Levels --</option>
                        @foreach ($parentCategories as $pCat)
                            <option value="{{ $pCat->id }}">{{ $pCat->name }} (Children)</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-xs text-slate-500 font-bold">
                    Showing {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
                </div>
            </div>

            <!-- CATEGORIES TABLE -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-soft">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 uppercase font-black text-[10px] tracking-wider">
                            <tr>
                                <th class="p-4">Category Name</th>
                                <th class="p-4">Slug Identifier</th>
                                <th class="p-4">Hierarchy / Parent</th>
                                <th class="p-4 text-center">Device Models</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs grid place-items-center shadow-2xs">
                                                <i class="fas fa-folder text-pp-300"></i>
                                            </div>
                                            <div>
                                                <b class="font-extrabold text-slate-900 dark:text-white block">{{ $cat->name }}</b>
                                                @if ($cat->children_count > 0)
                                                    <span class="text-[10px] text-pp-600 font-bold">{{ $cat->children_count }} sub-categories</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-slate-600 dark:text-slate-400">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="p-4">
                                        @if ($cat->parent)
                                            <span class="px-2.5 py-1 rounded-lg bg-pp-50 text-pp-800 text-[11px] font-extrabold border border-pp-200">
                                                ↳ {{ $cat->parent->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">
                                                ROOT CATEGORY
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 font-extrabold text-[11px] text-slate-800 dark:text-slate-200">
                                            {{ $cat->device_models_count }} models
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button
                                            type="button"
                                            wire:click="toggleCategoryStatus({{ $cat->id }})"
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                            title="Click to toggle status"
                                        >
                                            <i class="fas fa-circle text-[8px] mr-1"></i>
                                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="openEditCategoryModal({{ $cat->id }})"
                                                class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center gap-1"
                                            >
                                                <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deleteCategory({{ $cat->id }})"
                                                wire:confirm="Are you sure you want to delete category '{{ $cat->name }}'?"
                                                class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                title="Delete category"
                                            >
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                        No categories match your search query. Click "+ Add New Category" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- SECTION 3: DEVICE MODELS -->
    <!-- ========================================================================================= -->
    @if ($activeTab === 'models')
        <div class="space-y-4">
            <!-- TOOLBAR WITH SEARCH AND DUAL FILTERS -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center justify-between gap-3 shadow-2xs">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    <div class="relative flex-1 max-w-sm">
                        <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="modelSearch"
                            placeholder="Search models (e.g. iPhone 15, EliteBook, Galaxy S23)..."
                            class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @if ($modelSearch)
                            <button type="button" wire:click="$set('modelSearch', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>

                    <!-- BRAND FILTER -->
                    <select wire:model.live="modelBrandFilter" class="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-pp-500 bg-white dark:bg-slate-800">
                        <option value="">-- All Brands ({{ count($allBrands) }}) --</option>
                        @foreach ($allBrands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>

                    <!-- CATEGORY FILTER -->
                    <select wire:model.live="modelCategoryFilter" class="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-pp-500 bg-white dark:bg-slate-800">
                        <option value="">-- All Categories ({{ count($allCategories) }}) --</option>
                        @foreach ($allCategories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-xs text-slate-500 font-bold">
                    Showing {{ $models->firstItem() ?? 0 }}-{{ $models->lastItem() ?? 0 }} of {{ $models->total() }} models
                </div>
            </div>

            <!-- MODELS TABLE -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-soft">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 uppercase font-black text-[10px] tracking-wider">
                            <tr>
                                <th class="p-4">Device Model</th>
                                <th class="p-4">Brand</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Slug</th>
                                <th class="p-4 text-center">Inventory Items</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                            @forelse ($models as $m)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                                    <td class="p-4">
                                        <b class="font-extrabold text-slate-900 dark:text-white block text-sm">{{ $m->name }}</b>
                                        <span class="text-[10px] text-slate-400">ID: #{{ $m->id }}</span>
                                    </td>
                                    <td class="p-4">
                                        @if ($m->brand)
                                            <span class="px-2.5 py-1 rounded-lg bg-pp-50 text-pp-800 text-[11px] font-extrabold border border-pp-200 inline-flex items-center gap-1.5">
                                                <i class="fas fa-tag text-[9px] text-pp-600"></i> {{ $m->brand->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">No Brand</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if ($m->category)
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-extrabold inline-flex items-center gap-1.5">
                                                <i class="fas fa-folder text-[9px] text-slate-500"></i> {{ $m->category->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">No Category</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-slate-600 dark:text-slate-400">
                                        {{ $m->slug }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 font-extrabold text-[11px] text-slate-800 dark:text-slate-200">
                                            {{ $m->items_count }} items
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="openEditModelModal({{ $m->id }})"
                                                class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center gap-1"
                                            >
                                                <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deleteModel({{ $m->id }})"
                                                wire:confirm="Are you sure you want to delete model '{{ $m->name }}'?"
                                                class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                title="Delete model"
                                            >
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                        No device models match your criteria. Click "+ Add New Device Model" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($models->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $models->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- MODAL 1: ADD / EDIT BRAND -->
    <!-- ========================================================================================= -->
    @if ($showBrandModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6 space-y-5 shadow-soft">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-trademark text-pp-600"></i>
                        {{ $editingBrandId ? 'Edit Brand' : 'Add New Brand' }}
                    </h3>
                    <button type="button" wire:click="closeBrandModal" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 text-sm cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveBrand" class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Brand Name</label>
                        <input
                            type="text"
                            wire:model.live="brandName"
                            placeholder="e.g. Apple, Dell, HP, Samsung, Lenovo"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('brandName') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">URL Slug</label>
                        <input
                            type="text"
                            wire:model="brandSlug"
                            placeholder="e.g. apple, dell, hp"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        <p class="text-[10px] text-slate-400">Used in URLs and filters (e.g. /search?brand=apple)</p>
                        @error('brandSlug') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="brandIsActive"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                            />
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">Active Brand (Visible in Marketplace Filters)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeBrandModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        >
                            <i class="fas fa-check"></i> {{ $editingBrandId ? 'Update Brand' : 'Save Brand' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- MODAL 2: ADD / EDIT CATEGORY -->
    <!-- ========================================================================================= -->
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6 space-y-5 shadow-soft">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-sitemap text-pp-600"></i>
                        {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                    </h3>
                    <button type="button" wire:click="closeCategoryModal" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 text-sm cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveCategory" class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Category Name</label>
                        <input
                            type="text"
                            wire:model.live="categoryName"
                            placeholder="e.g. Laptops, Smartphones, Screens, Motherboards"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('categoryName') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">URL Slug</label>
                        <input
                            type="text"
                            wire:model="categorySlug"
                            placeholder="e.g. laptops, smartphones"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('categorySlug') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Parent Category (Optional)</label>
                        <select
                            wire:model="categoryParentId"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition bg-white dark:bg-slate-800"
                        >
                            <option value="">-- None (Top-Level / Root Category) --</option>
                            @foreach ($allCategories as $catOption)
                                @if (! $editingCategoryId || $catOption->id !== $editingCategoryId)
                                    <option value="{{ $catOption->id }}">{{ $catOption->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400">Leave as None if this is a primary top-level department.</p>
                        @error('categoryParentId') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="categoryIsActive"
                                class="w-4 h-4 rounded text-pp-600 focus:ring-pp-500 border-slate-300"
                            />
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">Active Category</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeCategoryModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        >
                            <i class="fas fa-check"></i> {{ $editingCategoryId ? 'Update Category' : 'Save Category' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- MODAL 3: ADD / EDIT DEVICE MODEL -->
    <!-- ========================================================================================= -->
    @if ($showModelModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6 space-y-5 shadow-soft">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-laptop text-pp-600"></i>
                        {{ $editingModelId ? 'Edit Device Model' : 'Add New Device Model' }}
                    </h3>
                    <button type="button" wire:click="closeModelModal" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 text-sm cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveModel" class="space-y-4 text-xs">
                    <!-- MODEL NAME -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Model Name</label>
                        <input
                            type="text"
                            wire:model.live="modelName"
                            placeholder="e.g. EliteBook 840 G7, iPhone 14 Pro, ThinkPad T14"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('modelName') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- SELECT BRAND -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Brand / Manufacturer</label>
                        <select
                            wire:model="modelBrandId"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition bg-white dark:bg-slate-800"
                        >
                            <option value="">-- Select Brand --</option>
                            @foreach ($allBrands as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('modelBrandId') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- SELECT CATEGORY -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Category</label>
                        <select
                            wire:model="modelCategoryId"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white outline-none focus:border-pp-500 transition bg-white dark:bg-slate-800"
                        >
                            <option value="">-- Select Category --</option>
                            @foreach ($allCategories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('modelCategoryId') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <!-- SLUG -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300 block">Model Slug</label>
                        <input
                            type="text"
                            wire:model="modelSlug"
                            placeholder="e.g. elitebook-840-g7"
                            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono text-slate-900 dark:text-white outline-none focus:border-pp-500 transition"
                        />
                        @error('modelSlug') <span class="text-rose-600 text-[11px] font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeModelModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5"
                        >
                            <i class="fas fa-check"></i> {{ $editingModelId ? 'Update Model' : 'Save Model' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
