<?php

namespace App\Livewire\Marketplace;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Help Center & Knowledge Base — Parts & Parcel')]
class Help extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'topic')]
    public string $selectedTopic = '';

    public function filterTopic(string $topic): void
    {
        $this->selectedTopic = $this->selectedTopic === $topic ? '' : $topic;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedTopic = '';
    }

    public function render()
    {
        $isSearching = $this->search !== '' || $this->selectedTopic !== '';

        $searchResults = null;
        if ($isSearching) {
            $searchResults = Post::help()
                ->published()
                ->when($this->search !== '', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('title', 'like', "%{$this->search}%")
                            ->orWhere('excerpt', 'like', "%{$this->search}%")
                            ->orWhere('tags', 'like', "%{$this->search}%")
                            ->orWhere('content', 'like', "%{$this->search}%");
                    });
                })
                ->when($this->selectedTopic !== '', function ($q) {
                    $q->where('tags', 'like', "%{$this->selectedTopic}%");
                })
                ->latest('published_at')
                ->take(30)
                ->get();
        }

        // Preload articles grouped by each of the 6 core topics
        $articlesByTopic = [];
        $topicCounts = [];
        foreach (Post::HELP_TOPICS as $topic) {
            $topicQuery = Post::help()
                ->published()
                ->where('tags', 'like', "%{$topic}%");

            $topicCounts[$topic] = (clone $topicQuery)->count();
            $articlesByTopic[$topic] = $topicQuery->latest('published_at')->take(4)->get();
        }

        return view('livewire.marketplace.help', [
            'isSearching' => $isSearching,
            'searchResults' => $searchResults,
            'articlesByTopic' => $articlesByTopic,
            'topicCounts' => $topicCounts,
            'topics' => Post::HELP_TOPICS,
        ]);
    }
}
