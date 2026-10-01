<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminCategories extends Component
{
    public ?int $selectedCategoryId = null;

    public string $categoryName = '';

    public string $categorySlug = '';

    public bool $showAttachModal = false;

    public ?int $selectedFieldToAttach = null;

    public int $attachSortOrder = 0;

    public function mount(): void
    {
        $this->selectedCategoryId = Category::query()->where('is_listing', true)->orderBy('name', 'asc')->value('id');
        $this->loadCategoryMeta();
    }

    public function updatedSelectedCategoryId(): void
    {
        $this->closeAttachModal();
        $this->loadCategoryMeta();
    }

    public function loadCategoryMeta(): void
    {
        $this->categoryName = '';
        $this->categorySlug = '';

        if (! $this->selectedCategoryId) {
            return;
        }

        $category = Category::query()->find($this->selectedCategoryId);
        if (! $category) {
            return;
        }

        $this->categoryName = $category->name;
        $this->categorySlug = (string) ($category->slug ?? '');
    }

    public function saveCategoryMeta(): void
    {
        $this->validate([
            'selectedCategoryId' => ['required', 'integer', 'exists:categories,id'],
            'categoryName' => ['required', 'string', 'max:255'],
        ]);

        $category = Category::query()->findOrFail($this->selectedCategoryId);
        $category->update(['name' => $this->categoryName]);

        session()->flash('status', 'Category name saved.');
    }

    public function openAttachModal(): void
    {
        $this->reset(['selectedFieldToAttach', 'attachSortOrder']);
        $this->resetErrorBag();
        $this->showAttachModal = true;
    }

    public function closeAttachModal(): void
    {
        $this->showAttachModal = false;
        $this->reset(['selectedFieldToAttach', 'attachSortOrder']);
    }

    public function attachField(): void
    {
        $this->validate([
            'selectedCategoryId' => ['required', 'integer', 'exists:categories,id'],
            'selectedFieldToAttach' => ['required', 'integer', 'exists:category_fields,id'],
            'attachSortOrder' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $category = Category::query()->findOrFail($this->selectedCategoryId);
        $field = CategoryField::query()->findOrFail($this->selectedFieldToAttach);

        DB::table('category_settings')->updateOrInsert(
            ['category_id' => $category->id, 'category_field_id' => $field->id],
            ['sort_order' => $this->attachSortOrder]
        );

        session()->flash('status', "Field “{$field->label}” attached to category.");
        $this->closeAttachModal();
    }

    public function detachField(int $fieldId): void
    {
        DB::table('category_settings')
            ->where('category_id', $this->selectedCategoryId)
            ->where('category_field_id', $fieldId)
            ->delete();

        session()->flash('status', 'Field detached from category.');
    }

    public function render(): View
    {
        $categories = Category::query()
            ->where('is_listing', true)
            ->withCount('fields')
            ->orderBy('name', 'asc')
            ->get();

        $selectedCategory = $this->selectedCategoryId
            ? Category::query()->with(['fields' => fn ($q) => $q->orderBy('category_settings.sort_order')])->find($this->selectedCategoryId)
            : null;

        $attachedFieldIds = $selectedCategory ? $selectedCategory->fields->pluck('id')->all() : [];

        $availableFields = CategoryField::query()
            ->whereNotIn('id', $attachedFieldIds)
            ->orderBy('label')
            ->get();

        return view('livewire.admin.settings.admin-categories', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'availableFields' => $availableFields,
        ]);
    }
}
