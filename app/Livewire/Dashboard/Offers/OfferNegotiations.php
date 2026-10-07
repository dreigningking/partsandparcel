<?php

namespace App\Livewire\Dashboard\Offers;

use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OfferNegotiations extends Component
{
    public string $activeTab = 'all'; // 'all', 'sent', 'received'
    public string $statusFilter = 'all'; // 'all', 'active', 'accepted', 'declined'
    public string $searchQuery = '';

    public function setDirection(string $direction)
    {
        $this->activeTab = in_array($direction, ['all', 'sent', 'received']) ? $direction : 'all';
    }

    public function setStatus(string $status)
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
            'active' => 0,
            'accepted' => 0,
            'declined' => 0,
        ];

        if ($user) {
            // Fetch all root offers involving the user (or offers that have counter offers or parent)
            $userOffers = Offer::with([
                'sender.primaryLocation',
                'recipient.primaryLocation',
                'items.listing',
                'parent.items',
                'counterOffers.items',
                'counterOffers.sender',
                'counterOffers.recipient',
                'cart',
                'discussion',
            ])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->latest('id')
            ->get();

            // Group offers into negotiation threads by their root offer ID
            $groupedThreads = $userOffers->groupBy(function ($offer) {
                return $offer->parent_id ?: $offer->id;
            });

            $processedThreads = $groupedThreads->map(function ($offersInThread, $rootId) use ($user) {
                $rootOffer = $offersInThread->firstWhere('id', $rootId) ?? $offersInThread->sortBy('id')->first();
                $latestOffer = $offersInThread->sortByDesc('id')->first();
                $roundsCount = $offersInThread->count();

                // Determine other party from latest offer
                $isUserSenderOfLatest = ($latestOffer->sender_id === $user->id);
                $otherParty = $isUserSenderOfLatest ? $latestOffer->recipient : $latestOffer->sender;
                $actionRequired = (! $isUserSenderOfLatest && $latestOffer->status === 'pending');

                // Determine thread status category
                $threadStatus = match ($latestOffer->status) {
                    'accepted' => 'accepted',
                    'declined', 'cancelled' => 'declined',
                    default => 'active', // pending or countered
                };

                return (object) [
                    'root_id' => $rootId,
                    'root_offer' => $rootOffer,
                    'latest_offer' => $latestOffer,
                    'rounds_count' => $roundsCount,
                    'other_party' => $otherParty,
                    'is_user_sender' => ($rootOffer->sender_id === $user->id),
                    'action_required' => $actionRequired,
                    'thread_status' => $threadStatus,
                    'primary_item' => $latestOffer->items->first() ?? $rootOffer->items->first(),
                    'latest_total' => $latestOffer->total(),
                    'original_total' => $rootOffer->total(),
                    'updated_at' => $latestOffer->updated_at ?? $latestOffer->created_at,
                ];
            });

            // 1. Calculate direction counts
            $directionCounts['all'] = $processedThreads->count();
            $directionCounts['sent'] = $processedThreads->where('is_user_sender', true)->count();
            $directionCounts['received'] = $processedThreads->where('is_user_sender', false)->count();

            // 2. Filter by active direction tab
            $filteredThreads = match ($this->activeTab) {
                'sent' => $processedThreads->where('is_user_sender', true),
                'received' => $processedThreads->where('is_user_sender', false),
                default => $processedThreads,
            };

            // 3. Calculate status counts for selected direction
            $statusCounts['all'] = $filteredThreads->count();
            $statusCounts['active'] = $filteredThreads->where('thread_status', 'active')->count();
            $statusCounts['accepted'] = $filteredThreads->where('thread_status', 'accepted')->count();
            $statusCounts['declined'] = $filteredThreads->where('thread_status', 'declined')->count();

            // 4. Filter by status
            if ($this->statusFilter !== 'all') {
                $filteredThreads = $filteredThreads->where('thread_status', $this->statusFilter);
            }

            // 5. Filter by search
            if (! empty($this->searchQuery)) {
                $term = strtolower($this->searchQuery);
                $filteredThreads = $filteredThreads->filter(function ($t) use ($term) {
                    $itemName = strtolower($t->primary_item?->description ?? '');
                    $partyName = strtolower($t->other_party?->name ?? '');
                    $bizName = strtolower($t->other_party?->business_name ?? '');
                    $terms = strtolower($t->latest_offer?->terms ?? '');
                    return str_contains($itemName, $term)
                        || str_contains($partyName, $term)
                        || str_contains($bizName, $term)
                        || str_contains($terms, $term);
                });
            }

            $threads = $filteredThreads->sortByDesc('updated_at')->values();
        }

        return view('livewire.dashboard.offers.offer-negotiations', [
            'negotiationThreads' => $threads,
            'directionCounts' => $directionCounts,
            'statusCounts' => $statusCounts,
        ]);
    }
}
