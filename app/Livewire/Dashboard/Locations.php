<?php

namespace App\Livewire\Dashboard;

use App\Models\Country;
use App\Models\Location;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Saved Locations — Parts & Parcel')]
class Locations extends Component
{
    public string $search = '';

    // Modal state
    public bool $showLocationModal = false;
    public ?int $editingLocationId = null;
    public ?int $confirmingDeleteId = null;

    // Form fields
    public string $label = '';
    public string $contact_name = '';
    public string $phone = '';
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $city = '';
    public ?int $state_id = null;
    public ?int $country_id = null;
    public string $postal_code = '';
    public bool $is_default = false;

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:100',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state_id' => 'required|exists:states,id',
            'phone' => 'nullable|string|max:50',
            'contact_name' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ];
    }

    protected $messages = [
        'label.required' => 'Please enter a name or label for this location (e.g. Main Shop).',
        'address_line_1.required' => 'Street address is required.',
        'city.required' => 'City name is required.',
        'state_id.required' => 'Please select a state.',
        'state_id.exists' => 'The selected state is invalid.',
    ];

    public function openCreateModal(): void
    {
        $this->resetErrorBag();
        $this->editingLocationId = null;
        $this->label = '';
        $this->contact_name = Auth::user()?->name ?? '';
        $this->phone = Auth::user()?->phone ?? '';
        $this->address_line_1 = '';
        $this->address_line_2 = '';
        $this->city = '';
        $this->state_id = null;
        $this->postal_code = '';

        $country = Country::where('is_default', true)->first() ?? Country::first();
        $this->country_id = $country?->id;

        // If user has no existing locations, auto-check is_default
        $hasExisting = Auth::user()?->locations()->count() > 0;
        $this->is_default = !$hasExisting;

        $this->showLocationModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetErrorBag();
        $location = Auth::user()->locations()->findOrFail($id);

        $this->editingLocationId = $location->id;
        $this->label = $location->label ?? '';
        $this->contact_name = $location->contact_name ?? '';
        $this->phone = $location->phone ?? '';
        $this->address_line_1 = $location->address_line_1 ?? '';
        $this->address_line_2 = $location->address_line_2 ?? '';
        $this->city = $location->city ?? '';
        $this->state_id = $location->state_id;
        $this->country_id = $location->country_id;
        $this->postal_code = $location->postal_code ?? '';
        $this->is_default = (bool) $location->is_default;

        $this->showLocationModal = true;
    }

    public function closeModal(): void
    {
        $this->showLocationModal = false;
        $this->editingLocationId = null;
        $this->resetErrorBag();
    }

    public function saveLocation(): void
    {
        $this->validate();

        $user = Auth::user();
        $state = State::find($this->state_id);

        $latitude = $state?->latitude;
        $longitude = $state?->longitude;
        $countryId = $state?->country_id ?? ($this->country_id ?: Country::where('is_default', true)->value('id'));

        // Handle default location logic: Only one location can be default for a user
        $totalLocations = $user->locations()->count();
        $isDefaultToSave = $this->is_default;

        if ($totalLocations === 0 || ($totalLocations === 1 && $this->editingLocationId)) {
            $isDefaultToSave = true;
        }

        if ($isDefaultToSave) {
            $user->locations()->where('id', '!=', $this->editingLocationId)->update(['is_default' => false]);
        } else {
            // Check if there are other locations marked default; if not, force this one to stay default
            $otherDefaultExists = $user->locations()
                ->where('id', '!=', $this->editingLocationId)
                ->where('is_default', true)
                ->exists();

            if (!$otherDefaultExists && $totalLocations > 0) {
                $isDefaultToSave = true;
            }
        }

        $payload = [
            'label' => $this->label,
            'contact_name' => $this->contact_name ?: $user->name,
            'phone' => $this->phone ?: $user->phone,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2 ?: null,
            'country_id' => $countryId,
            'state_id' => $state?->id,
            'city' => $this->city,
            'postal_code' => $this->postal_code ?: null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'is_default' => $isDefaultToSave,
        ];

        if ($this->editingLocationId) {
            $location = $user->locations()->findOrFail($this->editingLocationId);
            $location->update($payload);
            session()->flash('success', "Location '{$location->label}' updated successfully!");
        } else {
            $created = $user->locations()->create($payload);
            session()->flash('success', "Location '{$created->label}' added successfully!");
        }

        $this->closeModal();
    }

    public function setDefault(int $id): void
    {
        $user = Auth::user();
        $location = $user->locations()->findOrFail($id);

        $user->locations()->update(['is_default' => false]);
        $location->update(['is_default' => true]);

        session()->flash('success', "'{$location->label}' is now your default address.");
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteLocation(int $id): void
    {
        $user = Auth::user();
        $location = $user->locations()->findOrFail($id);
        $wasDefault = $location->is_default;
        $name = $location->label;

        $location->delete();

        // If the deleted location was default, make the next oldest location default
        if ($wasDefault) {
            $user->locations()->first()?->update(['is_default' => true]);
        }

        $this->confirmingDeleteId = null;
        session()->flash('success', "Location '{$name}' has been deleted.");
    }

    public function render()
    {
        $user = Auth::user();

        $locations = $user ? $user->locations()
            ->with(['state', 'country'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('label', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('address_line_1', 'like', "%{$this->search}%")
                        ->orWhereHas('state', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderByDesc('is_default')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name', 'latitude', 'longitude']);

        return view('livewire.dashboard.locations', [
            'locations' => $locations,
            'states' => $states,
            'totalCount' => $user ? $user->locations()->count() : 0,
            'defaultLocation' => $user ? $user->locations()->where('is_default', true)->first() : null,
        ]);
    }
}
