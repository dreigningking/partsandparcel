<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use Livewire\Component;

class HeaderSearch extends Component
{
    public string $query = '';

    public function submitSearch()
    {
        $q = trim($this->query);
        if ($q !== '') {
            return redirect()->route('search', ['q' => $q]);
        }

        return redirect()->route('category');
    }

    public function getSuggestionsProperty(): array
    {
        $q = trim($this->query);
        if (strlen($q) < 2) {
            return [];
        }

        $results = [];

        // 1. Device Models (e.g. iPhone 11 - Apple / Phones)
        $models = DeviceModel::with(['category', 'brand'])
            ->where('name', 'like', "%{$q}%")
            ->take(6)
            ->get();

        foreach ($models as $model) {
            $brandName = $model->brand?->name;
            $catName = $model->category?->name;

            $labelParts = [];
            if ($brandName) {
                $labelParts[] = $brandName;
            }
            if ($catName) {
                $labelParts[] = $catName;
            }
            $suffix = count($labelParts) > 0 ? implode(' / ', $labelParts) : '';

            $results[] = [
                'type' => 'model',
                'title' => $model->name,
                'subtitle' => $suffix ? "{$model->name} — {$suffix}" : $model->name,
                'url' => route('category', array_filter([
                    'cat' => $model->category?->slug,
                    'brand' => $model->brand?->slug,
                    'model' => $model->slug,
                ])),
            ];
        }

        // 2. Categories & Subcategories
        $categories = Category::with('parent')
            ->where('name', 'like', "%{$q}%")
            ->take(4)
            ->get();

        foreach ($categories as $cat) {
            $parentName = $cat->parent?->name;
            $subtitle = $parentName ? "{$cat->name} — {$parentName}" : $cat->name;

            $results[] = [
                'type' => 'category',
                'title' => $cat->name,
                'subtitle' => $subtitle,
                'url' => route('category', ['cat' => $cat->slug]),
            ];
        }

        // 3. Brands
        $brands = Brand::where('name', 'like', "%{$q}%")
            ->take(4)
            ->get();

        foreach ($brands as $brand) {
            $results[] = [
                'type' => 'brand',
                'title' => $brand->name,
                'subtitle' => "{$brand->name} (Brand)",
                'url' => route('category', ['brand' => $brand->slug]),
            ];
        }

        // 4. Exact Device / Item Names
        $items = Item::with(['deviceModel.category', 'deviceModel.brand'])
            ->whereNotNull('name')
            ->where('name', 'like', "%{$q}%")
            ->take(4)
            ->get();

        foreach ($items as $item) {
            $brandName = $item->deviceModel?->brand?->name;
            $catName = $item->deviceModel?->category?->name;
            $labelParts = [];
            if ($brandName) {
                $labelParts[] = $brandName;
            }
            if ($catName) {
                $labelParts[] = $catName;
            }
            $suffix = count($labelParts) > 0 ? implode(' / ', $labelParts) : '';

            $results[] = [
                'type' => 'device',
                'title' => $item->name,
                'subtitle' => $suffix ? "{$item->name} — {$suffix}" : $item->name,
                'url' => route('category', array_filter([
                    'cat' => $item->deviceModel?->category?->slug,
                    'brand' => $item->deviceModel?->brand?->slug,
                    'q' => $item->name,
                ])),
            ];
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.header-search', [
            'suggestions' => $this->suggestions,
        ]);
    }
}
