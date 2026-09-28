<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Brand;
use App\Models\Category as CategoryModel;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Location;
use Livewire\Component;

class CategoryRequests extends Component
{
    public $cat = '';
    public $brand = '';
    public $model = '';
    public $q = '';

    public string $search = '';
    public array $selectedBrands = [];
    public string $selectedType = '';
    public string $selectedLocation = '';
    public string $sortBy = 'relevance';

    public function mount($cat = '', $brand = '', $model = '', $q = '')
    {
        $this->cat = $cat;
        $this->brand = $brand;
        $this->model = $model;
        $this->q = $q;
        $this->search = $q;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedBrands = [];
        $this->selectedType = '';
        $this->selectedLocation = '';
        $this->sortBy = 'relevance';
    }

    public function render()
    {
        $activeCategory = $this->cat ? CategoryModel::where('slug', $this->cat)->first() : null;
        $activeBrand = $this->brand ? Brand::where('slug', $this->brand)->first() : null;
        $activeModel = $this->model ? DeviceModel::where('slug', $this->model)->first() : null;

        $catIds = collect();
        if ($activeCategory) {
            $catIds = $activeCategory->children()->pluck('id')->push($activeCategory->id);
        }

        $brandId = $activeBrand?->id;
        $modelId = $activeModel?->id;
        $searchTerm = trim($this->search !== '' ? $this->search : $this->q);

        $query = Discussion::with(['user', 'category', 'brand', 'deviceModel', 'location', 'responses'])
            ->withCount('responses');

        // Filter by Category
        if ($catIds->isNotEmpty()) {
            $query->where(function ($q) use ($catIds) {
                $q->whereIn('category_id', $catIds)
                  ->orWhereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
            });
        }

        // Filter by Brand
        if ($brandId) {
            $query->where(function ($q) use ($brandId) {
                $q->where('brand_id', $brandId)
                  ->orWhereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
            });
        } elseif (!empty($this->selectedBrands)) {
            $query->where(function ($q) {
                $q->whereIn('brand_id', $this->selectedBrands)
                  ->orWhereHas('deviceModel', fn($m) => $m->whereIn('brand_id', $this->selectedBrands));
            });
        }

        // Filter by Model
        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        // Filter by Type (device, service, advice)
        if ($this->selectedType !== '') {
            if ($this->selectedType === 'device') {
                $query->whereIn('type', ['device', 'item']);
            } else {
                $query->where('type', $this->selectedType);
            }
        }

        // Filter by Location
        if ($this->selectedLocation !== '') {
            $loc = $this->selectedLocation;
            $query->whereHas('location', fn($l) => $l->where('city', 'like', "%{$loc}%")->orWhere('state', 'like', "%{$loc}%"));
        }

        // Filter by Search Query
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%")
                  ->orWhereHas('deviceModel', fn($m) => $m->where('name', 'like', "%{$searchTerm}%"));
            });
        }

        // Sorting
        if ($this->sortBy === 'newest') {
            $query->latest();
        } elseif ($this->sortBy === 'offers') {
            $query->orderBy('responses_count', 'desc');
        } else {
            $query->latest('updated_at');
        }

        $discussions = $query->get();

        // Available brands for filter sidebar
        $brandsQuery = Brand::query();
        if ($catIds->isNotEmpty()) {
            $brandsQuery->whereHas('deviceModels', fn($m) => $m->whereIn('category_id', $catIds));
        }
        $availableBrands = $brandsQuery->orderBy('name')->take(10)->get();

        $availableLocations = Location::whereNotNull('city')->distinct('city')->pluck('city')->filter();

        return view('livewire.marketplace.listings.category-requests', [
            'discussions' => $discussions,
            'totalCount' => $discussions->count(),
            'activeCategory' => $activeCategory,
            'activeBrand' => $activeBrand,
            'availableBrands' => $availableBrands,
            'availableLocations' => $availableLocations,
        ]);
    }
}
