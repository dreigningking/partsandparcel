<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class Listings extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    public string $selectedCategory = '';
    public string $selectedBrand = '';
    public string $selectedStatus = '';
    public string $priceSort = ''; // 'high_low', 'low_high', ''

    // Quick Edit Listing Modal Properties
    public bool $showEditModal = false;
    public ?int $editingListingId = null;
    public float $editPrice = 0.0;
    public bool $editIsNegotiable = false;
    public int $editQuantity = 1;
    public string $editStatus = 'active';
    public int $editWarrantyPeriodDays = 0;
    public bool $editIsWarrantyNegotiable = false;
    public string $editWarrantyTerms = '';
    public bool $editAllowShipping = false;

    // Create New Listing Modal Properties
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

    public function mount()
    {
        if (request()->has('search')) {
            $this->search = (string) request()->query('search');
        }
    }

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
        $this->reset(['search', 'selectedCategory', 'selectedBrand', 'selectedStatus', 'priceSort']);
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
            'location_id' => $asset->location_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'is_negotiable' => $this->is_negotiable,
            'status' => 'active',
            'warranty_period_days' => $this->warranty_period_days ?: 0,
            'is_warranty_negotiable' => $this->is_warranty_negotiable,
            'warranty_terms' => $this->warranty_terms ?: '',
            'allow_shipping' => $this->allow_shipping,
            'description' => $asset->name ?? '',
        ]);

        session()->flash('message', 'Marketplace listing published successfully!');
        $this->showCreateListingModal = false;
        $this->reset(['selectedAssetKey', 'price', 'is_negotiable', 'quantity', 'warranty_period_days', 'is_warranty_negotiable', 'warranty_terms', 'allow_shipping', 'isQuantityDisabled']);
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
        $this->editIsNegotiable = (bool) $listing->is_negotiable;
        $this->editQuantity = (int) $listing->quantity;
        $this->editStatus = $listing->status;
        $this->editWarrantyPeriodDays = (int) $listing->warranty_period_days;
        $this->editIsWarrantyNegotiable = (bool) $listing->is_warranty_negotiable;
        $this->editWarrantyTerms = (string) $listing->warranty_terms;
        $this->editAllowShipping = (bool) $listing->allow_shipping;
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
                'is_negotiable' => $this->editIsNegotiable,
                'quantity' => $this->editQuantity,
                'status' => $status,
                'warranty_period_days' => $this->editWarrantyPeriodDays ?: 0,
                'is_warranty_negotiable' => $this->editIsWarrantyNegotiable,
                'warranty_terms' => $this->editWarrantyTerms ?: '',
                'allow_shipping' => $this->editAllowShipping,
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

        $query = Listing::with([
            'location',
            'item.deviceModel.category.parent',
            'item.deviceModel.brand',
            'item.parent.deviceModel.brand',
            'item.parent.deviceModel.category',
        ])
        ->where('user_id', $user?->id);

        // Search Filter
        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                  ->orWhereHas('item', function ($aq) use ($term) {
                      $aq->where('name', 'like', $term)
                         ->orWhereHas('deviceModel', function ($mq) use ($term) {
                             $mq->where('name', 'like', $term)
                                ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', $term));
                         })
                         ->orWhereHas('parent', function ($pq) use ($term) {
                             $pq->where('name', 'like', $term)
                                ->orWhereHas('deviceModel', fn($pmq) => $pmq->where('name', 'like', $term));
                         });
                  });
            });
        }

        // Category Filter
        if ($this->selectedCategory) {
            $catId = $this->selectedCategory;
            $catIds = Category::where('id', $catId)->orWhere('parent_id', $catId)->pluck('id');
            $query->where(function ($q) use ($catIds) {
                $q->whereHas('item', function ($iq) use ($catIds) {
                    $iq->where(function ($ciq) use ($catIds) {
                        $ciq->whereHas('deviceModel', fn($mq) => $mq->whereIn('category_id', $catIds))
                            ->orWhereHas('parent.deviceModel', fn($mq) => $mq->whereIn('category_id', $catIds));
                    });
                });
            });
        }

        // Brand Filter
        if ($this->selectedBrand) {
            $brandId = $this->selectedBrand;
            $query->where(function ($q) use ($brandId) {
                $q->whereHas('item', function ($iq) use ($brandId) {
                    $iq->where(function ($biq) use ($brandId) {
                        $biq->whereHas('deviceModel', fn($mq) => $mq->where('brand_id', $brandId))
                            ->orWhereHas('parent.deviceModel', fn($mq) => $mq->where('brand_id', $brandId));
                    });
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
        $brands = Brand::orderBy('name')->get();
        $locations = $user?->locations ?? collect();

        return view('livewire.dashboard.inventory.listings', [
            'listings' => $listings,
            'categories' => $categories,
            'brands' => $brands,
            'locations' => $locations,
            'unlistedAssets' => $this->unlistedAssets,
        ]);
    }
}
