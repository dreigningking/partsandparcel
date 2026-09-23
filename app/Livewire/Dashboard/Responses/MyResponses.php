<?php

namespace App\Livewire\Dashboard\Responses;

use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyResponses extends Component
{
    public $activeTab = 'all'; // 'active', 'accepted', 'declined', 'all'

    public function render()
    {
        $user = Auth::user();
        $userResponses = collect();
        $counts = [
            'active' => 0,
            'accepted' => 0,
            'declined' => 0,
            'all' => 0,
        ];

        if ($user) {
            $base = Response::where('user_id', $user->id);
            $counts['all'] = (clone $base)->count();
            $counts['active'] = (clone $base)->whereHas('offers', fn ($q) => $q->whereIn('status', ['pending', 'countered']))->count();
            $counts['accepted'] = (clone $base)->whereHas('offers', fn ($q) => $q->where('status', 'accepted'))->count();
            $counts['declined'] = (clone $base)->whereHas('offers', fn ($q) => $q->where('status', 'declined'))->count();

            $query = Response::with(['discussion.user.primaryLocation', 'offers.items'])
                ->where('user_id', $user->id);

            match ($this->activeTab) {
                'active' => $query->whereHas('offers', fn ($q) => $q->whereIn('status', ['pending', 'countered'])),
                'accepted' => $query->whereHas('offers', fn ($q) => $q->where('status', 'accepted')),
                'declined' => $query->whereHas('offers', fn ($q) => $q->where('status', 'declined')),
                default => null,
            };

            $userResponses = $query->latest()->get();
        }

        return view('livewire.dashboard.responses.my-responses', [
            'userResponses' => $userResponses,
            'counts' => $counts,
        ]);
    }
}
