<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\CartItem;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Report;
use App\Models\ViewedEntity;
use App\Models\Wishlist;
use App\Notifications\ListingReportedNotification;
use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class ListingDetails extends Component
{
    public Listing $listing;
    public int $soldCount = 0;
    public $relatedDiscussions;
    public $similarListings;
    public $reviews;
    public array $allMedia = [];
    public bool $isWishlisted = false;
    public bool $isReported = false;
    public string $title = 'Listing Details — Parts & Parcel';

    // Report Listing Modal Properties
    public bool $showReportModal = false;
    public string $reportTitle = '';
    public string $reportDescription = '';
    public string $presetReportReason = '';

    public function mount(Listing $listing)
    {
        $this->listing = $listing->load([
            'item.deviceModel.category.parent',
            'item.deviceModel.brand',
            'item.children',
            'item.location',
            'seller.primaryLocation',
            'media',
            'item.media',
            'reviews.user',
        ]);

        $this->title = (($this->listing->item?->name ?? $this->listing->title) ?? 'Listing Details') . ' — Parts & Parcel';

        // 1. Sold Count: calculate from cart_items where cart has paid invoice, fallback to listing.sold_quantity
        $paidCount = CartItem::where('listing_id', $this->listing->id)
            ->whereHas('cart.invoice', fn($q) => $q->whereNotNull('paid_at'))
            ->sum('quantity');
        $this->soldCount = (int) ($paidCount > 0 ? $paidCount : ($this->listing->sold_quantity ?? 0));

        // 2. All Media (Listing media + Item media)
        $itemMedia = $this->listing->item?->media ?? collect();
        $mediaColl = $this->listing->media->concat($itemMedia)->unique('id')->values();
        $this->allMedia = $mediaColl->all();

        // 3. Wishlist State
        if (Auth::check()) {
            $this->isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('listing_id', $this->listing->id)
                ->exists();

            $this->isReported = Report::where('user_id', Auth::id())
                ->where('reportable_type', Listing::class)
                ->where('reportable_id', $this->listing->id)
                ->exists();
        }

        // 4. Log Impression to ViewedEntity
        $this->logViewedEntity();

        // 5. Related Discussions (up to 6)
        $item = $this->listing->item;
        $catId = $item?->deviceModel?->category_id;
        $brandId = $item?->deviceModel?->brand_id;
        $modelId = $item?->model_id;

        $this->relatedDiscussions = Discussion::with(['user', 'category', 'brand', 'deviceModel', 'responses'])
            ->withCount('responses')
            ->when($catId || $brandId || $modelId, function ($q) use ($catId, $brandId, $modelId) {
                $q->where(function ($sub) use ($catId, $brandId, $modelId) {
                    if ($modelId) {
                        $sub->orWhere('model_id', $modelId);
                    }
                    if ($brandId) {
                        $sub->orWhere('brand_id', $brandId);
                    }
                    if ($catId) {
                        $sub->orWhere('category_id', $catId);
                    }
                });
            })
            ->latest()
            ->take(6)
            ->get();

        // 6. Similar Listings (up to 5)
        $this->similarListings = Listing::with(['item.deviceModel.brand', 'item.location', 'media', 'item.media'])
            ->where('id', '!=', $this->listing->id)
            ->where('is_published', true)
            ->where('is_active', true)
            ->when($catId || $brandId, function ($q) use ($catId, $brandId) {
                $q->whereHas('item.deviceModel', function ($dm) use ($catId, $brandId) {
                    if ($catId) {
                        $dm->where('category_id', $catId);
                    }
                    if ($brandId) {
                        $dm->orWhere('brand_id', $brandId);
                    }
                });
            })
            ->latest()
            ->take(5)
            ->get();

        // 7. Reviews (up to 6)
        $this->reviews = $this->listing->reviews()->with('user')->latest()->take(6)->get();
    }

    protected function logViewedEntity(): void
    {
        $userAgent = request()->userAgent() ?? '';
        $deviceType = 'desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            $deviceType = 'mobile';
        }

        if (Auth::check()) {
            ViewedEntity::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'viewable_id' => $this->listing->id,
                    'viewable_type' => Listing::class,
                ],
                [
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => substr($userAgent, 0, 255),
                    'device_type' => $deviceType,
                ]
            );
        } else {
            ViewedEntity::firstOrCreate(
                [
                    'user_id' => null,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'viewable_id' => $this->listing->id,
                    'viewable_type' => Listing::class,
                ],
                [
                    'user_agent' => substr($userAgent, 0, 255),
                    'device_type' => $deviceType,
                ]
            );
        }
    }

    public function addToCart(CartService $cartService)
    {
        $user = Auth::user();

        $cartService->addToCart($user, $this->listing, 1);
        $this->dispatch('cart-updated');
        session()->flash('cart_success', 'Item added to your cart!');
    }

    public function toggleWishlist()
    {
        $user = Auth::user();
        if (! $user) {
            session()->put('url.intended', url()->current());
            session()->flash('info', 'Please sign in to save items to your wishlist.');
            return redirect()->route('login');
        }

        $existing = Wishlist::where('user_id', $user->id)
            ->where('listing_id', $this->listing->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isWishlisted = false;
            session()->flash('wishlist_message', 'Item removed from your wishlist.');
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'listing_id' => $this->listing->id,
            ]);
            $this->isWishlisted = true;
            session()->flash('wishlist_message', 'Item saved to your wishlist!');
        }
    }

    // --- REPORT LISTING MODAL METHODS ---
    public function openReportModal(): void
    {
        if (! Auth::check()) {
            session()->put('url.intended', url()->current());
            session()->flash('info', 'Please sign in to report this listing.');
            $this->redirectRoute('login');
            return;
        }

        $this->dispatch('open-report-modal', type: 'listing', id: $this->listing->id);
    }

    #[On('report-submitted')]
    public function onReportSubmitted($payload = null): void
    {
        if (is_array($payload) && ($payload['type'] ?? '') === 'listing' && (int) ($payload['id'] ?? 0) === $this->listing->id) {
            $this->isReported = true;
        }
    }

    public function render()
    {
        return view('livewire.marketplace.listings.listing-details');
    }
}
