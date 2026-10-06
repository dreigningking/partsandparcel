<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Moderation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Listings Management — Admin Control Center')]
class AdminListings extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?int $selectedCategory = null;

    #[Url(as: 'brand')]
    public ?int $selectedBrand = null;

    #[Url(as: 'type')]
    public string $selectedType = '';

    #[Url(as: 'condition')]
    public string $selectedCondition = '';

    #[Url(as: 'status')]
    public string $selectedStatus = '';

    #[Url(as: 'shipping')]
    public bool $shippingOnly = false;

    #[Url(as: 'sort')]
    public string $priceSort = '';

    public int $perPage = 15;

    // Quick Rejection Modal
    public bool $showRejectModal = false;
    public ?int $selectedListingId = null;
    public string $rejectionReason = '';
    public string $presetReason = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedBrand(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedType(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCondition(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriceSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'selectedCategory',
            'selectedBrand',
            'selectedType',
            'selectedCondition',
            'selectedStatus',
            'shippingOnly',
            'priceSort',
        ]);
        $this->resetPage();
    }

    public function approve(int $listingId): void
    {
        $listing = Listing::findOrFail($listingId);

        $updated = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $listing->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'moderated_by' => Auth::id(),
            ]);

        if (! $updated) {
            Moderation::create([
                'moderatable_type' => Listing::class,
                'moderatable_id' => $listing->id,
                'moderated_by' => Auth::id(),
                'status' => 'approved',
                'action' => 'approved_by_admin',
            ]);
        }

        $listing->update([
            'is_published' => true,
            'is_active' => true,
        ]);

        session()->flash('message', "Listing #{$listing->id} has been successfully approved and published to the marketplace.");
    }

    public function openRejectModal(int $listingId): void
    {
        $this->selectedListingId = $listingId;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->showRejectModal = true;
    }

    public function updatedPresetReason(string $val): void
    {
        if (! empty($val)) {
            $this->rejectionReason = $val;
        }
    }

    public function submitReject(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:1000',
        ]);

        if (! $this->selectedListingId) {
            return;
        }

        $listing = Listing::findOrFail($this->selectedListingId);

        $updated = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $listing->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reason' => $this->rejectionReason,
                'moderated_by' => Auth::id(),
            ]);

        if (! $updated) {
            Moderation::create([
                'moderatable_type' => Listing::class,
                'moderatable_id' => $listing->id,
                'moderated_by' => Auth::id(),
                'status' => 'rejected',
                'action' => 'rejected_by_admin',
                'reason' => $this->rejectionReason,
            ]);
        }

        $listing->update([
            'is_published' => false,
        ]);

        $this->showRejectModal = false;
        $this->selectedListingId = null;
        $this->rejectionReason = '';

        session()->flash('message', "Listing #{$listing->id} has been rejected with the stated reason.");
    }

    public function togglePublish(int $listingId): void
    {
        $listing = Listing::findOrFail($listingId);
        $listing->update([
            'is_published' => ! $listing->is_published,
        ]);

        $statusText = $listing->is_published ? 'published' : 'unpublished';
        session()->flash('message', "Listing #{$listing->id} has been marked as {$statusText}.");
    }

    public function delete(int $listingId): void
    {
        $listing = Listing::findOrFail($listingId);

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $listing->id,
            'moderated_by' => Auth::id(),
            'status' => 'deleted',
            'action' => 'deleted_by_admin',
        ]);

        $listing->delete();

        session()->flash('message', "Listing #{$listingId} has been successfully deleted.");
    }

    public function render()
    {
        // 1. Dynamic Metric Totals
        $metrics = [
            'total' => Listing::count(),
            'live' => Listing::where('is_published', true)
                ->where('is_active', true)
                ->whereDoesntHave('latestModeration', fn($m) => $m->where('status', 'rejected'))
                ->count(),
            'pending' => Moderation::where('moderatable_type', Listing::class)
                ->where('status', 'pending')
                ->count(),
            'sold_out' => Listing::whereRaw('quantity <= (reserved_quantity + sold_quantity)')->count(),
            'inventory_value' => (float) Listing::sum(DB::raw('price * quantity')),
        ];

        // 2. Main Query
        $query = Listing::query()
            ->with([
                'user.country',
                'item.deviceModel.brand',
                'item.deviceModel.category',
                'item.parent.deviceModel.brand',
                'item.location',
                'latestModeration',
                'media',
                'item.media',
            ]);

        // Search Filter
        $query->when($this->search !== '', function (Builder $q) {
            $term = '%' . trim($this->search) . '%';
            $q->where(function (Builder $sub) use ($term) {
                $sub->whereHas('item', function (Builder $itemQuery) use ($term) {
                    $itemQuery->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('deviceModel', function (Builder $modelQuery) use ($term) {
                            $modelQuery->where('name', 'like', $term)
                                ->orWhereHas('brand', fn($b) => $b->where('name', 'like', $term))
                                ->orWhereHas('category', fn($c) => $c->where('name', 'like', $term));
                        });
                })
                ->orWhereHas('user', function (Builder $userQuery) use ($term) {
                    $userQuery->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                })
                ->orWhere('slug', 'like', $term)
                ->orWhere('id', str_replace('#', '', $this->search));
            });
        });

        // Category Filter
        $query->when($this->selectedCategory, function (Builder $q) {
            $q->whereHas('item.deviceModel', function (Builder $m) {
                $m->where('category_id', $this->selectedCategory);
            });
        });

        // Brand Filter
        $query->when($this->selectedBrand, function (Builder $q) {
            $q->whereHas('item.deviceModel', function (Builder $m) {
                $m->where('brand_id', $this->selectedBrand);
            });
        });

        // Item Type Filter (Whole, Part, Scrap)
        $query->when($this->selectedType !== '' && $this->selectedType !== 'all', function (Builder $q) {
            $q->whereHas('item', fn($i) => $i->where('item_type', $this->selectedType));
        });

        // Condition Status Filter
        $query->when($this->selectedCondition !== '' && $this->selectedCondition !== 'all', function (Builder $q) {
            $q->whereHas('item', fn($i) => $i->where('condition_status', $this->selectedCondition));
        });

        // Listing Status Filter
        $query->when($this->selectedStatus !== '' && $this->selectedStatus !== 'all', function (Builder $q) {
            match ($this->selectedStatus) {
                'live' => $q->where('is_published', true)
                            ->where('is_active', true)
                            ->whereRaw('quantity > (reserved_quantity + sold_quantity)')
                            ->whereDoesntHave('latestModeration', fn($m) => $m->where('status', 'rejected')),
                'pending' => $q->whereHas('latestModeration', fn($m) => $m->where('status', 'pending')),
                'rejected' => $q->whereHas('latestModeration', fn($m) => $m->where('status', 'rejected')),
                'draft' => $q->where('is_published', false),
                'sold_out' => $q->whereRaw('quantity <= (reserved_quantity + sold_quantity)'),
                'inactive' => $q->where('is_active', false)->where('is_published', true),
                default => null,
            };
        });

        // Shipping Allowed
        $query->when($this->shippingOnly, fn($q) => $q->where('allow_shipping', true));

        // Sorting
        match ($this->priceSort) {
            'high_low' => $query->orderByDesc('price'),
            'low_high' => $query->orderBy('price'),
            'qty_desc' => $query->orderByDesc('quantity'),
            'sold_desc' => $query->orderByDesc('sold_quantity'),
            'oldest' => $query->oldest('created_at'),
            default => $query->latest('created_at'),
        };

        $listings = $query->paginate($this->perPage);

        // Reference Data
        $categories = Category::query()
            ->where('is_listing', true)
            ->orWhereNull('is_listing')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $brands = Brand::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return view('livewire.admin.admin-listings', [
            'listings' => $listings,
            'metrics' => $metrics,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
