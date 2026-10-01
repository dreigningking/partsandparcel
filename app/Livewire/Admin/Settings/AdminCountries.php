<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Country;
use App\Models\CountrySetting;
use App\Models\Currency;
use App\Services\GeographyService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminCountries extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $code = '';

    public string $slug = '';

    public string $flag = '';

    public string $phone_code = '';

    public string $language_code = '';

    public bool $is_default = false;

    public bool $is_active = true;

    public ?int $currency_id = null;

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->slug = '';
        $this->flag = '';
        $this->phone_code = '';
        $this->language_code = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->currency_id = Currency::query()->where('is_active', true)->orderByDesc('is_default')->value('id');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $country = Country::query()->findOrFail($id);
        $this->editingId = $country->id;
        $this->name = $country->name;
        $this->code = (string) ($country->code ?? '');
        $this->slug = (string) ($country->slug ?? '');
        $this->flag = (string) ($country->flag ?? '');
        $this->phone_code = (string) ($country->phone_code ?? '');
        $this->language_code = (string) ($country->language_code ?? '');
        $this->is_default = (bool) $country->is_default;
        $this->is_active = (bool) $country->is_active;
        $this->currency_id = $country->currency_id;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function saveCountry(): void
    {
        $codeRules = ['nullable', 'string', 'max:8'];
        if ($this->code !== '') {
            $codeRules[] = $this->editingId
                ? Rule::unique('countries', 'code')->ignore($this->editingId)
                : Rule::unique('countries', 'code');
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => $codeRules,
            'slug' => ['nullable', 'string', 'max:255'],
            'flag' => ['nullable', 'string', 'max:32'],
            'phone_code' => ['nullable', 'string', 'max:32'],
            'language_code' => ['nullable', 'string', 'max:16'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],
        ]);

        $payload = [
            'name'          => $this->name,
            'code'          => $this->code !== '' ? strtoupper($this->code) : null,
            'slug'          => $this->slug !== '' ? Str::slug($this->slug) : null,
            'flag'          => $this->flag !== '' ? $this->flag : null,
            'phone_code'    => $this->phone_code !== '' ? $this->phone_code : null,
            'language_code' => $this->language_code !== '' ? $this->language_code : null,
            'is_active'     => $this->is_active,
            'currency_id'   => $this->currency_id,
        ];

        if ($this->is_default) {
            Country::query()->update(['is_default' => false]);
            $payload['is_default'] = true;
        } else {
            $payload['is_default'] = false;
        }

        if ($this->editingId) {
            Country::query()->whereKey($this->editingId)->update($payload);
        } else {
            $country = Country::query()->create($payload);

            // Fetch states & cities for the newly created country in background
            if ($country && ! $country->states()->exists()) {
                dispatch(function () use ($country) {
                    app(GeographyService::class)->fetchAndSave($country);
                })->afterResponse();
            }
        }

        session()->flash('status', __('Country saved.'));
        $this->closeModal();
    }

    public function setDefault(int $id): void
    {
        Country::query()->update(['is_default' => false]);
        Country::query()->whereKey($id)->update(['is_default' => true]);
        session()->flash('status', __('Default country updated.'));
    }

    public function deleteCountry(int $id): void
    {
        $country = Country::query()->findOrFail($id);
        if ($country->users()->exists() || $country->properties()->exists()) {
            session()->flash('error', __('Cannot delete a country that has users or properties.'));

            return;
        }
        if ($country->is_default) {
            session()->flash('error', __('Unset default before deleting.'));

            return;
        }
        CountrySetting::query()->where('country_id', $country->id)->delete();
        $country->delete();
        session()->flash('status', __('Country deleted.'));
    }

    public function render()
    {
        $countries = Country::query()->with('currency')->orderByDesc('is_default')->orderBy('name', 'asc')->get();
        $currencies = Currency::query()->where('is_active', true)->orderBy('code')->get();

        return view('livewire.admin.settings.admin-countries', [
            'countries' => $countries,
            'currencies' => $currencies,
        ]);
    }
}
