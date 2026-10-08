<?php

namespace App\Livewire\Dashboard\Offers;

use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OffersList extends Component
{
    public string $activeTab = 'all'; // Top filter: 'all', 'sent', 'received'
    public string $statusFilter = 'all'; // Secondary filter: 'all', 'pending', 'countered', 'accepted', 'declined', 'expired', 'cancelled'
    public string $searchQuery = '';

    public function setDirection(string $direction): void
    {
        $this->activeTab = in_array($direction, ['all', 'sent', 'received']) ? $direction : 'all';
    }

    public function setStatus(string $status): void
    {
        $this->statusFilter = $status;
    }

    public function render()
    {
        $user = Auth::user();
        $threads = collect();
        $directionCounts = [
            'all' => 0,
            'sent' => 0,
            'received' => 0,
        ];
        $statusCounts = [
            'all' => 0,
            'pending' => 0,
            'countered' => 0,
            'accepted' => 0,
            'declined' => 0,
            'expired' => 0,
            'cancelled' => 0,
        ];

        if ($user) {
            // Fetch all offers involving user with eager loading
            $allUserOffers = Offer::with([
                'sender.primaryLocation.state',
                'recipient.primaryLocation.state',
                'items.listing',
                'parent.items',
                'counterOffers.items',
                'cart',
                'discussion',
            ])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->latest('id')
            ->get();

            // Group offers into negotiation deal threads by root offer ID
            $groupedThreads = $allUserOffers->groupBy(function ($offer) {
                return $offer->parent_id ?: $offer->id;
            });

            $processedThreads = $groupedThreads->map(function ($offersInThread, $rootId) use ($user) {
                $rootOffer = $offersInThread->firstWhere('id', $rootId) ?? $offersInThread->sortBy('id')->first();
                $latestOffer = $offersInThread->sortByDesc('id')->first();
                $roundsCount = $offersInThread->count();

                $isUserSenderOfLatest = ($latestOffer->sender_id === $user->id);
                $isUserSenderOfRoot = ($rootOffer->sender_id === $user->id);
                $otherParty = $isUserSenderOfLatest ? $latestOffer->recipient : $latestOffer->sender;
                $actionRequired = (! $isUserSenderOfLatest && $latestOffer->status === 'pending');

                return (object) [
                    'root_id' => $rootId,
                    'root_offer' => $rootOffer,
                    'latest_offer' => $latestOffer,
                    'rounds_count' => $roundsCount,
                    'other_party' => $otherParty,
                    'is_user_sender' => $isUserSenderOfRoot,
                    'action_required' => $actionRequired,
                    'status' => $latestOffer->status,
                    'primary_item' => $latestOffer->items->first() ?? $rootOffer->items->first(),
                    'items' => $latestOffer->items->isNotEmpty() ? $latestOffer->items : $rootOffer->items,
                    'latest_total' => $latestOffer->total(),
                    'original_total' => $rootOffer->total(),
                    'updated_at' => $latestOffer->updated_at ?? $latestOffer->created_at,
                    'created_at' => $rootOffer->created_at,
                    'expires_at' => $latestOffer->expires_at,
                    'delivery_method' => $latestOffer->delivery_method,
                    'cart_id' => $latestOffer->cart_id ?? $rootOffer->cart_id,
                    'discussion_id' => $latestOffer->discussion_id ?? $rootOffer->discussion_id,
                ];
            });

            // 1. Top filter counts (All, Sent, Received)
            $directionCounts['all'] = $processedThreads->count();
            $directionCounts['sent'] = $processedThreads->where('is_user_sender', true)->count();
            $directionCounts['received'] = $processedThreads->where('is_user_sender', false)->count();

            // 2. Base collection scoped by active direction
            $scopedByDirection = match ($this->activeTab) {
                'sent' => $processedThreads->where('is_user_sender', true),
                'received' => $processedThreads->where('is_user_sender', false),
                default => $processedThreads,
            };

            // 3. Status counts within selected direction
            $statusCounts['all'] = $scopedByDirection->count();
            $statusCounts['pending'] = $scopedByDirection->where('status', 'pending')->count();
            $statusCounts['countered'] = $scopedByDirection->filter(fn ($t) => $t->status === 'countered' || $t->rounds_count > 1)->count();
            $statusCounts['accepted'] = $scopedByDirection->where('status', 'accepted')->count();
            $statusCounts['declined'] = $scopedByDirection->where('status', 'declined')->count();
            $statusCounts['expired'] = $scopedByDirection->where('status', 'expired')->count();
            $statusCounts['cancelled'] = $scopedByDirection->where('status', 'cancelled')->count();

            // 4. Apply status filter
            $filteredThreads = $scopedByDirection;
            if ($this->statusFilter !== 'all') {
                if ($this->statusFilter === 'countered') {
                    $filteredThreads = $filteredThreads->filter(fn ($t) => $t->status === 'countered' || $t->rounds_count > 1);
                } else {
                    $filteredThreads = $filteredThreads->where('status', $this->statusFilter);
                }
            }

            // 5. Apply search query
            if (! empty($this->searchQuery)) {
                $term = strtolower(trim($this->searchQuery));
                $filteredThreads = $filteredThreads->filter(function ($t) use ($term) {
                    $itemName = strtolower($t->primary_item?->description ?? '');
                    $partyName = strtolower($t->other_party?->name ?? '');
                    $bizName = strtolower($t->other_party?->business_name ?? '');
                    $terms = strtolower($t->latest_offer?->terms ?? '');
                    $rootIdStr = (string) $t->root_id;

                    return str_contains($itemName, $term)
                        || str_contains($partyName, $term)
                        || str_contains($bizName, $term)
                        || str_contains($terms, $term)
                        || str_contains($rootIdStr, $term);
                });
            }

            $threads = $filteredThreads->sortByDesc('updated_at')->values();
        }

        return view('livewire.dashboard.offers.offers-list', [
            'threads' => $threads,
            'directionCounts' => $directionCounts,
            'statusCounts' => $statusCounts,
        ]);
    }
}