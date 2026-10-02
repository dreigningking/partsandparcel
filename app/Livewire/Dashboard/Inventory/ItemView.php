<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Item;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Item Details — Inventory Management')]
class ItemView extends Component
{
    public Item $item;

    // Add Component Modal Properties (for scrap units)
    public bool $showAddComponentModal = false;
    public string $newComponentName = '';
    public string $newComponentCondition = 'Testing working';
    public string $newComponentNotes = '';
    public float $newComponentPrice = 0.00;
    public bool $newComponentListNow = true;
    public int $newComponentWarrantyDays = 7;
    public string $newComponentWarrantyTerms = '7 days testing warranty';

    public function mount(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this inventory item.');
        }

        $this->item = $item->load([
            'deviceModel.category.parent',
            'deviceModel.brand',
            'location',
            'media',
            'parent.deviceModel.brand',
            'children.listing',
            'children.media',
            'listing',
        ]);
    }

    public function openAddComponentModal(): void
    {
        $this->newComponentName = '';
        $this->newComponentCondition = 'Testing working';
        $this->newComponentNotes = '';
        $this->newComponentPrice = 0.00;
        $this->newComponentListNow = true;
        $this->newComponentWarrantyDays = 7;
        $this->newComponentWarrantyTerms = '7 days testing warranty';
        $this->resetErrorBag();
        $this->showAddComponentModal = true;
    }

    public function closeAddComponentModal(): void
    {
        $this->showAddComponentModal = false;
        $this->resetErrorBag();
    }

    public function saveComponent(): void
    {
        $this->validate([
            'newComponentName' => ['required', 'string', 'max:150'],
            'newComponentCondition' => ['required', 'string', 'max:50'],
            'newComponentPrice' => ['nullable', 'numeric', 'min:0'],
        ]);

        $child = Item::create([
            'user_id' => Auth::id(),
            'parent_id' => $this->item->id,
            'location_id' => $this->item->location_id,
            'model_id' => $this->item->model_id,
            'item_type' => 'part',
            'name' => $this->newComponentName,
            'condition_status' => $this->newComponentCondition,
            'condition_notes' => $this->newComponentNotes ?: null,
            'year' => now(),
        ]);

        if ($this->newComponentListNow && $this->newComponentPrice > 0) {
            Listing::create([
                'user_id' => Auth::id(),
                'item_id' => $child->id,
                'quantity' => 1,
                'price' => $this->newComponentPrice,
                'is_negotiable' => false,
                'is_published' => true,
                'warranty_period_days' => $this->newComponentWarrantyDays ?: 0,
                'warranty_terms' => $this->newComponentWarrantyTerms ?: '',
                'allow_shipping' => true,
            ]);
        }

        $this->item->load('children.listing');
        $this->closeAddComponentModal();
        session()->flash('message', 'Harvested component recorded successfully!');
    }

    public function deleteComponent(int $id): void
    {
        $child = $this->item->children()->where('id', $id)->first();
        if ($child) {
            Listing::where('item_id', $child->id)->delete();
            $child->delete();
            $this->item->load('children.listing');
            session()->flash('message', 'Component part deleted from disassembly matrix.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.inventory.item-view');
    }
}
