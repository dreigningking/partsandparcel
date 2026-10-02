<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Brand;
use App\Models\Category as CategoryModel;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Category extends Component
{
    public $title = "Marketplace — Parts & Parcel";

    #[Url(as: 'tab', keep: true)]
    public $tab = 'devices';

    #[Url(as: 'cat', keep: true)]
    public $cat = '';

    #[Url(as: 'brand', keep: true)]
    public $brand = '';

    #[Url(as: 'model', keep: true)]
    public $model = '';

    #[Url(as: 'q', keep: true)]
    public $q = '';

    public function mount()
    {
        $this->normalizeTab();
    }

    protected function normalizeTab()
    {
        if ($this->tab === 'complete') {
            $this->tab = 'devices';
        } elseif ($this->tab === 'scrap') {
            $this->tab = 'scraps';
        } elseif ($this->tab === 'community') {
            $this->tab = 'requests';
        }
    }

    public function switchTab($tabName)
    {
        if ($tabName === 'complete') $tabName = 'devices';
        if ($tabName === 'scrap') $tabName = 'scraps';
        if ($tabName === 'community') $tabName = 'requests';

        if (in_array($tabName, ['devices', 'parts', 'scraps', 'requests'])) {
            $this->tab = $tabName;
        }
    }

    public function clearFilter($filterName)
    {
        if ($filterName === 'cat') {
            $this->cat = '';
        }
        if ($filterName === 'brand') {
            $this->brand = '';
        }
        if ($filterName === 'model') {
            $this->model = '';
        }
        if ($filterName === 'q') {
            $this->q = '';
        }
    }

    public function clearAllFilters()
    {
        $this->cat = '';
        $this->brand = '';
        $this->model = '';
        $this->q = '';
    }

    public function render()
    {
        $this->normalizeTab();

        $activeCategory = $this->cat ? CategoryModel::where('slug', $this->cat)->first() : null;
        $activeBrand = $this->brand ? Brand::where('slug', $this->brand)->first() : null;
        $activeModel = $this->model ? DeviceModel::where('slug', $this->model)->first() : null;

        $catIds = collect();
        if ($activeCategory) {
            $catIds = $activeCategory->children()->pluck('id')->push($activeCategory->id);
        }

        $brandId = $activeBrand?->id;
        $modelId = $activeModel?->id;
        $searchTerm = trim($this->q);

        // Calculate count for devices (item_type = whole)
        $devicesCount = Listing::where('is_published', true)
            ->where('is_active', true)
            ->whereHas('item', function ($q) use ($catIds, $brandId, $modelId, $searchTerm) {
                $q->where('item_type', 'whole');
                if ($catIds->isNotEmpty()) {
                    $q->whereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
                }
                if ($brandId) {
                    $q->whereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
                }
                if ($modelId) {
                    $q->where('model_id', $modelId);
                }
                if ($searchTerm !== '') {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$searchTerm}%")->orWhere('description', 'like', "%{$searchTerm}%"));
                }
            })->count();

        // Calculate count for parts (item_type = part or parts)
        $partsCount = Listing::where('is_published', true)
            ->where('is_active', true)
            ->whereHas('item', function ($q) use ($catIds, $brandId, $modelId, $searchTerm) {
                $q->whereIn('item_type', ['part', 'parts']);
                if ($catIds->isNotEmpty()) {
                    $q->whereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
                }
                if ($brandId) {
                    $q->whereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
                }
                if ($modelId) {
                    $q->where('model_id', $modelId);
                }
                if ($searchTerm !== '') {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$searchTerm}%")->orWhere('description', 'like', "%{$searchTerm}%"));
                }
            })->count();

        // Calculate count for scraps (item_type = scrap)
        $scrapsCount = Listing::where('is_published', true)
            ->where('is_active', true)
            ->whereHas('item', function ($q) use ($catIds, $brandId, $modelId, $searchTerm) {
                $q->where('item_type', 'scrap');
                if ($catIds->isNotEmpty()) {
                    $q->whereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
                }
                if ($brandId) {
                    $q->whereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
                }
                if ($modelId) {
                    $q->where('model_id', $modelId);
                }
                if ($searchTerm !== '') {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$searchTerm}%")->orWhere('description', 'like', "%{$searchTerm}%"));
                }
            })->count();

        // Calculate count for requests (discussions)
        $requestsQuery = Discussion::query();
        if ($catIds->isNotEmpty()) {
            $requestsQuery->where(function ($q) use ($catIds) {
                $q->whereIn('category_id', $catIds)
                  ->orWhereHas('deviceModel', fn($m) => $m->whereIn('category_id', $catIds));
            });
        }
        if ($brandId) {
            $requestsQuery->where(function ($q) use ($brandId) {
                $q->where('brand_id', $brandId)
                  ->orWhereHas('deviceModel', fn($m) => $m->where('brand_id', $brandId));
            });
        }
        if ($modelId) {
            $requestsQuery->where('model_id', $modelId);
        }
        if ($searchTerm !== '') {
            $requestsQuery->where(fn($q) => $q->where('title', 'like', "%{$searchTerm}%")->orWhere('body', 'like', "%{$searchTerm}%"));
        }
        $requestsCount = $requestsQuery->count();

        $activeSellersCount = Listing::where('is_published', true)->where('is_active', true)->distinct('user_id')->count('user_id');

        return view('livewire.marketplace.listings.category', [
            'activeCategory' => $activeCategory,
            'activeBrand' => $activeBrand,
            'activeModel' => $activeModel,
            'devicesCount' => $devicesCount,
            'partsCount' => $partsCount,
            'scrapsCount' => $scrapsCount,
            'requestsCount' => $requestsCount,
            'activeSellersCount' => $activeSellersCount,
        ]);
    }
}
