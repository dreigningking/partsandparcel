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

    // Create Listing Modal Properties
    public bool $showCreateListingModal = false;
    public string $selectedAssetKey = '';
    public float $price = 0.00;
    public int $quantity = 1;
    public bool $isQuantityDisabled = false;
    public int $warranty_period_days = 0;
    public string $warranty_terms = '';
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

    public function resetFilters()
    {
        $this->reset(['search', 'selectedCategory', 'selectedBrand', 'selectedCondition', 'selectedLocation', 'selectedStatus']);
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
        $this->quantity = 1;
        $this->warranty_period_days = 0;
        $this->warranty_terms = '';
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
            'assetable_type' => Item::class,
            'assetable_id' => $asset->id,
            'location_id' => $asset->location_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'status' => 'active',
            'warranty_period_days' => $this->warranty_period_days ?: 0,
            'warranty_terms' => $this->warranty_terms ?: '',
            'description' => $asset->name ?? '',
        ]);

        session()->flash('message', 'Marketplace listing published successfully!');
        $this->showCreateListingModal = false;
        $this->reset(['selectedAssetKey', 'price', 'quantity', 'warranty_period_days', 'warranty_terms', 'isQuantityDisabled']);
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
        Listing::where('assetable_type', Item::class)->where('assetable_id', $item->id)->delete();

        // Delete child component listings & child items
        $childIds = $item->children()->pluck('id');
        if ($childIds->isNotEmpty()) {
            Listing::where('assetable_type', Item::class)->whereIn('assetable_id', $childIds)->delete();
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

        $components = Item::with('parent')
            ->where('user_id', $userId)
            ->whereNotNull('parent_id')
            ->whereDoesntHave('listing')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'parent_item' => $c->parent?->name ?: 'Scrap Asset',
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
            'deviceModel.category',
            'deviceModel.brand',
            'children.listing',
            'listing.location',
        ])
        ->where('user_id', $user?->id)
        ->whereNull('parent_id'); // Main items (whole, part, or scrap)

        // Category Filter
        if ($this->selectedCategory) {
            $query->whereHas('deviceModel', function ($q) {
                $q->where('category_id', $this->selectedCategory);
            });
        }

        // Brand Filter
        if ($this->selectedBrand) {
            $query->whereHas('deviceModel', function ($q) {
                $q->where('brand_id', $this->selectedBrand);
            });
        }

        // Condition Filter (new, used, refurbished, faulty)
        if ($this->selectedCondition) {
            if ($this->selectedCondition === 'used') {
                $query->whereIn('condition_status', ['used', 'working']);
            } elseif ($this->selectedCondition === 'faulty') {
                $query->whereIn('condition_status', ['faulty', 'scrap']);
            } else {
                $query->where('condition_status', $this->selectedCondition);
            }
        }

        // Location Filter (dropdown of user's locations)
        if ($this->selectedLocation) {
            $query->where(function ($q) {
                $q->where('location_id', $this->selectedLocation)
                  ->orWhereHas('listing', fn($lq) => $lq->where('location_id', $this->selectedLocation));
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
                  ->orWhereHas('location', function ($lq) use ($term) {
                      $lq->where('label', 'like', $term)
                         ->orWhere('city', 'like', $term)
                         ->orWhere('state', 'like', $term);
                  });
            });
        }

        $items = $query->latest()->paginate(10);
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $locations = $user?->locations ?? collect();

        return view('livewire.dashboard.inventory.items-list', [
            'items' => $items,
            'categories' => $categories,
            'brands' => $brands,
            'locations' => $locations,
            'unlistedAssets' => $this->unlistedAssets,
        ]);
    }
}
