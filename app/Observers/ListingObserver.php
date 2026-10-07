<?php

namespace App\Observers;

use App\Models\Listing;
use App\Models\Moderation;
use App\Models\User;
use App\Services\Commercial\SubscriptionService;

class ListingObserver
{
    public static bool $seeding = false;

    public function saving(Listing $listing): void
    {
        if (self::$seeding) {
            return;
        }

        // Active flag deals with subscription limit
        if ($listing->is_active && $listing->user_id) {
            $user = $listing->user ?: User::find($listing->user_id);
            if ($user) {
                $subService = app(SubscriptionService::class);
                $stats = $subService->getUsageStats($user);
                $limit = (int) ($stats['listing_limit'] ?? 10);
                $currentActiveCount = Listing::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->when($listing->id, fn($q) => $q->where('id', '!=', $listing->id))
                    ->count();

                if ($currentActiveCount >= $limit) {
                    $listing->is_active = false;
                }
            }
        }
    }

    public function created(Listing $listing): void
    {
        if (self::$seeding) {
            return;
        }

        // Only listings that are published are checked for moderation
        if ($listing->is_published) {
            $this->createModerationRecord($listing, 'created');
        }
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

        // If newly published or substantive changes on a published listing
        if ($listing->is_published && (! empty($changes) || $listing->wasChanged('is_published'))) {
            $this->createModerationRecord($listing, 'updated');
        }

        $this->notifyWishlistedUsersOfStockChanges($listing);
    }

    public function deleting(Listing $listing): void
    {
        if (self::$seeding) {
            return;
        }

        $wasAvailable = ($listing->is_active && $listing->is_published && $listing->availableQuantity() > 0);
        if (! $wasAvailable) {
            return;
        }

        $wishlists = \App\Models\Wishlist::where('listing_id', $listing->id)
            ->with('user')
            ->get();

        foreach ($wishlists as $wishlist) {
            if (! $wishlist->user || $wishlist->user->id === $listing->user_id) {
                continue;
            }

            $wishlist->user->notify(new \App\Notifications\ListingSoldOutNotification($listing, 'deleted'));
        }
    }

    protected function notifyWishlistedUsersOfStockChanges(Listing $listing): void
    {
        $oldQty = (int) ($listing->getOriginal('quantity') ?? 0);
        $oldReserved = (int) ($listing->getOriginal('reserved_quantity') ?? 0);
        $oldSold = (int) ($listing->getOriginal('sold_quantity') ?? 0);
        $wasAvailableStock = max(0, $oldQty - $oldReserved - $oldSold);

        $wasActive = (bool) ($listing->getOriginal('is_active') ?? true);
        $wasPublished = (bool) ($listing->getOriginal('is_published') ?? true);
        $wasListedAndAvailable = ($wasActive && $wasPublished && $wasAvailableStock > 0);

        $newQty = (int) ($listing->quantity ?? 0);
        $newReserved = (int) ($listing->reserved_quantity ?? 0);
        $newSold = (int) ($listing->sold_quantity ?? 0);
        $nowAvailableStock = max(0, $newQty - $newReserved - $newSold);

        $nowActive = (bool) ($listing->is_active ?? true);
        $nowPublished = (bool) ($listing->is_published ?? true);
        $nowListedAndAvailable = ($nowActive && $nowPublished && $nowAvailableStock > 0);

        // Check if availability meaningfully changed
        $isRestocked = (! $wasListedAndAvailable && $nowListedAndAvailable);
        $isSoldOut = ($wasListedAndAvailable && ! $nowListedAndAvailable);
        $isLowStock = ($wasListedAndAvailable && $wasAvailableStock > 2 && $nowListedAndAvailable && $nowAvailableStock <= 2);

        if (! $isRestocked && ! $isSoldOut && ! $isLowStock) {
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
                $wishlist->user->notify(new \App\Notifications\ListingRestockedNotification($listing, $nowAvailableStock));
            } elseif ($isSoldOut) {
                $reason = ($nowAvailableStock <= 0) ? 'sold_out' : 'unavailable';
                $wishlist->user->notify(new \App\Notifications\ListingSoldOutNotification($listing, $reason));
            } elseif ($isLowStock) {
                $wishlist->user->notify(new \App\Notifications\ListingLowStockNotification($listing, $nowAvailableStock));
            }
        }
    }

    protected function createModerationRecord(Listing $listing, string $action): void
    {
        $autoApprove = (bool) \App\Models\Setting::getValue('auto_approve_listings', false);
        $status = $autoApprove ? 'approved' : 'pending';

        $existing = Moderation::where('moderatable_type', Listing::class)
            ->where('moderatable_id', $listing->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending' || (! $autoApprove && $action === 'updated')) {
                $existing->update([
                    'action' => $action,
                    'status' => $status,
                ]);
            }
            return;
        }

        Moderation::create([
            'moderatable_type' => Listing::class,
            'moderatable_id' => $listing->id,
            'action' => $action,
            'status' => $status,
            'reason' => null,
            'moderated_by' => null,
        ]);
    }
}
