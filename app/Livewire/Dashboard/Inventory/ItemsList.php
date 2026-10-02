<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class ItemsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';
    public string $selectedBrand = '';
    public string $selectedCondition = '';
    public string $selectedLocation = '';
    public string $selectedStatus = '';
    public string $sortBy = 'date_desc';

    // Create Listing Modal Properties
    public bool $showCreateListingModal = false;
    public string $selectedAssetKey = '';
    public float $price = 0.00;
    public bool $is_negotiable = false;
    public int $quantity = 1;
    public bool $isQuantityDisabled = false;
    public int $warranty_period_days = 0;
    public bool $is_warranty_negotiable = false;
    public string $warranty_terms = '';
    public bool $allow_shipping = false;
    public ?int $location_id = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedBrand()
    {
        $this->resetPage();
    }

    public function updatingSelectedCondition()
    {
        $this->resetPage();
    }

    public function updatingSelectedLocation()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedCategory', 'selectedBrand', 'selectedCondition', 'selectedLocation', 'selectedStatus']);
        $this->sortBy = 'date_desc';
        $this->resetPage();
    }

    public function openCreateListingModal(?int $itemId = null)
    {
        $user = Auth::user();
        $defaultLoc = $user?->locations()->where('is_default', true)->first() ?? $user?->locations()->first();
        $this->location_id = $defaultLoc?->id;

        if ($itemId) {
            $this->selectedAssetKey = 'item_' . $itemId;
            $this->isQuantityDisabled = false;
        } else {
            $this->selectedAssetKey = '';
            $this->isQuantityDisabled = false;
        }

        $this->price = 0.00;
        $this->is_negotiable = false;
        $this->quantity = 1;
        $this->warranty_period_days = 0;
        $this->is_warranty_negotiable = false;
        $this->warranty_terms = '';
        $this->allow_shipping = false;
        $this->showCreateListingModal = true;
    }

    public function closeCreateListingModal()
    {
        $this->showCreateListingModal = false;
        $this->selectedAssetKey = '';
    }

    public function updatedSelectedAssetKey($value)
    {
        if (str_starts_with($value, 'component_')) {
            $this->quantity = 1;
            $this->isQuantityDisabled = true;
        } else {
            $this->isQuantityDisabled = false;
        }
    }

    public function createListing()
    {
        $this->validate([
            'selectedAssetKey' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'warranty_period_days' => 'nullable|integer|min:0',
            'warranty_terms' => 'nullable|string|max:255',
        ]);

        [$type, $id] = explode('_', $this->selectedAssetKey);
        $asset = Item::where('user_id', Auth::id())->find($id);

        if (!$asset) {
            session()->flash('error', 'Selected item or component not found.');
            return;
        }

        if ($asset->parent_id !== null) {
            $this->quantity = 1;
        }

        Listing::create([
            'user_id' => Auth::id(),
            'item_id' => $asset->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'is_negotiable' => $this->is_negotiable,
            'is_published' => true,
            'warranty_period_days' => $this->warranty_period_days ?: 0,
            'is_warranty_negotiable' => $this->is_warranty_negotiable,
            'warranty_terms' => $this->warranty_terms ?: '',
            'allow_shipping' => $this->allow_shipping,
        ]);

        session()->flash('message', 'Marketplace listing published successfully!');
        $this->showCreateListingModal = false;
        $this->reset(['selectedAssetKey', 'price', 'is_negotiable', 'quantity', 'warranty_period_days', 'is_warranty_negotiable', 'warranty_terms', 'allow_shipping', 'isQuantityDisabled']);
    }

    public function deleteItem($id)
    {
        $user = Auth::user();
        $item = Item::where('user_id', $user?->id)->find($id);

        if (!$item) {
            session()->flash('error', 'Item not found or unauthorized.');
            return;
        }

        // Delete listings for this item
        Listing::where('item_id', $item->id)->delete();

        // Delete child component listings & child items
        $childIds = $item->children()->pluck('id');
        if ($childIds->isNotEmpty()) {
            Listing::whereIn('item_id', $childIds)->delete();
            $item->children()->delete();
        }

        $item->delete();

        session()->flash('message', 'Item and all associated listings deleted successfully.');
    }

    public function getUnlistedAssetsProperty()
    {
        $userId = Auth::id();

        $items = Item::with('deviceModel')
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->whereDoesntHave('listing')
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'name' => $i->name ?: ($i->deviceModel?->name ?? "Item #{$i->id}"),
                'condition' => $i->condition_status,
            ])
            ->toArray();

        $components = Item::with(['parent.deviceModel'])
            ->where('user_id', $userId)
            ->whereNotNull('parent_id')
            ->whereDoesntHave('listing')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name ?: 'Component Part',
                'parent_item' => $c->parent?->name ?: ($c->parent?->deviceModel?->name ?? 'Scrap Asset'),
                'condition' => $c->condition_status,
            ])
            ->toArray();

        return [
            'items' => $items,
            'components' => $components,
        ];
    }

    public function render()
    {
        $user = Auth::user();

        $query = Item::with([
            'parent.deviceModel',
            'deviceModel.category.parent',
            'deviceModel.brand',
            'children.listing',
            'location',
            'listing',
        ])
        ->where('user_id', $user?->id);

        // Category Filter
        if ($this->selectedCategory) {
            $catId = $this->selectedCategory;
            $catIds = Category::where('id', $catId)->orWhere('parent_id', $catId)->pluck('id');
            $query->where(function ($q) use ($catIds) {
                $q->whereHas('deviceModel', fn($mq) => $mq->whereIn('category_id', $catIds))
                  ->orWhereHas('parent.deviceModel', fn($mq) => $mq->whereIn('category_id', $catIds));
            });
        }

        // Brand Filter
        if ($this->selectedBrand) {
            $brandId = $this->selectedBrand;
            $query->where(function ($q) use ($brandId) {
                $q->whereHas('deviceModel', fn($mq) => $mq->where('brand_id', $brandId))
                  ->orWhereHas('parent.deviceModel', fn($mq) => $mq->where('brand_id', $brandId));
            });
        }

        // Condition Filter (Dynamic & grouped matches)
        if ($this->selectedCondition) {
            if ($this->selectedCondition === 'new') {
                $query->whereIn('condition_status', ['new', 'brand_new']);
            } elseif ($this->selectedCondition === 'used') {
                $query->whereIn('condition_status', ['used', 'working', 'tested_used', 'tested_working', 'tested_grade_a', 'used_clean', 'used_excellent', 'clean', 'tokunbo']);
            } elseif ($this->selectedCondition === 'refurbished') {
                $query->whereIn('condition_status', ['refurbished', 'repaired']);
            } elseif ($this->selectedCondition === 'faulty') {
                $query->whereIn('condition_status', ['faulty', 'scrap', 'donor_unit', 'damaged', 'water_damaged', 'cracked', 'untested']);
            } else {
                $query->where('condition_status', $this->selectedCondition);
            }
        }

        // Location Filter (dropdown of user's locations)
        if ($this->selectedLocation) {
            $locId = $this->selectedLocation;
            $query->where(function ($q) use ($locId) {
                $q->where('location_id', $locId)
                  ->orWhereHas('parent', fn($pq) => $pq->where('location_id', $locId))
                  ->orWhereHas('children', fn($cq) => $cq->where('location_id', $locId));
            });
        }

        // Status Filter (Listed vs Unlisted)
        if ($this->selectedStatus === 'listed') {
            $query->where(function ($q) {
                $q->has('listing')
                  ->orWhereHas('children', function ($cq) {
                      $cq->has('listing');
                  });
            });
        } elseif ($this->selectedStatus === 'unlisted') {
            $query->whereDoesntHave('listing')
                  ->whereDoesntHave('children', function ($cq) {
                      $cq->has('listing');
                  });
        }

        // Search Input Filter (Title, Model, anything)
        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('condition_notes', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('deviceModel', function ($mq) use ($term) {
                      $mq->where('name', 'like', $term)
                         ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', $term))
                         ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $term));
                  })
                  ->orWhereHas('parent', function ($pq) use ($term) {
                      $pq->where('name', 'like', $term)
                         ->orWhereHas('deviceModel', function ($pmq) use ($term) {
                             $pmq->where('name', 'like', $term)
                                 ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', $term));
                         });
                  })
                  ->orWhereHas('location', function ($lq) use ($term) {
                      $lq->where('label', 'like', $term)
                         ->orWhere('city', 'like', $term)
                         ->orWhere('state', 'like', $term);
                  });
            });
        }

        // Sort By Filter (Name, Date Added, Listed)
        switch ($this->sortBy) {
            case 'name':
            case 'name_asc':
                $query->orderByRaw('COALESCE(NULLIF(items.name, ""), (SELECT models.name FROM models WHERE models.id = items.model_id), "") ASC');
                break;
            case 'name_desc':
                $query->orderByRaw('COALESCE(NULLIF(items.name, ""), (SELECT models.name FROM models WHERE models.id = items.model_id), "") DESC');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'listed':
                $query->withCount('listing')->orderByDesc('listing_count')->latest();
                break;
            case 'unlisted':
                $query->withCount('listing')->orderBy('listing_count', 'asc')->latest();
                break;
            case 'date_desc':
            case 'date_added':
            default:
                $query->latest();
                break;
        }

        $items = $query->paginate(10);
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $locations = $user?->locations ?? collect();

        // Extract distinct conditions dynamically from database
        $userConditions = Item::where('user_id', $user?->id)
            ->whereNotNull('condition_status')
            ->where('condition_status', '!=', '')
            ->pluck('condition_status')
            ->unique()
            ->values();

        $defaultConditions = collect(['brand_new', 'tested_used', 'tested_working', 'refurbished', 'scrap', 'donor_unit']);
        $conditions = $userConditions->concat($defaultConditions)->unique()->sort()->values();

        return view('livewire.dashboard.inventory.items-list', [
            'items' => $items,
            'categories' => $categories,
            'brands' => $brands,
            'locations' => $locations,
            'conditions' => $conditions,
            'unlistedAssets' => $this->unlistedAssets,
        ]);
    }
}
