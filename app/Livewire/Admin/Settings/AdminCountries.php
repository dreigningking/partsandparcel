<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Country;
use App\Services\Location\GeographyService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Countries & Regions — Admin Control Center')]
class AdminCountries extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    // Model Fields
    public string $name = '';
    public string $code = '';
    public string $phone_code = '';
    public string $currency = 'NGN';
    public string $currency_symbol = '₦';
    public string $timezone = 'Africa/Lagos';
    public bool $is_default = false;
    public bool $is_active = true;
    public array $payment_gateway = ['paystack', 'flutterwave'];
    public string $views = '0.0050';
    public string $clicks = '20.00';

    // Search query
    public string $search = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->phone_code = '';
        $this->currency = 'NGN';
        $this->currency_symbol = '₦';
        $this->timezone = 'Africa/Lagos';
        $this->is_default = false;
        $this->is_active = true;
        $this->payment_gateway = ['paystack', 'flutterwave'];
        $this->views = '0.0050';
        $this->clicks = '20.00';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $country = Country::query()->findOrFail($id);
        $this->editingId = $country->id;
        $this->name = $country->name;
        $this->code = (string) $country->code;
        $this->phone_code = (string) ($country->phone_code ?? '');
        $this->currency = (string) ($country->currency ?? 'NGN');
        $this->currency_symbol = (string) ($country->currency_symbol ?? '₦');
        $this->timezone = (string) ($country->timezone ?? 'Africa/Lagos');
        $this->is_default = (bool) $country->is_default;
        $this->is_active = (bool) $country->is_active;
        $this->payment_gateway = is_array($country->payment_gateway) ? $country->payment_gateway : [];
        $this->views = (string) ($country->views ?? '0.0000');
        $this->clicks = (string) ($country->clicks ?? '0.00');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function saveCountry(GeographyService $geographyService): void
    {
        $codeRule = ['required', 'string', 'size:2'];
        $codeRule[] = $this->editingId
            ? Rule::unique('countries', 'code')->ignore($this->editingId)
            : Rule::unique('countries', 'code');

        $this->validate([
            'name'            => ['required', 'string', 'max:255'],
            'code'            => $codeRule,
            'phone_code'      => ['nullable', 'string', 'max:10'],
            'currency'        => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'timezone'        => ['required', 'string', 'max:100'],
            'is_default'      => ['boolean'],
            'is_active'       => ['boolean'],
            'payment_gateway' => ['nullable', 'array'],
            'views'           => ['required', 'numeric', 'min:0', 'max:999999.9999'],
            'clicks'          => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ], [
            'code.size'     => 'Country code must be exactly 2 characters (ISO-2 code, e.g. NG, US, GB).',
            'currency.size' => 'Currency code must be exactly 3 characters (e.g. NGN, USD, GBP).',
        ]);

        $payload = [
            'name'            => trim($this->name),
            'code'            => strtoupper(trim($this->code)),
            'phone_code'      => $this->phone_code ? trim($this->phone_code) : null,
            'currency'        => strtoupper(trim($this->currency)),
            'currency_symbol' => trim($this->currency_symbol),
            'timezone'        => trim($this->timezone),
            'is_active'       => $this->is_active,
            'payment_gateway' => array_values($this->payment_gateway),
            'views'           => (float) $this->views,
            'clicks'          => (float) $this->clicks,
        ];

        if ($this->is_default) {
            Country::query()->update(['is_default' => false]);
            $payload['is_default'] = true;
        } else {
            $payload['is_default'] = false;
        }

        if ($this->editingId) {
            Country::query()->whereKey($this->editingId)->update($payload);
            session()->flash('status', "Country “{$this->name}” updated successfully.");
        } else {
            $country = Country::query()->create($payload);

            // Upon creation of a country, run GeographyService to fetch and persist states
            $geographyService->fetchAndSave($country);

            $statesCount = $country->states()->count();
            $stateMessage = $statesCount > 0 ? " and synchronized {$statesCount} states" : "";
            session()->flash('status', "Country “{$country->name}” created successfully{$stateMessage}.");
        }

        $this->closeModal();
    }

    public function setDefault(int $id): void
    {
        Country::query()->update(['is_default' => false]);
        Country::query()->whereKey($id)->update(['is_default' => true]);
        session()->flash('status', 'Default country updated.');
    }

    public function toggleActive(int $id): void
    {
        $country = Country::query()->findOrFail($id);
        $country->is_active = ! $country->is_active;
        $country->save();
        session()->flash('status', "Country “{$country->name}” is now " . ($country->is_active ? 'Active' : 'Inactive') . '.');
    }

    public function syncStates(int $id, GeographyService $geographyService): void
    {
        $country = Country::query()->findOrFail($id);
        $geographyService->fetchAndSave($country);
        $statesCount = $country->states()->count();
        session()->flash('status', "Synced {$statesCount} states for {$country->name}.");
    }

    public function deleteCountry(int $id): void
    {
        $country = Country::query()->withCount('states')->findOrFail($id);

        if ($country->is_default) {
            session()->flash('error', 'Cannot delete the default country. Please set another country as default first.');
            return;
        }

        $country->states()->delete();
        $country->delete();
        session()->flash('status', "Country “{$country->name}” and its states deleted.");
    }

    public function render(): View
    {
        $countries = Country::query()
            ->withCount('states')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('currency', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_code', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc('is_default')
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.admin.settings.admin-countries', [
            'countries' => $countries,
        ]);
    }
}
