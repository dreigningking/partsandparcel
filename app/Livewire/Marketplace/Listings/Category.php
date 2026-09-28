<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Brand;
use App\Models\Category as CategoryModel;
use App\Models\DeviceModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Category extends Component
{
    public $title = "Electronics & Devices — Parts & Parcel";

    #[Url(as: 'tab', keep: true)]
    public $tab = 'complete';

    #[Url(as: 'cat', keep: true)]
    public $cat = '';

    #[Url(as: 'brand', keep: true)]
    public $brand = '';

    #[Url(as: 'model', keep: true)]
    public $model = '';

    #[Url(as: 'q', keep: true)]
    public $q = '';

    public function switchTab($tabName)
    {
        if (in_array($tabName, ['complete', 'parts', 'scrap', 'community', 'requests'])) {
            $this->tab = ($tabName === 'community') ? 'requests' : $tabName;
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
        $activeCategory = $this->cat ? CategoryModel::where('slug', $this->cat)->first() : null;
        $activeBrand = $this->brand ? Brand::where('slug', $this->brand)->first() : null;
        $activeModel = $this->model ? DeviceModel::where('slug', $this->model)->first() : null;

        return view('livewire.marketplace.listings.category', [
            'activeCategory' => $activeCategory,
            'activeBrand' => $activeBrand,
            'activeModel' => $activeModel,
        ]);
    }
}
