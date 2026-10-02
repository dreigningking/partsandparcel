<?php

namespace App\Livewire\Marketplace;

use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\ServiceJob;
use App\Models\ServiceReview;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    use WithPagination;

    public User $user;
    public string $tab = 'listings'; // listings, services, reviews
    public string $reviewFilter = 'all'; // all, listings, services

    protected $queryString = [
        'tab' => ['except' => 'listings'],
        'reviewFilter' => ['except' => 'all'],
    ];

    public function mount(User $user)
    {
        $this->user = $user->load(['country', 'primaryLocation', 'role']);
    }

    public function setTab(string $tab)
    {
        if (in_array($tab, ['listings', 'services', 'reviews'])) {
            $this->tab = $tab;
            $this->resetPage();
        }
    }

    public function setReviewFilter(string $filter)
    {
        if (in_array($filter, ['all', 'listings', 'services'])) {
            $this->reviewFilter = $filter;
            $this->resetPage();
        }
    }

    public function render()
    {
        // Listings Count
        $listingsCount = Listing::where('user_id', $this->user->id)
            ->where('is_published', true)
            ->where('is_active', true)
            ->count();

        // Services Count
        $servicesCount = ServiceJob::where('provider_id', $this->user->id)->count();

        // Reviews Stats
        $listingReviewStats = ListingReview::whereIn('listing_id', function ($query) {
            $query->select('id')->from('listings')->where('user_id', $this->user->id);
        });

        $listingReviewsCount = (clone $listingReviewStats)->count();
        $listingReviewsSum = (clone $listingReviewStats)->sum('rating');

        $serviceReviewStats = ServiceReview::where('provider_id', $this->user->id);
        $serviceReviewsCount = (clone $serviceReviewStats)->count();
        $serviceReviewsSum = (clone $serviceReviewStats)->sum('rating');

        $totalReviewsCount = $listingReviewsCount + $serviceReviewsCount;
        $totalRatingSum = $listingReviewsSum + $serviceReviewsSum;
        $averageRating = $totalReviewsCount > 0 ? round($totalRatingSum / $totalReviewsCount, 1) : null;

        // Rating distribution calculation
        $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        if ($totalReviewsCount > 0) {
            $lGrouped = (clone $listingReviewStats)->selectRaw('rating, count(*) as cnt')->groupBy('rating')->pluck('cnt', 'rating');
            $sGrouped = (clone $serviceReviewStats)->selectRaw('rating, count(*) as cnt')->groupBy('rating')->pluck('cnt', 'rating');
            for ($s = 1; $s <= 5; $s++) {
                $starCounts[$s] = (int) (($lGrouped[$s] ?? 0) + ($sGrouped[$s] ?? 0));
            }
        }

        // Active Tab Data Query
        $listings = null;
        $services = null;
        $reviews = null;

        if ($this->tab === 'listings') {
            $listings = Listing::where('user_id', $this->user->id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->with(['item.media', 'media', 'item.deviceModel.brand', 'item.location'])
                ->latest()
                ->paginate(12);
        } elseif ($this->tab === 'services') {
            $services = ServiceJob::where('provider_id', $this->user->id)
                ->with(['item', 'location', 'review.reviewer', 'customer'])
                ->latest()
                ->paginate(10);
        } elseif ($this->tab === 'reviews') {
            if ($this->reviewFilter === 'listings') {
                $reviews = ListingReview::whereIn('listing_id', function ($query) {
                    $query->select('id')->from('listings')->where('user_id', $this->user->id);
                })
                ->with(['user', 'listing.item'])
                ->latest()
                ->paginate(10);
            } elseif ($this->reviewFilter === 'services') {
                $reviews = ServiceReview::where('provider_id', $this->user->id)
                    ->with(['reviewer', 'serviceJob'])
                    ->latest()
                    ->paginate(10);
            } else {
                $listingReviews = ListingReview::whereIn('listing_id', function ($query) {
                    $query->select('id')->from('listings')->where('user_id', $this->user->id);
                })
                ->with(['user', 'listing.item'])
                ->latest()
                ->take(50)
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'id' => $r->id,
                        'type' => 'listing',
                        'reviewer' => $r->user,
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                        'target_title' => $r->listing?->item?->name ?? 'Marketplace Listing',
                        'target_url' => $r->listing ? route('listing-details', $r->listing->id) : null,
                        'created_at' => $r->created_at,
                    ];
                });

                $serviceReviews = ServiceReview::where('provider_id', $this->user->id)
                    ->with(['reviewer', 'serviceJob'])
                    ->latest()
                    ->take(50)
                    ->get()
                    ->map(function ($r) {
                        return (object) [
                            'id' => $r->id,
                            'type' => 'service',
                            'reviewer' => $r->reviewer,
                            'rating' => $r->rating,
                            'comment' => $r->review,
                            'target_title' => $r->serviceJob?->title ?? 'Service Job',
                            'target_url' => null,
                            'created_at' => $r->created_at,
                        ];
                    });

                $reviews = $listingReviews->concat($serviceReviews)->sortByDesc('created_at')->values();
            }
        }

        return view('livewire.marketplace.user-profile', [
            'listings' => $listings,
            'services' => $services,
            'reviews' => $reviews,
            'listingsCount' => $listingsCount,
            'servicesCount' => $servicesCount,
            'listingReviewsCount' => $listingReviewsCount,
            'serviceReviewsCount' => $serviceReviewsCount,
            'totalReviewsCount' => $totalReviewsCount,
            'averageRating' => $averageRating,
            'starCounts' => $starCounts,
        ]);
    }
}
