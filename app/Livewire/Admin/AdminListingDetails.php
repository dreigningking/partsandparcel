<?php

namespace App\Livewire\Admin;

use App\Models\InvoiceItem;
use App\Models\Listing;
use App\Models\Moderation;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Listing Details — Admin Control Center')]
class AdminListingDetails extends Component
{
    public Listing $listing;

    // Tab Navigation
    public string $activeTab = 'overview';

    // Quick Rejection Modal
    public bool $showRejectModal = false;
    public string $rejectionReason = '';
    public string $presetReason = '';

    // Admin Stock Adjustment
    public bool $isEditingStock = false;
    public int $newQuantity = 0;

    public function mount($listing): void
    {
        if ($listing instanceof Listing) {
            $this->listing = $listing;
        } else {
            $this->listing = Listing::where('id', $listing)
                ->orWhere('slug', $listing)
                ->firstOrFail();
        }

        $this->newQuantity = (int) $this->listing->quantity;
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'sales', 'reviews', 'trust'])) {
            $this->activeTab = $tab;
        }
    }

    public function approve(): void
    {
        $hasPending = Moderation::where(function ($q) {
            $q->where('moderatable_type', 'listing')
                ->orWhere('moderatable_type', Listing::class);
        })
            ->where('moderatable_id', $this->listing->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where(function ($q) {
                $q->where('moderatable_type', 'listing')
                    ->orWhere('moderatable_type', Listing::class);
            })
                ->where('moderatable_id', $this->listing->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'action' => 'approved_by_admin',
                    'moderated_by' => Auth::id(),
                ]);
        } else {
            Moderation::create([
                'moderatable_type' => Listing::class,
                'moderatable_id' => $this->listing->id,
                'moderated_by' => Auth::id(),
                'status' => 'approved',
                'action' => 'approved_by_admin',
            ]);
        }

        $this->listing->update([
            'is_published' => true,
            'is_active' => true,
        ]);

        $this->listing->refresh();

        session()->flash('message', "Listing #{$this->listing->id} has been approved and published to the live marketplace.");
    }

    public function openRejectModal(): void
    {
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

        $hasPending = Moderation::where(function ($q) {
            $q->where('moderatable_type', 'listing')
                ->orWhere('moderatable_type', Listing::class);
        })
            ->where('moderatable_id', $this->listing->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where(function ($q) {
                $q->where('moderatable_type', 'listing')
                    ->orWhere('moderatable_type', Listing::class);
            })
                ->where('moderatable_id', $this->listing->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'action' => 'rejected_by_admin',
                    'reason' => $this->rejectionReason,
                    'moderated_by' => Auth::id(),
                ]);
        } else {
            Moderation::create([
                'moderatable_type' => Listing::class,
                'moderatable_id' => $this->listing->id,
                'moderated_by' => Auth::id(),
                'status' => 'rejected',
                'action' => 'rejected_by_admin',
                'reason' => $this->rejectionReason,
            ]);
        }

        $this->listing->update([
            'is_published' => false,
        ]);

        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->listing->refresh();

        session()->flash('message', "Listing #{$this->listing->id} has been rejected with the stated reason recorded.");
    }

    public function togglePublished(): void
    {
        $newState = ! $this->listing->is_published;
        $this->listing->update(['is_published' => $newState]);

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'moderated_by' => Auth::id(),
            'status' => $newState ? 'published' : 'unpublished',
            'action' => $newState ? 'published_by_admin' : 'unpublished_by_admin',
        ]);

        $this->listing->refresh();
        $text = $newState ? 'published' : 'unpublished';
        session()->flash('message', "Listing publication status set to: {$text}.");
    }

    public function toggleActive(): void
    {
        $newState = ! $this->listing->is_active;
        $this->listing->update(['is_active' => $newState]);

        $this->listing->refresh();
        $text = $newState ? 'active' : 'inactive';
        session()->flash('message', "Listing active status set to: {$text}.");
    }

    public function startEditStock(): void
    {
        $this->newQuantity = (int) $this->listing->quantity;
        $this->isEditingStock = true;
    }

    public function saveStock(): void
    {
        $this->validate([
            'newQuantity' => 'required|integer|min:0|max:1000000',
        ]);

        $oldQty = $this->listing->quantity;
        $this->listing->update([
            'quantity' => $this->newQuantity,
        ]);

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'moderated_by' => Auth::id(),
            'status' => 'stock_updated',
            'action' => 'stock_adjustment',
            'reason' => "Admin adjusted inventory quantity from {$oldQty} to {$this->newQuantity}",
        ]);

        $this->isEditingStock = false;
        $this->listing->refresh();

        session()->flash('message', "Inventory stock quantity updated to {$this->newQuantity} units.");
    }

    public function delete(): mixed
    {
        $id = $this->listing->id;

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'moderated_by' => Auth::id(),
            'status' => 'deleted',
            'action' => 'deleted_by_admin',
        ]);

        $this->listing->delete();

        session()->flash('message', "Listing #{$id} was permanently removed.");

        return redirect()->route('admin.properties');
    }

    public function resolveReport(int $reportId, string $notes = 'Resolved by admin during listing inspection.'): void
    {
        $report = $this->listing->reports()->findOrFail($reportId);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Resolved by administrator.',
        ]);

        $this->listing->refresh();
        session()->flash('message', "Report #{$reportId} marked as resolved.");
    }

    public function dismissReport(int $reportId, string $notes = 'Dismissed as false positive / invalid report.'): void
    {
        $report = $this->listing->reports()->findOrFail($reportId);

        $report->update([
            'status' => 'dismissed',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Dismissed by administrator.',
        ]);

        $this->listing->refresh();
        session()->flash('message', "Report #{$reportId} has been dismissed.");
    }

    public function render()
    {
        $this->listing->loadMissing([
            'user.country',
            'user.primaryLocation',
            'user.subscriptions.plan',
            'item.deviceModel.brand',
            'item.deviceModel.category',
            'item.location.state',
            'item.location.country',
            'item.parent.deviceModel.brand',
            'item.children.deviceModel',
            'media',
            'item.media',
            'reviews.user',
            'promotions.payments',
            'cartItems',
            'reports.user',
            'reports.resolvedBy',
        ]);

        // Sales history from InvoiceItem
        $salesQuery = InvoiceItem::query()
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('itemable_type', 'listing')
                        ->where('itemable_id', $this->listing->id);
                });
                if ($this->listing->item_id) {
                    $q->orWhere(function ($sub) {
                        $sub->where('itemable_type', 'item')
                            ->where('itemable_id', $this->listing->item_id);
                    });
                }
            })
            ->with(['invoice.buyer', 'invoice.payments']);

        $timesSold = (clone $salesQuery)->count();
        $totalSoldUnits = (int) (clone $salesQuery)->sum('quantity');
        $grossRevenue = (float) (clone $salesQuery)->sum('amount');
        $salesHistory = (clone $salesQuery)->latest('created_at')->get();

        // Marketplace views from ViewedEntity
        $viewsCount = $this->listing->views()->count();
        $uniqueViewers = $this->listing->views()->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // Reviews metrics
        $reviewsCount = $this->listing->reviewsCount();
        $avgRating = $this->listing->averageRating();
        $starDistribution = $this->listing->starDistribution();
        $reviews = $this->listing->reviews()->with('user')->latest('created_at')->get();

        // Moderation audit records
        $moderations = Moderation::query()
            ->where(function ($q) {
                $q->where('moderatable_type', 'listing')
                    ->orWhere('moderatable_type', Listing::class);
            })
            ->where('moderatable_id', $this->listing->id)
            ->with('moderator')
            ->latest('created_at')
            ->get();

        return view('livewire.admin.admin-listing-details', [
            'moderations' => $moderations,
            'timesSold' => $timesSold,
            'totalSoldUnits' => $totalSoldUnits,
            'grossRevenue' => $grossRevenue,
            'salesHistory' => $salesHistory,
            'viewsCount' => $viewsCount,
            'uniqueViewers' => $uniqueViewers,
            'reviewsCount' => $reviewsCount,
            'avgRating' => $avgRating,
            'starDistribution' => $starDistribution,
            'reviews' => $reviews,
        ]);
    }
}
