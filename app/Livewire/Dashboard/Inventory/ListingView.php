<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Country;
use App\Models\Coupon;
use App\Models\InvoiceItem;
use App\Models\Listing;
use App\Models\OfferItem;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\ViewedEntity;
use App\Models\Wishlist;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Listing Details — Seller Control Center')]
class ListingView extends Component
{
    public Listing $listing;

    // Active Navigation Tab
    public string $activeTab = 'overview'; // 'overview', 'promotions', 'reviews', 'reports', 'specs'

    // Quick Edit Modal Properties
    public bool $showEditModal = false;
    public float $editPrice = 0.0;
    public bool $editIsNegotiable = false;
    public int $editQuantity = 1;
    public bool $is_published = true;
    public int $editWarrantyPeriodDays = 0;
    public bool $editIsWarrantyNegotiable = false;
    public string $editWarrantyTerms = '';
    public bool $editAllowShipping = false;

    // Promotion Purchase Form Properties
    public string $promoType = 'clicks'; // 'clicks' or 'views'
    public int $promoQuantity = 10;
    public string $couponCode = '';
    public ?int $appliedCouponId = null;
    public float $couponDiscount = 0.0;
    public string $couponMessage = '';
    public bool $couponValid = false;
    public string $paymentProvider = 'paystack'; // 'paystack' or 'flutterwave'
    public ?string $purchaseError = null;

    public function mount(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this marketplace listing.');
        }

        $this->listing = $listing->load([
            'item.deviceModel.category.parent',
            'item.deviceModel.brand',
            'item.location',
            'item.media',
            'media',
            'reports.user',
            'reviews.user',
            'latestModeration',
            'promotions.payments',
        ]);

        $this->promoQuantity = $this->getMinQuantity();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
        $this->purchaseError = null;
    }

    public function togglePublish(): void
    {
        $this->listing->update([
            'is_published' => ! $this->listing->is_published,
        ]);
        $this->listing->refresh();
        session()->flash('message', $this->listing->is_published ? 'Listing published to buyers.' : 'Listing paused / made private.');
    }

    public function openEditModal(): void
    {
        $this->editPrice = (float) $this->listing->price;
        $this->editIsNegotiable = (bool) $this->listing->is_negotiable;
        $this->editQuantity = (int) $this->listing->quantity;
        $this->is_published = (bool) $this->listing->is_published;
        $this->editWarrantyPeriodDays = (int) $this->listing->warranty_period_days;
        $this->editIsWarrantyNegotiable = (bool) $this->listing->is_warranty_negotiable;
        $this->editWarrantyTerms = (string) $this->listing->warranty_terms;
        $this->editAllowShipping = (bool) $this->listing->allow_shipping;
        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetErrorBag();
    }

    public function updateListing(): void
    {
        $this->validate([
            'editPrice' => ['required', 'numeric', 'min:0'],
            'editQuantity' => ['required', 'integer', 'min:0'],
            'is_published' => ['required', 'boolean'],
            'editWarrantyTerms' => ['nullable', 'string', new \App\Rules\ProhibitedWordsRule],
        ]);

        $this->listing->update([
            'price' => $this->editPrice,
            'is_negotiable' => $this->editIsNegotiable,
            'quantity' => $this->editQuantity,
            'is_published' => $this->is_published,
            'warranty_period_days' => $this->editWarrantyPeriodDays ?: 0,
            'is_warranty_negotiable' => $this->editIsWarrantyNegotiable,
            'warranty_terms' => $this->editWarrantyTerms ?: '',
            'allow_shipping' => $this->editAllowShipping,
        ]);

        $this->listing->refresh();
        $this->closeEditModal();
        session()->flash('message', 'Marketplace listing updated successfully!');
    }

    // --- PROMOTION CAMPAIGN PURCHASE METHODS ---

    public function getMinClicksProperty(): int
    {
        return max(1, (int) Setting::getValue('minimum_promotion_clicks', 10));
    }

    public function getMinViewsProperty(): int
    {
        return max(100, (int) Setting::getValue('minimum_promotion_views', 10000));
    }

    public function getMinQuantity(): int
    {
        return $this->promoType === 'clicks' ? $this->minClicks : $this->minViews;
    }

    public function setPromoType(string $type): void
    {
        if (! in_array($type, ['clicks', 'views'], true)) {
            return;
        }

        $this->promoType = $type;
        $this->promoQuantity = $this->getMinQuantity();
        $this->recalculateCoupon();
        $this->purchaseError = null;
    }

    public function incrementPromoQuantity(int $amount): void
    {
        $this->promoQuantity = max($this->getMinQuantity(), $this->promoQuantity + $amount);
        $this->recalculateCoupon();
    }

    public function decrementPromoQuantity(int $amount): void
    {
        $this->promoQuantity = max($this->getMinQuantity(), $this->promoQuantity - $amount);
        $this->recalculateCoupon();
    }

    public function updatedPromoQuantity(): void
    {
        $min = $this->getMinQuantity();
        if ($this->promoQuantity < $min) {
            $this->promoQuantity = $min;
        }
        $this->recalculateCoupon();
    }

    public function getSellerCountry(): Country
    {
        $user = Auth::user();
        $country = $user?->country ?? Country::find($user?->country_id) ?? $user?->primaryLocation?->state?->country;

        if (! $country) {
            $country = Country::where('code', 'NG')->first() ?? Country::where('is_default', true)->first();
        }

        if (! $country) {
            $country = new Country([
                'name' => 'Nigeria',
                'code' => 'NG',
                'currency' => 'NGN',
                'currency_symbol' => '₦',
                'views' => 0.0050,
                'clicks' => 20.00,
            ]);
        }

        return $country;
    }

    public function getSubtotalProperty(): float
    {
        $country = $this->getSellerCountry();
        $unitPrice = $this->promoType === 'clicks' ? (float) ($country->clicks ?? 20.00) : (float) ($country->views ?? 0.0050);

        return round($this->promoQuantity * $unitPrice, 2);
    }

    public function getTotalAmountProperty(): float
    {
        return max(0.0, round($this->subtotal - $this->couponDiscount, 2));
    }

    public function applyCoupon(): void
    {
        $this->resetErrorBag('couponCode');
        $this->couponMessage = '';
        $this->purchaseError = null;

        $code = trim($this->couponCode);
        if (empty($code)) {
            $this->addError('couponCode', 'Please enter a coupon code.');
            return;
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            $this->appliedCouponId = null;
            $this->couponDiscount = 0.0;
            $this->couponValid = false;
            $this->addError('couponCode', 'Invalid coupon code.');
            return;
        }

        $subtotal = $this->subtotal;
        $eligibility = $coupon->validateEligibility($subtotal);

        if (! $eligibility['valid']) {
            $this->appliedCouponId = null;
            $this->couponDiscount = 0.0;
            $this->couponValid = false;
            $this->addError('couponCode', $eligibility['message']);
            return;
        }

        $this->appliedCouponId = $coupon->id;
        $this->couponDiscount = $coupon->calculateDiscount($subtotal);
        $this->couponValid = true;
        $this->couponMessage = "Coupon '{$coupon->code}' applied: " . ($coupon->type === 'percentage' ? "{$coupon->value}% off" : "₦" . number_format($coupon->value, 2) . " off");
    }

    public function removeCoupon(): void
    {
        $this->appliedCouponId = null;
        $this->couponDiscount = 0.0;
        $this->couponCode = '';
        $this->couponMessage = '';
        $this->couponValid = false;
        $this->resetErrorBag('couponCode');
    }

    protected function recalculateCoupon(): void
    {
        if ($this->appliedCouponId) {
            $coupon = Coupon::find($this->appliedCouponId);
            if ($coupon) {
                $subtotal = $this->subtotal;
                $eligibility = $coupon->validateEligibility($subtotal);
                if ($eligibility['valid']) {
                    $this->couponDiscount = $coupon->calculateDiscount($subtotal);
                    $this->couponValid = true;
                } else {
                    $this->removeCoupon();
                }
            } else {
                $this->removeCoupon();
            }
        }
    }

    public function processPromotionPayment(PaystackService $paystack, FlutterwaveService $flutterwave)
    {
        $this->purchaseError = null;

        $min = $this->getMinQuantity();
        if ($this->promoQuantity < $min) {
            $this->purchaseError = "The minimum number of {$this->promoType} you can purchase is " . number_format($min) . ".";
            return;
        }

        $country = $this->getSellerCountry();
        $subtotal = $this->subtotal;
        $finalAmount = $this->totalAmount;
        $user = Auth::user();
        $currency = $country->currency ?? 'NGN';
        $reference = 'PROM-' . strtoupper(Str::random(12));

        $couponCode = null;
        if ($this->appliedCouponId) {
            $coupon = Coupon::find($this->appliedCouponId);
            $couponCode = $coupon?->code;
        }

        // Create pending (or active if free) Promotion record
        $isFree = ($finalAmount <= 0.0);
        $promotion = Promotion::create([
            'user_id' => $user->id,
            'listing_id' => $this->listing->id,
            'type' => $this->promoType,
            'target_count' => $this->promoQuantity,
            'achieved_count' => 0,
            'status' => $isFree ? 'active' : 'pending',
        ]);

        $unitPrice = $this->promoType === 'clicks' ? (float) ($country->clicks ?? 20.00) : (float) ($country->views ?? 0.0050);

        // Create Payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'paymentable_id' => $promotion->id,
            'paymentable_type' => Promotion::class,
            'reference' => $reference,
            'provider' => $this->paymentProvider,
            'status' => $isFree ? 'successful' : 'pending',
            'amount' => $finalAmount,
            'currency' => $currency,
            'paid_at' => $isFree ? now() : null,
            'metadata' => [
                'payment_type' => 'promotion',
                'promotion_id' => $promotion->id,
                'listing_id' => $this->listing->id,
                'country_id' => $country->id ?? null,
                'type' => $this->promoType,
                'quantity' => $this->promoQuantity,
                'target_count' => $this->promoQuantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'discount' => $this->couponDiscount,
                'coupon_code' => $couponCode,
            ],
        ]);

        // If coupon gave 100% discount, activate promotion immediately without gateway
        if ($isFree) {
            if ($couponCode && isset($coupon)) {
                $coupon->recordUsage();
            }

            session()->flash('message', "Promotion campaign activated successfully! " . number_format($this->promoQuantity) . " promotional {$this->promoType} added.");
            $this->removeCoupon();
            $this->promoQuantity = $this->getMinQuantity();
            $this->listing->refresh();
            $this->activeTab = 'promotions';
            return;
        }

        // Gateway checkout initialization
        $callbackUrl = route('payment.callback', ['reference' => $reference, 'provider' => $this->paymentProvider]);
        $authorizationUrl = null;
        $gatewayKey = config("services.{$this->paymentProvider}.secret");

        if (empty($gatewayKey) && app()->isLocal()) {
            $authorizationUrl = route('payment.callback', [
                'reference' => $reference,
                'provider' => $this->paymentProvider,
                'mock_success' => 1,
            ]);
        } else {
            try {
                if ($this->paymentProvider === 'flutterwave') {
                    $response = $flutterwave->initialize($payment, $callbackUrl);
                    $authorizationUrl = $response['link'] ?? $response['authorization_url'] ?? null;
                } else {
                    $response = $paystack->initialize($payment, $callbackUrl);
                    $authorizationUrl = $response['authorization_url'] ?? null;
                }
            } catch (\Throwable $e) {
                $this->purchaseError = 'Failed to communicate with payment gateway: ' . $e->getMessage();
                return;
            }
        }

        if ($authorizationUrl) {
            return redirect()->away($authorizationUrl);
        }

        $this->purchaseError = 'Could not initialize payment with ' . ucfirst($this->paymentProvider) . '. Please verify gateway credentials or select another provider.';
    }

    public function render()
    {
        // 1. Analytics & Views
        $viewsCount = ViewedEntity::where('viewable_type', Listing::class)
            ->where('viewable_id', $this->listing->id)
            ->count();

        $wishlistsCount = Wishlist::where('listing_id', $this->listing->id)->count();

        $grossRevenue = ((float) $this->listing->price) * ($this->listing->sold_quantity ?? 0);

        // 2. Listing Offers Count
        $offersCount = OfferItem::where('listing_id', $this->listing->id)
            ->distinct('offer_id')
            ->count('offer_id');

        // 3. Closed Order Count (invoice items where the invoice status is paid)
        $closedOrdersCount = InvoiceItem::where('itemable_type', Listing::class)
            ->where('itemable_id', $this->listing->id)
            ->whereHas('invoice', fn($q) => $q->where('status', 'paid')->orWhereNotNull('paid_at'))
            ->count();

        // 4. Open Order Count (invoice items where invoice is issued, accepted, or partially paid)
        $openOrdersCount = InvoiceItem::where('itemable_type', Listing::class)
            ->where('itemable_id', $this->listing->id)
            ->whereHas('invoice', fn($q) => $q->whereIn('status', ['issued', 'accepted', 'partially_paid'])->whereNull('paid_at'))
            ->count();

        // 5. Promotions & Achievements
        $promotions = $this->listing->promotions()->with('payments')->latest()->get();
        $ongoingPromotions = $promotions->where('status', 'active');
        $totalAchievedClicks = $promotions->where('type', 'clicks')->sum('achieved_count');
        $totalAchievedViews = $promotions->where('type', 'views')->sum('achieved_count');

        // 6. Pricing Plan & Seller Country
        $sellerCountry = $this->getSellerCountry();
        $user = Auth::user();
        $currencySymbol = $sellerCountry->currency_symbol ?? '₦';
        $countryName = $sellerCountry->name ?? 'Default Region';

        // 7. Approval & Overall Listing Status
        $approvalStatus = $this->listing->latestModeration?->status ?? 'pending';
        $listingStatus = $this->listing->status ?? ($this->listing->is_published ? 'live' : 'draft');

        return view('livewire.dashboard.inventory.listing-view', [
            'viewsCount' => $viewsCount,
            'wishlistsCount' => $wishlistsCount,
            'grossRevenue' => $grossRevenue,
            'offersCount' => $offersCount,
            'closedOrdersCount' => $closedOrdersCount,
            'openOrdersCount' => $openOrdersCount,
            'promotions' => $promotions,
            'ongoingPromotions' => $ongoingPromotions,
            'totalAchievedClicks' => $totalAchievedClicks,
            'totalAchievedViews' => $totalAchievedViews,
            'sellerCountry' => $sellerCountry,
            'promotionPlan' => $sellerCountry,
            'currencySymbol' => $currencySymbol,
            'countryName' => $countryName,
            'approvalStatus' => $approvalStatus,
            'listingStatus' => $listingStatus,
            'minClicks' => $this->minClicks,
            'minViews' => $this->minViews,
            'subtotal' => $this->subtotal,
            'totalAmount' => $this->totalAmount,
        ]);
    }
}
