<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Category;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class Listings extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';
    public string $selectedStatus = '';
    public string $priceSort = ''; // 'high_low', 'low_high', ''

    // Quick Edit Listing Modal Properties
    public bool $showEditModal = false;
    public ?int $editingListingId = null;
    public float $editPrice = 0.0;
    public int $editQuantity = 1;
    public string $editStatus = 'active';

    // Create New Listing Modal Properties
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

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingPriceSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedCategory', 'selectedStatus', 'priceSort']);
        $this->resetPage();
    }

    // --- CREATE LISTING MODAL METHODS ---
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

    // --- EDIT LISTING MODAL METHODS ---
    public function editListing($id)
    {
        $listing = Listing::where('user_id', Auth::id())->find($id);

        if (!$listing) {
            session()->flash('error', 'Listing not found.');
            return;
        }

        $this->editingListingId = $listing->id;
        $this->editPrice = (float) $listing->price;
        $this->editQuantity = (int) $listing->quantity;
        $this->editStatus = $listing->status;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingListingId = null;
    }

    public function updateListing()
    {
        $this->validate([
            'editPrice' => 'required|numeric|min:0',
            'editQuantity' => 'required|integer|min:0',
            'editStatus' => 'required|in:active,inactive,draft,rejected,sold_out',
        ]);

        $listing = Listing::where('user_id', Auth::id())->find($this->editingListingId);

        if ($listing) {
            $status = $this->editStatus;
            if ($this->editQuantity <= 0 && $status === 'active') {
                $status = 'sold_out';
            }

            $listing->update([
                'price' => $this->editPrice,
                'quantity' => $this->editQuantity,
                'status' => $status,
            ]);

            session()->flash('message', 'Listing updated successfully!');
        }

        $this->showEditModal = false;
        $this->editingListingId = null;
    }

    public function deleteListing($id)
    {
        $listing = Listing::where('user_id', Auth::id())->find($id);

        if ($listing) {
            $listing->delete();
            session()->flash('message', 'Marketplace listing deleted successfully.');
        } else {
            session()->flash('error', 'Listing not found.');
        }
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

        $query = Listing::with(['location', 'assetable'])
            ->where('user_id', $user?->id);

        // Search Filter
        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                  ->orWhereHasMorph('assetable', [Item::class], function ($aq) use ($term) {
                      $aq->where('name', 'like', $term)
                         ->orWhereHas('deviceModel', fn($mq) => $mq->where('name', 'like', $term));
                  });
            });
        }

        // Category Filter
        if ($this->selectedCategory) {
            $catId = $this->selectedCategory;
            $query->where(function ($q) use ($catId) {
                $q->whereHasMorph('assetable', [Item::class], function ($iq) use ($catId) {
                    $iq->whereHas('deviceModel', fn($mq) => $mq->where('category_id', $catId));
                });
            });
        }

        // Status Filter
        if ($this->selectedStatus) {
            if ($this->selectedStatus === 'sold_out') {
                $query->where(function ($q) {
                    $q->where('status', 'sold_out')
                      ->orWhere('quantity', '<=', 0);
                });
            } elseif ($this->selectedStatus === 'live') {
                $query->where('status', 'active')->where('quantity', '>', 0);
            } else {
                $query->where('status', $this->selectedStatus);
            }
        }

        // Price Sort Filter
        if ($this->priceSort === 'high_low') {
            $query->orderBy('price', 'desc');
        } elseif ($this->priceSort === 'low_high') {
            $query->orderBy('price', 'asc');
        } else {
            $query->latest();
        }

        $listings = $query->paginate(10);
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $locations = $user?->locations ?? collect();

        return view('livewire.dashboard.inventory.listings', [
            'listings' => $listings,
            'categories' => $categories,
            'locations' => $locations,
            'unlistedAssets' => $this->unlistedAssets,
        ]);
    }
}
