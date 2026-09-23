<?php

namespace App\Livewire\Dashboard\Requests;

use App\Models\Discussion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyRequests extends Component
{
    public $activeTab = 'open'; // 'open', 'fulfilled', 'closed', 'all'

    public function render()
    {
        $user = Auth::user();
        $userRequests = collect();
        $counts = [
            'open' => 0,
            'fulfilled' => 0,
            'closed' => 0,
            'all' => 0,
        ];

        if ($user) {
            $base = Discussion::where('user_id', $user->id);
            $counts['open'] = (clone $base)->where('status', 'open')->count();
            $counts['fulfilled'] = (clone $base)->where('status', 'resolved')->count();
            $counts['closed'] = (clone $base)->where('status', 'closed')->count();
            $counts['all'] = (clone $base)->count();

            $query = Discussion::with(['category', 'responses', 'offers'])
                ->where('user_id', $user->id);

            match ($this->activeTab) {
                'open' => $query->where('status', 'open'),
                'fulfilled' => $query->where('status', 'resolved'),
                'closed' => $query->where('status', 'closed'),
                default => null,
            };

            $userRequests = $query->latest()->get();
        }

        return view('livewire.dashboard.requests.my-requests', [
            'userRequests' => $userRequests,
            'counts' => $counts,
        ]);
    }
}
