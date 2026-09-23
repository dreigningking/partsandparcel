<?php

namespace App\Livewire\Dashboard\Offers;

use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class OffersList extends Component
{
    public $activeTab = 'received'; // 'received', 'sent', 'accepted', 'declined', 'all'
    public $searchQuery = '';

    public function render()
    {
        $user = Auth::user();
        $dbOffers = collect();
        $counts = [
            'received' => 0,
            'sent' => 0,
            'accepted' => 0,
            'declined' => 0,
            'all' => 0,
        ];

        if ($user) {
            $baseQuery = Offer::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });

            $counts['received'] = Offer::where('recipient_id', $user->id)->whereIn('status', ['pending', 'countered'])->count();
            $counts['sent'] = Offer::where('sender_id', $user->id)->count();
            $counts['accepted'] = (clone $baseQuery)->where('status', 'accepted')->count();
            $counts['declined'] = (clone $baseQuery)->where('status', 'declined')->count();
            $counts['all'] = (clone $baseQuery)->count();

            $query = Offer::with(['sender.primaryLocation', 'recipient.primaryLocation', 'items.listing', 'cart', 'discussion']);

            match ($this->activeTab) {
                'received' => $query->where('recipient_id', $user->id)->whereIn('status', ['pending', 'countered']),
                'sent' => $query->where('sender_id', $user->id),
                'accepted' => $query->where(fn ($q) => $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'accepted'),
                'declined' => $query->where(fn ($q) => $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'declined'),
                default => $query->where(fn ($q) => $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id)),
            };

            if (! empty($this->searchQuery)) {
                $query->where(function ($q) {
                    $q->whereHas('items', fn ($sub) => $sub->where('description', 'like', "%{$this->searchQuery}%"))
                      ->orWhere('terms', 'like', "%{$this->searchQuery}%");
                });
            }

            $dbOffers = $query->latest()->get();
        }

        return view('livewire.dashboard.offers.offers-list', [
            'userOffers' => $dbOffers,
            'counts' => $counts,
        ]);
    }
}