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

        $this->notifyWishlistedUsersOfStockChanges($listing);
    }

    protected function notifyWishlistedUsersOfStockChanges(Listing $listing): void
    {
        $oldQty = (int) ($listing->getOriginal('quantity') ?? 0);
        $oldReserved = (int) ($listing->getOriginal('reserved_quantity') ?? 0);
        $oldSold = (int) ($listing->getOriginal('sold_quantity') ?? 0);
        $wasAvailable = max(0, $oldQty - $oldReserved - $oldSold);

        $newQty = (int) ($listing->quantity ?? 0);
        $newReserved = (int) ($listing->reserved_quantity ?? 0);
        $newSold = (int) ($listing->sold_quantity ?? 0);
        $nowAvailable = max(0, $newQty - $newReserved - $newSold);

        // Check if stock state meaningfully changed
        if ($wasAvailable === $nowAvailable) {
            return;
        }

        // Restocked: was 0 or less, now has available stock
        $isRestocked = ($wasAvailable <= 0 && $nowAvailable > 0);

        // Low stock: dropped into 1 or 2 units remaining from a higher level
        $isLowStock = ($wasAvailable > 2 && $nowAvailable > 0 && $nowAvailable <= 2);

        if (! $isRestocked && ! $isLowStock) {
            return;
        }

        $wishlists = \App\Models\Wishlist::where('listing_id', $listing->id)
            ->with('user')
            ->get();

        foreach ($wishlists as $wishlist) {
            if (! $wishlist->user || $wishlist->user->id === $listing->user_id) {
                continue;
            }

            if ($isRestocked) {
                $wishlist->user->notify(new \App\Notifications\ListingRestockedNotification($listing, $nowAvailable));
            } elseif ($isLowStock) {
                $wishlist->user->notify(new \App\Notifications\ListingLowStockNotification($listing, $nowAvailable));
            }
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
