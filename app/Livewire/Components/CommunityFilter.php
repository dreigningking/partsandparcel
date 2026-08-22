<?php

namespace App\Livewire\Components;

use Livewire\Component;

class CommunityFilter extends Component
{
    public $search = '';
    public $category = '';
    public $type = '';
    public $location = '';
    public $budgetMin = '';
    public $budgetMax = '';
    public $status = ['open'];
    public $isMobile = false;

    public function mount($isMobile = false)
    {
        $this->isMobile = $isMobile;
    }

    public function updated($property)
    {
        $this->dispatchFilterUpdate();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->type = '';
        $this->location = '';
        $this->budgetMin = '';
        $this->budgetMax = '';
        $this->status = ['open'];

        $this->dispatchFilterUpdate();
    }

    protected function dispatchFilterUpdate()
    {
        $this->dispatch('filtersUpdated', [
            'search' => $this->search,
            'category' => $this->category,
            'type' => $this->type,
            'location' => $this->location,
            'budgetMin' => $this->budgetMin,
            'budgetMax' => $this->budgetMax,
            'status' => $this->status,
        ]);
    }

    public function render()
    {
        return view('livewire.components.community-filter');
    }
}
