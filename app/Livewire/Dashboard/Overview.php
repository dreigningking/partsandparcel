<?php

namespace App\Livewire\Dashboard;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discussion;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Settlement;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Dashboard Overview — Parts & Parcel')]
class Overview extends Component
{
    public function render()
    {
        $user = Auth::user();

        if (! $user) {
            return view('livewire.dashboard.overview', [
                'currencySymbol' => '₦',
                'activePurchasesCount' => 0,
                'awaitingDeliveryCount' => 0,
                'cartItemsCount' => 0,
                'cartSellersCount' => 0,
                'totalListingsCount' => 0,
                'activeListingsCount' => 0,
                'soldListingsCount' => 0,
                'totalPendingOffersCount' => 0,
                'offersNeedingAttentionCount' => 0,
                'buyersWaitingCount' => 0,
                'recentPurchases' => collect(),
                'availablePayout' => 0.0,
                'responsesRemaining' => 0,
                'responsesLimit' => 1,
                'recentActivities' => collect(),
                'communityDiscussions' => collect(),
            ]);
        }

        $currencySymbol = $user->country?->currency_symbol ?: '₦';

        // 1. INVOICES & PURCHASES (Buying Activity)
        $buyerInvoicesQuery = Invoice::where('buyer_id', $user->id)
            ->with(['items.itemable', 'seller']);

        $activePurchasesCount = (clone $buyerInvoicesQuery)
            ->whereIn('status', ['paid', 'accepted', 'partially_paid'])
            ->count();

        $awaitingDeliveryCount = (clone $buyerInvoicesQuery)
            ->whereIn('status', ['paid', 'accepted'])
            ->whereNull('delivered_at')
            ->whereNull('completed_at')
            ->count();

        // Recent purchases list (items received or in transit)
        $recentInvoices = (clone $buyerInvoicesQuery)
            ->latest()
            ->take(4)
            ->get();

        $recentPurchases = $recentInvoices->map(function ($inv) {
            $firstItem = $inv->items->first();
            $title = $firstItem?->description ?: ($firstItem?->itemable?->name ?? ('Invoice #' . $inv->invoice_number));

            if ($inv->delivered_at || $inv->completed_at) {
                $statusText = 'Received';
                $statusColor = 'emerald';
                $deliveryStatus = 'Delivered & Completed';
            } elseif ($inv->shipped_at) {
                $statusText = 'In transit';
                $statusColor = 'amber';
                $deliveryStatus = 'Awaiting delivery';
            } elseif ($inv->ready_for_pickup_at) {
                $statusText = 'Ready for pickup';
                $statusColor = 'blue';
                $deliveryStatus = 'Awaiting pickup';
            } elseif ($inv->status === 'paid' || $inv->paid_at) {
                $statusText = 'Processing';
                $statusColor = 'amber';
                $deliveryStatus = 'Awaiting shipment';
            } elseif ($inv->status === 'accepted') {
                $statusText = 'Accepted';
                $statusColor = 'blue';
                $deliveryStatus = 'Payment pending';
            } else {
                $statusText = ucfirst($inv->status);
                $statusColor = 'slate';
                $deliveryStatus = ucfirst($inv->status);
            }

            return [
                'id' => $inv->id,
                'title' => $title,
                'invoice_number' => $inv->invoice_number,
                'delivery_status' => $deliveryStatus,
                'amount' => $inv->total,
                'currency_symbol' => $inv->currency_symbol,
                'status_text' => $statusText,
                'status_color' => $statusColor,
                'created_at' => $inv->created_at,
            ];
        });

        // 2. CART
        $cartItemsCount = CartItem::whereHas('cart', function ($q) use ($user) {
            $q->where('buyer_id', $user->id)->where('status', 'active');
        })->sum('quantity') ?: 0;

        $cartSellersCount = Cart::where('buyer_id', $user->id)
            ->where('status', 'active')
            ->whereHas('items')
            ->distinct('seller_id')
            ->count('seller_id') ?: 0;

        // 3. LISTINGS (Selling Activity)
        $totalListingsCount = Listing::where('user_id', $user->id)->count();

        $activeListingsCount = Listing::where('user_id', $user->id)
            ->where('is_published', true)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->count();

        $soldListingsCount = Listing::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('quantity', '<=', 0)
                  ->orWhere('sold_quantity', '>', 0);
            })
            ->count();

        // 4. OFFERS (Things requiring seller's attention)
        $sellerPendingOffers = Offer::where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->with(['sender', 'items.listing']);

        $offersNeedingAttentionCount = (clone $sellerPendingOffers)->count();

        $buyersWaitingCount = (clone $sellerPendingOffers)
            ->distinct('sender_id')
            ->count('sender_id') ?: 0;

        $totalPendingOffersCount = Offer::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
              ->orWhere('sender_id', $user->id);
        })->where('status', 'pending')->count();

        // 5. SETTLEMENTS (Payout Determination)
        $availablePayout = (float) Settlement::where('seller_id', $user->id)
            ->whereIn('status', ['pending', 'eligible'])
            ->sum('amount');

        // 6. SUBSCRIPTION (Responses left)
        $subscriptionService = app(SubscriptionService::class);
        $usage = $subscriptionService->getUsageStats($user);
        $responsesRemaining = $usage['daily_responses_remaining'] ?? 0;
        $responsesLimit = $usage['daily_response_limit'] ?? 1;

        // 7. COMMUNITY DISCUSSIONS
        $communityDiscussions = Discussion::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('responses', fn ($r) => $r->where('user_id', $user->id));
        })
        ->withCount(['responses', 'offers'])
        ->latest()
        ->take(3)
        ->get();

        if ($communityDiscussions->isEmpty()) {
            $communityDiscussions = Discussion::withCount(['responses', 'offers'])
                ->latest()
                ->take(3)
                ->get();
        }

        // 8. RECENT ACTIVITY TIMELINE (Buying & Selling)
        $activities = collect();

        // Recent offers
        $recentOffers = Offer::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)->orWhere('sender_id', $user->id);
        })->with(['sender', 'recipient', 'items'])->latest()->take(4)->get();

        foreach ($recentOffers as $offer) {
            $isRecipient = $offer->recipient_id === $user->id;
            $otherParty = $isRecipient ? $offer->sender : $offer->recipient;
            $firstItem = $offer->items->first();
            $itemDesc = $firstItem?->description ?: ($firstItem?->listing?->title ?? 'Item');

            if ($offer->status === 'accepted') {
                $actTitle = $isRecipient
                    ? "You accepted an offer from " . ($otherParty->name ?? 'Buyer')
                    : ($otherParty->name ?? 'Seller') . " accepted your offer";
            } elseif ($offer->status === 'pending') {
                $actTitle = $isRecipient
                    ? "New offer received from " . ($otherParty->name ?? 'Buyer')
                    : "You sent an offer to " . ($otherParty->name ?? 'Seller');
            } elseif ($offer->status === 'countered') {
                $actTitle = "Counter-offer from " . ($otherParty->name ?? 'Counterparty');
            } else {
                $actTitle = "Offer " . ucfirst($offer->status) . " (" . ($otherParty->name ?? 'User') . ")";
            }

            $activities->push([
                'title' => $actTitle,
                'subtitle' => $itemDesc . ' · ' . $currencySymbol . number_format($offer->total(), 2) . ' · ' . $offer->created_at->diffForHumans(),
                'timestamp' => $offer->created_at,
                'url' => route('offers.view', ['offer_id' => $offer->id]),
            ]);
        }

        // Recent invoices
        $recentAllInvoices = Invoice::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->with(['items'])->latest()->take(4)->get();

        foreach ($recentAllInvoices as $inv) {
            $isBuyer = $inv->buyer_id === $user->id;
            $firstItem = $inv->items->first();
            $itemDesc = $firstItem?->description ?: 'Order Items';

            if ($inv->completed_at || $inv->delivered_at) {
                $actTitle = $isBuyer ? "Your package was delivered" : "Your package was delivered to buyer";
            } elseif ($inv->shipped_at) {
                $actTitle = $isBuyer ? "Your order is in transit" : "You dispatched order #{$inv->invoice_number}";
            } elseif ($inv->status === 'paid') {
                $actTitle = $isBuyer ? "Payment confirmed for Invoice #{$inv->invoice_number}" : "Payment received for Invoice #{$inv->invoice_number}";
            } else {
                $actTitle = ($isBuyer ? "Purchase " : "Sale ") . "Invoice #{$inv->invoice_number} (" . ucfirst($inv->status) . ")";
            }

            $activities->push([
                'title' => $actTitle,
                'subtitle' => $itemDesc . ' · ' . $inv->currency_symbol . number_format($inv->total, 2) . ' · ' . ($inv->paid_at ?: $inv->created_at)->diffForHumans(),
                'timestamp' => $inv->paid_at ?: $inv->created_at,
                'url' => route('invoices.view', ['invoice_id' => $inv->id]),
            ]);
        }

        $recentActivities = $activities->sortByDesc('timestamp')->take(5)->values();

        return view('livewire.dashboard.overview', [
            'currencySymbol' => $currencySymbol,
            'activePurchasesCount' => $activePurchasesCount,
            'awaitingDeliveryCount' => $awaitingDeliveryCount,
            'cartItemsCount' => $cartItemsCount,
            'cartSellersCount' => $cartSellersCount,
            'totalListingsCount' => $totalListingsCount,
            'activeListingsCount' => $activeListingsCount,
            'soldListingsCount' => $soldListingsCount,
            'totalPendingOffersCount' => $totalPendingOffersCount,
            'offersNeedingAttentionCount' => $offersNeedingAttentionCount,
            'buyersWaitingCount' => $buyersWaitingCount,
            'recentPurchases' => $recentPurchases,
            'availablePayout' => $availablePayout,
            'responsesRemaining' => $responsesRemaining,
            'responsesLimit' => $responsesLimit,
            'recentActivities' => $recentActivities,
            'communityDiscussions' => $communityDiscussions,
        ]);
    }
}
