<?php

namespace App\Livewire\Dashboard\Requests;

use App\Models\Category;
use App\Models\Discussion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class MyRequests extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'open'; // 'open', 'fulfilled', 'closed', 'all'

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = 'all'; // 'all', 'item', 'service', 'advice'

    #[Url(as: 'cat')]
    public string $categoryFilter = 'all';

    #[Url(as: 'sort')]
    public string $sortBy = 'latest'; // 'latest', 'oldest', 'most_offers', 'most_responses'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = 'all';
        $this->categoryFilter = 'all';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function markFulfilled(int $id): void
    {
        $user = Auth::user();
        if (! $user) return;

        $discussion = Discussion::where('user_id', $user->id)->findOrFail($id);
        $discussion->update(['status' => 'resolved']);

        session()->flash('message', "Request #REQ-{$id} has been marked as fulfilled.");
    }

    public function closeRequest(int $id): void
    {
        $user = Auth::user();
        if (! $user) return;

        $discussion = Discussion::where('user_id', $user->id)->findOrFail($id);
        $discussion->update(['status' => 'closed']);

        session()->flash('message', "Request #REQ-{$id} has been closed.");
    }

    public function reopenRequest(int $id): void
    {
        $user = Auth::user();
        if (! $user) return;

        $discussion = Discussion::where('user_id', $user->id)->findOrFail($id);
        $discussion->update(['status' => 'open']);

        session()->flash('message', "Request #REQ-{$id} has been reopened for proposals.");
    }

    public function deleteRequest(int $id): void
    {
        $user = Auth::user();
        if (! $user) return;

        $discussion = Discussion::where('user_id', $user->id)->findOrFail($id);
        $discussion->delete();

        session()->flash('message', "Request #REQ-{$id} has been deleted.");
    }

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
        $categories = collect();

        if ($user) {
            $base = Discussion::where('user_id', $user->id);
            $counts['open'] = (clone $base)->where('status', 'open')->count();
            $counts['fulfilled'] = (clone $base)->whereIn('status', ['resolved', 'fulfilled'])->count();
            $counts['closed'] = (clone $base)->where('status', 'closed')->count();
            $counts['all'] = (clone $base)->count();

            $categories = Category::whereHas('discussions', fn ($q) => $q->where('user_id', $user->id))
                ->orderBy('name')
                ->get();

            $query = Discussion::with([
                'category',
                'brand',
                'deviceModel',
                'location',
                'responses.user',
                'offers.items',
                'offers.sender.primaryLocation',
                'latestModeration',
                'media',
            ])->where('user_id', $user->id);

            // Tab filter
            match ($this->activeTab) {
                'open' => $query->where('status', 'open'),
                'fulfilled' => $query->whereIn('status', ['resolved', 'fulfilled']),
                'closed' => $query->where('status', 'closed'),
                default => null,
            };

            // Search filter
            if (! empty($this->search)) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%")
                        ->orWhere('budget', 'like', "%{$search}%");
                });
            }

            // Type filter
            if ($this->typeFilter !== 'all') {
                $query->where('type', $this->typeFilter);
            }

            // Category filter
            if ($this->categoryFilter !== 'all') {
                $query->where('category_id', $this->categoryFilter);
            }

            // Sorting
            match ($this->sortBy) {
                'oldest' => $query->oldest('id'),
                'most_offers' => $query->withCount('offers')->orderByDesc('offers_count'),
                'most_responses' => $query->withCount('responses')->orderByDesc('responses_count'),
                default => $query->latest('id'),
            };

            $userRequests = $query->paginate(8);
        }

        return view('livewire.dashboard.requests.my-requests', [
            'userRequests' => $userRequests,
            'counts' => $counts,
            'categories' => $categories,
        ]);
    }
}
