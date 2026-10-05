<?php

namespace App\Livewire\Admin;

use App\Models\Moderation;
use App\Models\Listing;
use Livewire\Component;

class AdminListingDetails extends Component
{
    public Listing $listing;

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function approve(): void
    {
        $pendingModeration = Moderation::query()
            ->where('moderatable_type', Listing::class)
            ->where('moderatable_id', $this->listing->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingModeration) {
            $pendingModeration->update([
                'status' => 'approved',
                'moderated_by' => auth()->id(),
            ]);
        }

        $this->listing->update(['is_published' => true]);
        $this->listing->refresh();
        $this->logModeration('approved', 'approve');
        session()->flash('status', __('Listing approved.'));
    }

    public function disapprove(): void
    {
        $pendingModeration = Moderation::query()
            ->where('moderatable_type', Listing::class)
            ->where('moderatable_id', $this->listing->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingModeration) {
            $pendingModeration->update([
                'status' => 'rejected',
                'moderated_by' => auth()->id(),
            ]);
        }

        $this->listing->update(['is_published' => false]);
        $this->listing->refresh();
        $this->logModeration('rejected', 'disapprove');
        session()->flash('status', __('Listing disapproved.'));
    }

    public function delete(): mixed
    {
        $listingId = $this->listing->id;
        $this->logModeration('deleted', 'delete');
        $this->listing->delete();
        session()->flash('status', __('Listing #:id deleted.', ['id' => $listingId]));

        return redirect()->route('admin.listings');
    }

    private function logModeration(string $status, string $action): void
    {
        Moderation::query()->create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $this->listing->id,
            'moderated_by' => auth()->id(),
            'status' => $status,
            'action' => $action,
            'reason' => null,
        ]);
    }

    public function render()
    {
        $this->listing->load([
            'user',
            'category',
            'media',
            'subscribedListingLinks.subscription.plan',
            'promotions.plan',
        ]);
        $moderations = Moderation::query()
            ->where('moderatable_type', Listing::class)
            ->where('moderatable_id', $this->listing->id)
            ->latest('created_at')
            ->take(20)
            ->get();

        return view('livewire.admin.admin-listing-details', ['moderations' => $moderations]);
    }
}
