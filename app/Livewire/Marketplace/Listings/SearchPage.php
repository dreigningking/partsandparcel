<?php

namespace App\Livewire\Marketplace\Listings;

use App\Models\Discussion;
use App\Models\Item;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class SearchPage extends Component
{
    #[Url(as: 'q')]
    public $q = '';

    #[Url(as: 'tab')]
    public $tab = 'all'; // 'all', 'devices', 'parts', 'scrap', 'community'

    public function switchTab(string $tabName)
    {
        if (in_array($tabName, ['all', 'devices', 'parts', 'scrap', 'community'])) {
            $this->tab = $tabName;
        }
    }

    public function render()
    {
        $searchTerm = trim($this->q);

        // Fetch Items matching name, description, or model name
        $itemsQuery = Item::with(['deviceModel.category', 'deviceModel.brand', 'user', 'listing'])
            ->where(function ($query) use ($searchTerm) {
                if ($searchTerm !== '') {
                    $query->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('deviceModel', fn ($m) => $m->where('name', 'like', "%{$searchTerm}%"));
                }
            });

        // Apply tab filters if specific item category tab selected
        if ($this->tab === 'devices') {
            $itemsQuery->where('condition_status', '!=', 'scrap');
        } elseif ($this->tab === 'scrap') {
            $itemsQuery->where('condition_status', 'scrap');
        }

        $items = $itemsQuery->latest()->take(30)->get();

        // Fetch Community Discussions & Responses matching title, body, or response body
        $discussionsQuery = Discussion::with(['user', 'category', 'brand', 'deviceModel', 'responses'])
            ->where(function ($query) use ($searchTerm) {
                if ($searchTerm !== '') {
                    $query->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('body', 'like', "%{$searchTerm}%")
                        ->orWhereHas('responses', fn ($r) => $r->where('body', 'like', "%{$searchTerm}%"));
                }
            });

        $discussions = $discussionsQuery->latest()->take(30)->get();

        $totalItemsCount = Item::where(function ($query) use ($searchTerm) {
            if ($searchTerm !== '') {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhereHas('deviceModel', fn ($m) => $m->where('name', 'like', "%{$searchTerm}%"));
            }
        })->count();

        $totalDiscussionsCount = Discussion::where(function ($query) use ($searchTerm) {
            if ($searchTerm !== '') {
                $query->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('body', 'like', "%{$searchTerm}%")
                    ->orWhereHas('responses', fn ($r) => $r->where('body', 'like', "%{$searchTerm}%"));
            }
        })->count();

        return view('livewire.marketplace.listings.search-page', [
            'searchTerm' => $searchTerm,
            'items' => $items,
            'discussions' => $discussions,
            'totalItemsCount' => $totalItemsCount,
            'totalDiscussionsCount' => $totalDiscussionsCount,
        ]);
    }
}
