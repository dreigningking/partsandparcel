<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Catalog: Brands, Categories & Device Models — Admin Control Center')]
class AdminCategories extends Component
{
    use WithPagination;

    // Active Section / Tab ('brands', 'categories', 'models')
    public string $activeTab = 'brands';

    // -------------------------------------------------------------
    // BRANDS STATE
    // -------------------------------------------------------------
    public string $brandSearch = '';
    public bool $showBrandModal = false;
    public ?int $editingBrandId = null;
    public string $brandName = '';
    public string $brandSlug = '';
    public bool $brandIsActive = true;

    // -------------------------------------------------------------
    // CATEGORIES STATE
    // -------------------------------------------------------------
    public string $categorySearch = '';
    public ?int $categoryFilterParent = null;
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categorySlug = '';
    public ?int $categoryParentId = null;
    public bool $categoryIsActive = true;

    // -------------------------------------------------------------
    // DEVICE MODELS STATE
    // -------------------------------------------------------------
    public string $modelSearch = '';
    public ?int $modelBrandFilter = null;
    public ?int $modelCategoryFilter = null;
    public bool $showModelModal = false;
    public ?int $editingModelId = null;
    public string $modelName = '';
    public string $modelSlug = '';
    public ?int $modelBrandId = null;
    public ?int $modelCategoryId = null;

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['brands', 'categories', 'models']) ? $tab : 'brands';
    }

    // -------------------------------------------------------------
    // BRAND ACTIONS
    // -------------------------------------------------------------
    public function updatedBrandSearch(): void
    {
        $this->resetPage('brandsPage');
    }

    public function updatedBrandName($value): void
    {
        if (! $this->editingBrandId) {
            $this->brandSlug = Str::slug($value);
        }
    }

    public function openCreateBrandModal(): void
    {
        $this->reset(['editingBrandId', 'brandName', 'brandSlug']);
        $this->brandIsActive = true;
        $this->resetErrorBag();
        $this->showBrandModal = true;
    }

    public function openEditBrandModal(int $id): void
    {
        $this->resetErrorBag();
        $brand = Brand::query()->findOrFail($id);
        $this->editingBrandId = $brand->id;
        $this->brandName = $brand->name;
        $this->brandSlug = $brand->slug;
        $this->brandIsActive = (bool) $brand->is_active;
        $this->showBrandModal = true;
    }

    public function closeBrandModal(): void
    {
        $this->showBrandModal = false;
        $this->reset(['editingBrandId', 'brandName', 'brandSlug']);
        $this->resetErrorBag();
    }

    public function saveBrand(): void
    {
        $slugRule = 'required|string|max:100|unique:brands,slug,' . ($this->editingBrandId ?: 'NULL') . ',id';

        $this->validate([
            'brandName' => 'required|string|max:100',
            'brandSlug' => $slugRule,
            'brandIsActive' => 'boolean',
        ]);

        Brand::updateOrCreate(
            ['id' => $this->editingBrandId],
            [
                'name' => trim($this->brandName),
                'slug' => Str::slug($this->brandSlug ?: $this->brandName),
                'is_active' => $this->brandIsActive,
            ]
        );

        $action = $this->editingBrandId ? 'updated' : 'created';
        session()->flash('status', "Brand “{$this->brandName}” successfully {$action}.");

        $this->closeBrandModal();
    }

    public function toggleBrandStatus(int $id): void
    {
        $brand = Brand::query()->findOrFail($id);
        $brand->is_active = ! $brand->is_active;
        $brand->save();

        session()->flash('status', "Brand “{$brand->name}” status changed to " . ($brand->is_active ? 'Active' : 'Inactive') . '.');
    }

    public function deleteBrand(int $id): void
    {
        $brand = Brand::withCount('deviceModels')->findOrFail($id);

        if ($brand->device_models_count > 0) {
            session()->flash('error', "Cannot delete brand “{$brand->name}” because it has {$brand->device_models_count} associated device models.");
            return;
        }

        $brand->delete();
        session()->flash('status', "Brand “{$brand->name}” deleted successfully.");
    }

    // -------------------------------------------------------------
    // CATEGORY ACTIONS
    // -------------------------------------------------------------
    public function updatedCategorySearch(): void
    {
        $this->resetPage('categoriesPage');
    }

    public function updatedCategoryFilterParent(): void
    {
        $this->resetPage('categoriesPage');
    }

    public function updatedCategoryName($value): void
    {
        if (! $this->editingCategoryId) {
            $this->categorySlug = Str::slug($value);
        }
    }

    public function openCreateCategoryModal(): void
    {
        $this->reset(['editingCategoryId', 'categoryName', 'categorySlug', 'categoryParentId']);
        $this->categoryIsActive = true;
        $this->resetErrorBag();
        $this->showCategoryModal = true;
    }

    public function openEditCategoryModal(int $id): void
    {
        $this->resetErrorBag();
        $category = Category::query()->findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categorySlug = $category->slug;
        $this->categoryParentId = $category->parent_id;
        $this->categoryIsActive = (bool) $category->is_active;
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->reset(['editingCategoryId', 'categoryName', 'categorySlug', 'categoryParentId']);
        $this->resetErrorBag();
    }

    public function saveCategory(): void
    {
        $slugRule = 'required|string|max:100|unique:categories,slug,' . ($this->editingCategoryId ?: 'NULL') . ',id';

        $this->validate([
            'categoryName' => 'required|string|max:100',
            'categorySlug' => $slugRule,
            'categoryParentId' => 'nullable|integer|exists:categories,id',
            'categoryIsActive' => 'boolean',
        ]);

        if ($this->editingCategoryId && (int) $this->categoryParentId === (int) $this->editingCategoryId) {
            $this->addError('categoryParentId', 'A category cannot be its own parent.');
            return;
        }

        Category::updateOrCreate(
            ['id' => $this->editingCategoryId],
            [
                'name' => trim($this->categoryName),
                'slug' => Str::slug($this->categorySlug ?: $this->categoryName),
                'parent_id' => $this->categoryParentId ?: null,
                'is_active' => $this->categoryIsActive,
            ]
        );

        $action = $this->editingCategoryId ? 'updated' : 'created';
        session()->flash('status', "Category “{$this->categoryName}” successfully {$action}.");

        $this->closeCategoryModal();
    }

    public function toggleCategoryStatus(int $id): void
    {
        $category = Category::query()->findOrFail($id);
        $category->is_active = ! $category->is_active;
        $category->save();

        session()->flash('status', "Category “{$category->name}” status changed to " . ($category->is_active ? 'Active' : 'Inactive') . '.');
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::withCount(['children', 'deviceModels'])->findOrFail($id);

        if ($category->children_count > 0) {
            session()->flash('error', "Cannot delete category “{$category->name}” because it has {$category->children_count} sub-categories.");
            return;
        }

        if ($category->device_models_count > 0) {
            session()->flash('error', "Cannot delete category “{$category->name}” because it is linked to {$category->device_models_count} device models.");
            return;
        }

        $category->delete();
        session()->flash('status', "Category “{$category->name}” deleted successfully.");
    }

    // -------------------------------------------------------------
    // DEVICE MODEL ACTIONS
    // -------------------------------------------------------------
    public function updatedModelSearch(): void
    {
        $this->resetPage('modelsPage');
    }

    public function updatedModelBrandFilter(): void
    {
        $this->resetPage('modelsPage');
    }

    public function updatedModelCategoryFilter(): void
    {
        $this->resetPage('modelsPage');
    }

    public function updatedModelName($value): void
    {
        if (! $this->editingModelId) {
            $this->modelSlug = Str::slug($value);
        }
    }

    public function openCreateModelModal(): void
    {
        $this->reset(['editingModelId', 'modelName', 'modelSlug', 'modelBrandId', 'modelCategoryId']);
        $this->resetErrorBag();
        $this->showModelModal = true;
    }

    public function openEditModelModal(int $id): void
    {
        $this->resetErrorBag();
        $model = DeviceModel::query()->findOrFail($id);
        $this->editingModelId = $model->id;
        $this->modelName = $model->name;
        $this->modelSlug = $model->slug;
        $this->modelBrandId = $model->brand_id;
        $this->modelCategoryId = $model->category_id;
        $this->showModelModal = true;
    }

    public function closeModelModal(): void
    {
        $this->showModelModal = false;
        $this->reset(['editingModelId', 'modelName', 'modelSlug', 'modelBrandId', 'modelCategoryId']);
        $this->resetErrorBag();
    }

    public function saveModel(): void
    {
        $this->validate([
            'modelName' => 'required|string|max:120',
            'modelSlug' => 'required|string|max:120',
            'modelBrandId' => 'required|integer|exists:brands,id',
            'modelCategoryId' => 'required|integer|exists:categories,id',
        ], [
            'modelBrandId.required' => 'Please select a Brand for this model.',
            'modelCategoryId.required' => 'Please select a Category for this model.',
        ]);

        $slug = Str::slug($this->modelSlug ?: $this->modelName);

        // Check unique constraint for [brand_id, slug]
        $exists = DeviceModel::query()
            ->where('brand_id', $this->modelBrandId)
            ->where('slug', $slug)
            ->when($this->editingModelId, fn ($q) => $q->where('id', '!=', $this->editingModelId))
            ->exists();

        if ($exists) {
            $this->addError('modelSlug', 'A model with this slug already exists for the selected brand.');
            return;
        }

        DeviceModel::updateOrCreate(
            ['id' => $this->editingModelId],
            [
                'name' => trim($this->modelName),
                'slug' => $slug,
                'brand_id' => $this->modelBrandId,
                'category_id' => $this->modelCategoryId,
            ]
        );

        $action = $this->editingModelId ? 'updated' : 'created';
        session()->flash('status', "Device model “{$this->modelName}” successfully {$action}.");

        $this->closeModelModal();
    }

    public function deleteModel(int $id): void
    {
        $model = DeviceModel::withCount('items')->findOrFail($id);

        if ($model->items_count > 0) {
            session()->flash('error', "Cannot delete device model “{$model->name}” because {$model->items_count} inventory items or listings reference it.");
            return;
        }

        $model->delete();
        session()->flash('status', "Device model “{$model->name}” deleted successfully.");
    }

    public function render(): View
    {
        // 1. Brands Query
        $brands = Brand::query()
            ->withCount('deviceModels')
            ->when($this->brandSearch, fn ($q) => $q->where('name', 'like', '%' . $this->brandSearch . '%')->orWhere('slug', 'like', '%' . $this->brandSearch . '%'))
            ->orderBy('name', 'asc')
            ->paginate(15, ['*'], 'brandsPage');

        // 2. Categories Query
        $categories = Category::query()
            ->with('parent')
            ->withCount(['deviceModels', 'children'])
            ->when($this->categorySearch, fn ($q) => $q->where('name', 'like', '%' . $this->categorySearch . '%')->orWhere('slug', 'like', '%' . $this->categorySearch . '%'))
            ->when($this->categoryFilterParent, fn ($q) => $q->where('parent_id', $this->categoryFilterParent))
            ->orderBy('name', 'asc')
            ->paginate(15, ['*'], 'categoriesPage');

        // 3. Models Query
        $models = DeviceModel::query()
            ->with(['brand', 'category'])
            ->withCount('items')
            ->when($this->modelSearch, fn ($q) => $q->where('name', 'like', '%' . $this->modelSearch . '%')->orWhere('slug', 'like', '%' . $this->modelSearch . '%'))
            ->when($this->modelBrandFilter, fn ($q) => $q->where('brand_id', $this->modelBrandFilter))
            ->when($this->modelCategoryFilter, fn ($q) => $q->where('category_id', $this->modelCategoryFilter))
            ->orderBy('name', 'asc')
            ->paginate(15, ['*'], 'modelsPage');

        // Static Lists for Dropdowns
        $allBrands = Brand::query()->where('is_active', true)->orderBy('name', 'asc')->get(['id', 'name']);
        $allCategories = Category::query()->where('is_active', true)->orderBy('name', 'asc')->get(['id', 'name', 'parent_id']);
        $parentCategories = Category::query()->whereNull('parent_id')->orderBy('name', 'asc')->get(['id', 'name']);

        // Overall Counts for Badge Display
        $brandsCount = Brand::count();
        $categoriesCount = Category::count();
        $modelsCount = DeviceModel::count();

        return view('livewire.admin.settings.admin-categories', [
            'brands' => $brands,
            'categories' => $categories,
            'models' => $models,
            'allBrands' => $allBrands,
            'allCategories' => $allCategories,
            'parentCategories' => $parentCategories,
            'brandsCount' => $brandsCount,
            'categoriesCount' => $categoriesCount,
            'modelsCount' => $modelsCount,
        ]);
    }
}
