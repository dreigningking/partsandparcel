<?php

namespace App\Services\Promotion;

use App\Models\Country;
use App\Models\Listing;
use App\Models\Promotion;
use App\Models\State;
use App\Models\User;
use App\Models\ViewedEntity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PromotionService
{
    /**
     * Resolve the location context (Country ID, State ID / Name) for the current user or guest.
     */
    public function resolveVisitorLocation(?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if ($user) {
            $user->loadMissing(['primaryLocation.state', 'country']);
            $primaryLoc = $user->primaryLocation;

            $countryId = $user->country_id
                ?? $primaryLoc?->country_id
                ?? Country::where('code', strtoupper($user->country_code ?? 'NG'))->value('id')
                ?? Country::where('is_default', true)->value('id');

            $stateId = $primaryLoc?->state_id;
            $stateName = $primaryLoc?->state?->name;

            return [
                'country_id' => $countryId ? (int) $countryId : null,
                'country_name' => $user->country?->name ?? 'Nigeria',
                'state_id' => $stateId ? (int) $stateId : null,
                'state_name' => $stateName,
            ];
        }

        $sessionLoc = session('current_location', []);
        $countryId = $sessionLoc['country_id'] ?? null;
        $stateName = $sessionLoc['state'] ?? null;
        $stateId = null;

        if (! $countryId) {
            $countryCode = strtoupper($sessionLoc['country_code'] ?? 'NG');
            $country = Country::where('code', $countryCode)->where('is_active', true)->first()
                ?? Country::where('is_default', true)->first();
            $countryId = $country?->id;
        }

        if ($stateName && $countryId) {
            $stateId = State::where('country_id', $countryId)
                ->where(function ($q) use ($stateName) {
                    $q->where('name', $stateName)->orWhere('code', $stateName);
                })
                ->value('id');
        }

        return [
            'country_id' => $countryId ? (int) $countryId : null,
            'country_name' => $sessionLoc['country_name'] ?? 'Nigeria',
            'state_id' => $stateId ? (int) $stateId : null,
            'state_name' => $stateName,
        ];
    }

    /**
     * Retrieve featured listings using the tiered location and achievement algorithm:
     * 1. Check user/guest location (State & Country)
     * 2. Promotions in user's state (lowest achievement first)
     * 3. Fallback/backfill: Promotions in user's country (lowest achievement first)
     * 4. Fallback/backfill: Popular available listings via ViewedEntity
     */
    public function getFeaturedListings(int $limit = 10, bool $backfill = true): Collection
    {
        $loc = $this->resolveVisitorLocation();
        $countryId = $loc['country_id'];
        $stateId = $loc['state_id'];
        $stateName = $loc['state_name'];

        $featured = collect();

        // Tier 1: Promotions in State
        if ($stateId || $stateName) {
            $stateListings = $this->queryPromotionsByLocation(
                stateId: $stateId,
                stateName: $stateName,
                countryId: null,
                excludeIds: $featured->pluck('id')->all()
            );

            $sortedState = $this->sortByLowestAchievement($stateListings);
            $featured = $featured->concat($sortedState->take($limit));
        }

        // Tier 2: Promotions in Country (if needed)
        if (($featured->count() < $limit || $featured->isEmpty()) && $countryId) {
            $needed = $limit - $featured->count();
            $countryListings = $this->queryPromotionsByLocation(
                stateId: null,
                stateName: null,
                countryId: $countryId,
                excludeIds: $featured->pluck('id')->all()
            );

            $sortedCountry = $this->sortByLowestAchievement($countryListings);
            $featured = $featured->concat($sortedCountry->take($needed));
        }

        // Tier 3: Popular Available Listings via ViewedEntity (if still needed)
        if ($featured->count() < $limit || $featured->isEmpty()) {
            $needed = $limit - $featured->count();
            $popular = $this->queryPopularListings(
                limit: $needed,
                countryId: $countryId,
                excludeIds: $featured->pluck('id')->all()
            );

            $featured = $featured->concat($popular);
        }

        return $featured->values();
    }

    /**
     * Query candidate available listings with active promotions for a specific location.
     */
    protected function queryPromotionsByLocation(?int $stateId, ?string $stateName, ?int $countryId, array $excludeIds = []): Collection
    {
        return Listing::available()
            ->with([
                'item.deviceModel.category',
                'item.deviceModel.brand',
                'item.location.state',
                'seller.primaryLocation.state',
                'media',
                'item.media',
                'activePromotion',
            ])
            ->whereHas('activePromotion')
            ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->where(function ($query) use ($stateId, $stateName, $countryId) {
                if ($stateId || $stateName) {
                    $query->whereHas('item.location', function ($lq) use ($stateId, $stateName) {
                        if ($stateId) {
                            $lq->where('state_id', $stateId);
                        } elseif ($stateName) {
                            $lq->whereHas('state', fn ($sq) => $sq->where('name', $stateName));
                        }
                    })->orWhereHas('seller.primaryLocation', function ($pq) use ($stateId, $stateName) {
                        if ($stateId) {
                            $pq->where('state_id', $stateId);
                        } elseif ($stateName) {
                            $pq->whereHas('state', fn ($sq) => $sq->where('name', $stateName));
                        }
                    });
                } elseif ($countryId) {
                    $query->whereHas('seller', fn ($sq) => $sq->where('country_id', $countryId))
                        ->orWhereHas('item.location', fn ($lq) => $lq->where('country_id', $countryId))
                        ->orWhereHas('seller.primaryLocation', fn ($pq) => $pq->where('country_id', $countryId));
                }
            })
            ->get();
    }

    /**
     * Query popular available listings ordered by ViewedEntity view counts.
     */
    protected function queryPopularListings(int $limit, ?int $countryId = null, array $excludeIds = []): Collection
    {
        $query = Listing::available()
            ->with([
                'item.deviceModel.category',
                'item.deviceModel.brand',
                'item.location.state',
                'seller.primaryLocation.state',
                'media',
                'item.media',
                'activePromotion',
            ])
            ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->withCount('views')
            ->orderByDesc('views_count');

        if ($countryId) {
            $results = (clone $query)->inCurrentCountry($countryId)->take($limit)->get();
            if ($results->isNotEmpty()) {
                return $results;
            }
        }

        return $query->take($limit)->get();
    }

    /**
     * Sort candidate listings with active promotions by lowest achievement percentage first.
     * Lowest achievement percentage = (achieved / target) ascending
     * Equivalent to: (target - achieved) / target descending.
     */
    public function sortByLowestAchievement(Collection $listings): Collection
    {
        return $listings->sortByDesc(function (Listing $listing) {
            $promo = $listing->activePromotion;
            if (! $promo || $promo->target_count <= 0) {
                return -1;
            }

            // Unachieved percentage: the higher this is, the lower the achieved percentage
            $unachieved = (float) ($promo->target_count - $promo->achieved_count);
            return $unachieved / (float) $promo->target_count;
        });
    }

    /**
     * Check if a visitor (User ID or IP Address) has already viewed this listing.
     */
    public function hasVisitorViewed(Listing $listing, ?User $user = null, ?string $ip = null): bool
    {
        $userId = $user?->id ?? Auth::id();
        $ipAddress = $ip ?? request()->ip() ?? '127.0.0.1';
        $morphType = $listing->getMorphClass();

        return ViewedEntity::whereIn('viewable_type', [$morphType, Listing::class])
            ->where('viewable_id', $listing->id)
            ->where(function ($q) use ($userId, $ipAddress) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->whereNull('user_id')->where('ip_address', $ipAddress);
                }
            })
            ->exists();
    }

    /**
     * Create a deduplicated ViewedEntity record. Returns null if already viewed.
     */
    public function recordViewedEntity(
        Listing $listing,
        ?User $user = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $deviceType = null
    ): ?ViewedEntity {
        $userId = $user?->id ?? Auth::id();
        $ipAddress = $ip ?? request()->ip() ?? '127.0.0.1';
        $ua = $userAgent ?? request()->userAgent() ?? '';
        $device = $deviceType ?? $this->detectDeviceType($ua);

        if ($this->hasVisitorViewed($listing, $user, $ipAddress)) {
            return null;
        }

        return ViewedEntity::create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => substr($ua, 0, 255),
            'device_type' => $device,
            'viewable_id' => $listing->id,
            'viewable_type' => $listing->getMorphClass(),
        ]);
    }

    /**
     * Increment achieved_count on an active promotion, marking completed if target reached.
     */
    public function incrementPromotionIfApplicable(Promotion $promotion): bool
    {
        if ($promotion->status !== 'active' || $promotion->achieved_count >= $promotion->target_count) {
            return false;
        }

        $promotion->increment('achieved_count');
        $promotion->refresh();

        if ($promotion->achieved_count >= $promotion->target_count) {
            $promotion->update(['status' => 'completed']);
        }

        return true;
    }

    /**
     * Process retrieved listings for deferred promotion initialization.
     * For all items where activePromotion has type === 'views':
     * Deduplicate via ViewedEntity and increment achieved_count.
     */
    public function processImpressionPromotions(
        Collection $listings,
        ?User $user = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $deviceType = null
    ): void {
        foreach ($listings as $listing) {
            $promo = $listing->activePromotion;
            if ($promo && $promo->type === 'views') {
                if (! $this->hasVisitorViewed($listing, $user, $ip)) {
                    $this->recordViewedEntity($listing, $user, $ip, $userAgent, $deviceType);
                    $this->incrementPromotionIfApplicable($promo);
                }
            }
        }
    }

    /**
     * Detect device type from user agent.
     */
    public function detectDeviceType(?string $userAgent): string
    {
        $ua = $userAgent ?? request()->userAgent() ?? '';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }
}
