<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Brand;
use App\Models\Category as CategoryModel;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use Livewire\Component;

class CategoryParts extends Component
{
    public $cat = '';
    public $brand = '';
    public $model = '';
    public $q = '';

    public string $search = '';
    public array $selectedBrands = [];
    public array $selectedComponentTypes = [];
    public array $selectedConditions = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
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
        $this->selectedComponentTypes = [];
        $this->selectedConditions = [];
        $this->minPrice = null;
        $this->maxPrice = null;
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

        $query = Listing::with(['item.deviceModel.category', 'item.deviceModel.brand', 'item.location', 'seller.primaryLocation', 'media', 'item.media'])
            ->where('is_published', true)
            ->where('is_active', true)
            ->whereHas('item', function ($q) use ($catIds, $brandId, $modelId, $searchTerm) {
                $q->whereIn('item_type', ['part', 'parts']);

                if ($catIds->isNotEmpty()) {
                    $q->whereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
                }

                if ($brandId) {
                    $q->whereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
                } elseif (!empty($this->selectedBrands)) {
                    $q->whereHas('deviceModel', fn($m) => $m->whereIn('brand_id', $this->selectedBrands));
                }

                if ($modelId) {
                    $q->where('model_id', $modelId);
                }

                if (!empty($this->selectedComponentTypes)) {
                    $q->where(function ($sub) {
                        foreach ($this->selectedComponentTypes as $type) {
                            $sub->orWhere('name', 'like', "%{$type}%")
                                ->orWhere('description', 'like', "%{$type}%");
                        }
                    });
                }

                if (!empty($this->selectedConditions)) {
                    $q->where(function ($sub) {
                        foreach ($this->selectedConditions as $cond) {
                            if ($cond === 'new') {
                                $sub->orWhereIn('condition_status', ['new', 'brand_new']);
                            } elseif ($cond === 'used') {
                                $sub->orWhereIn('condition_status', ['used', 'used_clean', 'used_excellent', 'tokunbo']);
                            } elseif ($cond === 'refurbished') {
                                $sub->orWhereIn('condition_status', ['refurbished', 'tested_working', 'tested_grade_a']);
                            } else {
                                $sub->orWhere('condition_status', $cond);
                            }
                        }
                    });
                }

                if ($searchTerm !== '') {
                    $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('description', 'like', "%{$searchTerm}%")
                            ->orWhereHas('deviceModel', fn($m) => $m->where('name', 'like', "%{$searchTerm}%"))
                            ->orWhereHas('deviceModel.brand', fn($b) => $b->where('name', 'like', "%{$searchTerm}%"));
                    });
                }
            });

        if ($this->minPrice !== null && $this->minPrice > 0) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice !== null && $this->maxPrice > 0) {
            $query->where('price', '<=', $this->maxPrice);
        }

        if ($this->selectedLocation !== '') {
            $loc = $this->selectedLocation;
            $query->whereHas('item.location', fn($l) => $l->where('city', 'like', "%{$loc}%")->orWhere('state', 'like', "%{$loc}%"));
        }

        // Sorting
        if ($this->sortBy === 'newest') {
            $query->latest();
        } elseif ($this->sortBy === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest('updated_at');
        }

        $listings = $query->get();

        // Available brands for filter sidebar
        $brandsQuery = Brand::query();
        if ($catIds->isNotEmpty()) {
            $brandsQuery->whereHas('deviceModels', fn($m) => $m->whereIn('category_id', $catIds));
        }
        $availableBrands = $brandsQuery->orderBy('name')->take(10)->get();

        $availableLocations = Location::whereNotNull('city')->distinct('city')->pluck('city')->filter();

        return view('livewire.marketplace.listings.category-parts', [
            'listings' => $listings,
            'totalCount' => $listings->count(),
            'activeCategory' => $activeCategory,
            'activeBrand' => $activeBrand,
            'availableBrands' => $availableBrands,
            'availableLocations' => $availableLocations,
        ]);
    }
}
