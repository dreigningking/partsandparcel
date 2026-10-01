<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\Listing;

class ListingObserver
{
    public static bool $seeding = false;

    public function created(Listing $listing): void
    {
        if (self::$seeding || ($listing->is_published && $this->hasSubscription($listing))) {
            $this->createModerationRecord($listing, 'created');
        }
    }

    public function updated(Listing $listing): void
    {
        if ($listing->wasChanged('is_published') && $listing->is_published && $this->hasSubscription($listing)) {
            $this->createModerationRecord($listing, 'updated');
        }
    }

    protected function hasSubscription(Listing $listing): bool
    {
        if ($listing->relationLoaded('activeSubscribedListingLink')) {
            return $listing->activeSubscribedListingLink !== null;
        }

        return (bool) $listing->activeSubscribedListingLink()->exists();
    }

    protected function createModerationRecord(Listing $listing, string $action): void
    {
        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $listing->id,
            'action' => $action,
            'status' => self::$seeding ? 'approved' : 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
