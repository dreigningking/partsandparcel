<?php

namespace App\Observers;

use App\Models\Listing;
use App\Models\Moderation;

class ListingObserver
{
    public static bool $seeding = false;

    public function created(Listing $listing): void
    {
        if (self::$seeding) {
            return;
        }

        $this->createModerationRecord($listing, 'created');
    }

    public function updated(Listing $listing): void
    {
        if (self::$seeding) {
            return;
        }

        // Substantive listing changes (price, warranty, etc.), ignoring counters and moderation-managed flags
        $changes = array_diff(array_keys($listing->getChanges()), [
            'updated_at',
            'is_published',
            'is_active',
            'reserved_quantity',
            'sold_quantity',
        ]);

        if (!empty($changes)) {
            $this->createModerationRecord($listing, 'updated');
        }
    }

    protected function createModerationRecord(Listing $listing, string $action): void
    {
        $alreadyPending = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $listing->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return;
        }

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $listing->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
