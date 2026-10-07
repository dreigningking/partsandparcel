<?php

namespace App\Livewire\Dashboard\Offers;

use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OffersList extends Component
{
    public $activeTab = 'all'; // Top filter: 'all', 'sent', 'received'
    public $statusFilter = 'all'; // Secondary filter: 'all', 'pending', 'countered', 'accepted', 'declined', 'expired', 'cancelled'
    public $searchQuery = '';

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
        $userOffers = collect();
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
            $baseQuery = Offer::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });

            // 1. Top filter counts (All, Sent, Received)
            $directionCounts['all'] = (clone $baseQuery)->count();
            $directionCounts['sent'] = Offer::where('sender_id', $user->id)->count();
            $directionCounts['received'] = Offer::where('recipient_id', $user->id)->count();

            // 2. Base query scoped by active direction
            $scopedByDirection = match ($this->activeTab) {
                'sent' => Offer::where('sender_id', $user->id),
                'received' => Offer::where('recipient_id', $user->id),
                default => clone $baseQuery,
            };

            // 3. Status counts within selected direction
            $statusCounts['all'] = (clone $scopedByDirection)->count();
            $statusCounts['pending'] = (clone $scopedByDirection)->where('status', 'pending')->count();
            $statusCounts['countered'] = (clone $scopedByDirection)->where('status', 'countered')->count();
            $statusCounts['accepted'] = (clone $scopedByDirection)->where('status', 'accepted')->count();
            $statusCounts['declined'] = (clone $scopedByDirection)->where('status', 'declined')->count();
            $statusCounts['expired'] = (clone $scopedByDirection)->where('status', 'expired')->count();
            $statusCounts['cancelled'] = (clone $scopedByDirection)->where('status', 'cancelled')->count();

            // 4. Main Query with eager loading for fully dynamic display
            $query = (clone $scopedByDirection)->with([
                'sender.primaryLocation',
                'recipient.primaryLocation',
                'items.listing',
                'cart',
                'discussion',
                'parent.items',
                'counterOffers.items',
            ]);

            // Apply Status Filter
            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            // Apply Search Query
            if (! empty($this->searchQuery)) {
                $term = '%' . $this->searchQuery . '%';
                $query->where(function ($q) use ($term) {
                    $q->whereHas('items', fn ($sub) => $sub->where('description', 'like', $term))
                      ->orWhere('terms', 'like', $term)
                      ->orWhereHas('sender', fn ($sub) => $sub->where('name', 'like', $term)->orWhere('business_name', 'like', $term))
                      ->orWhereHas('recipient', fn ($sub) => $sub->where('name', 'like', $term)->orWhere('business_name', 'like', $term));
                });
            }

            $userOffers = $query->latest('id')->get();
        }

        return view('livewire.dashboard.offers.offers-list', [
            'userOffers' => $userOffers,
            'directionCounts' => $directionCounts,
            'statusCounts' => $statusCounts,
        ]);
    }
}