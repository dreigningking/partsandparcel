<?php

namespace App\Livewire\Components;

use App\Models\Country;
use App\Models\Location;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AddLocationModal extends Component
{
    public bool $isOpen = false;

    public string $label = '';
    public string $address_line_1 = '';
    public string $city = '';
    public ?int $state_id = null;
    public string $contact_name = '';
    public string $phone = '';

    #[On('open-location-modal')]
    public function openModal()
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Please sign in to add a location.');
            return redirect()->route('login');
        }

        $user = Auth::user();
        $this->contact_name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->resetValidation();
        $this->isOpen = true;
    }

    #[On('close-location-modal')]
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['label', 'address_line_1', 'city', 'state_id', 'contact_name', 'phone']);
        $this->resetValidation();
    }

    public function createLocation()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to add a location.');
            return redirect()->route('login');
        }

        $this->validate([
            'label' => 'required|string|max:100',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state_id' => 'required|exists:states,id',
            'phone' => 'nullable|string|max:50',
            'contact_name' => 'nullable|string|max:100',
        ], [
            'label.required' => 'Please provide a location label (e.g. Main Workshop, Office, Home).',
            'address_line_1.required' => 'Street address is required.',
            'city.required' => 'City is required.',
            'state_id.required' => 'Please select a state.',
        ]);

        $state = State::find($this->state_id);
        $isFirst = $user->locations()->count() === 0;

        $countryId = $state?->country_id 
            ?? $user->country_id 
            ?? Country::where('is_default', true)->value('id') 
            ?? 1;

        $location = Location::create([
            'user_id' => $user->id,
            'label' => $this->label,
            'contact_name' => $this->contact_name ?: $user->name,
            'phone' => $this->phone ?: $user->phone,
            'address_line_1' => $this->address_line_1,
            'city' => $this->city,
            'state_id' => $state?->id,
            'country_id' => $countryId,
            'latitude' => $state?->latitude,
            'longitude' => $state?->longitude,
            'is_default' => $isFirst,
        ]);

        $this->isOpen = false;
        $this->reset(['label', 'address_line_1', 'city', 'state_id', 'contact_name', 'phone']);

        $this->dispatch('location-created', id: $location->id, label: $location->label, city: $location->city);
        $this->dispatch('flash-message', type: 'success', message: "Location '{$location->label}' saved successfully!");
    }

    public function render()
    {
        $user = Auth::user();
        $countryId = $user?->country_id ?? Country::where('is_default', true)->value('id');

        $states = collect();
        if ($countryId) {
            $states = State::where('country_id', $countryId)->orderBy('name')->get();
        }

        if ($states->isEmpty()) {
            $states = State::where('is_active', true)->orderBy('name')->get();
        }

        if ($states->isEmpty()) {
            $states = State::orderBy('name')->take(100)->get();
        }

        return view('livewire.components.add-location-modal', [
            'states' => $states,
        ]);
    }
}
