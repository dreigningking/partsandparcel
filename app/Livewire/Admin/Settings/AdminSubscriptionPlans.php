<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Currency;
use App\Models\Price;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminSubscriptionPlans extends Component
{
    const FEATURE_KEYS = [
        'max_listings',
        'featured_listings',
        'api_access',
        'analytics',
        'priority_support',
    ];

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public int $seats = 1;

    public int $days = 30;

    /** @var list<array{key: string, value: string}> */
    public array $featureRows = [];

    /** @var list<array{currency_id: int|null, amount: string}> */
    public array $priceRows = [];

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->seats = 1;
        $this->days = 30;
        $this->featureRows = array_map(
            fn ($key) => ['key' => $key, 'value' => ''],
            self::FEATURE_KEYS
        );
        $defaultCurrency = Currency::query()->where('is_active', true)->orderByDesc('is_default')->orderBy('code')->value('id');
        $this->priceRows = [['currency_id' => $defaultCurrency, 'amount' => '0']];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $plan = SubscriptionPlan::query()->with(['prices' => fn ($q) => $q->whereNull('country_id')])->findOrFail($id);
        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->slug = (string) ($plan->slug ?? '');
        $this->seats = (int) $plan->seats;
        $this->days = (int) $plan->days;
        $this->featureRows = array_map(function ($key) use ($plan) {
            $value = $plan->features[$key] ?? '';
            return ['key' => $key, 'value' => (string) $value];
        }, self::FEATURE_KEYS);
        $this->priceRows = [];
        foreach ($plan->prices as $p) {
            $this->priceRows[] = ['currency_id' => $p->currency_id, 'amount' => (string) $p->amount];
        }
        if ($this->priceRows === []) {
            $defaultCurrency = Currency::query()->where('is_active', true)->orderByDesc('is_default')->orderBy('code')->value('id');
            $this->priceRows = [['currency_id' => $defaultCurrency, 'amount' => '0']];
        }
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function addPriceRow(): void
    {
        $defaultCurrency = Currency::query()->where('is_active', true)->orderByDesc('is_default')->orderBy('code')->value('id');
        $this->priceRows[] = ['currency_id' => $defaultCurrency, 'amount' => '0'];
    }

    public function removePriceRow(int $index): void
    {
        unset($this->priceRows[$index]);
        $this->priceRows = array_values($this->priceRows);
        if ($this->priceRows === []) {
            $this->addPriceRow();
        }
    }

    public function savePlan(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:1'],
            'days' => ['required', 'integer', 'min:1'],
            'featureRows' => ['nullable', 'array'],
            'featureRows.*.value' => ['nullable', 'string', 'max:1000'],
            'priceRows' => ['required', 'array', 'min:1'],
            'priceRows.*.currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'priceRows.*.amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Convert feature rows to associative array
        $features = [];
        foreach ($this->featureRows as $row) {
            if (isset($row['key'], $row['value'])) {
                $features[$row['key']] = $row['value'];
            }
        }

        $slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        if ($this->editingId) {
            $plan = SubscriptionPlan::query()->findOrFail($this->editingId);
            $plan->update([
                'name' => $this->name,
                'slug' => $slug,
                'seats' => $this->seats,
                'days' => $this->days,
                'features' => $features,
            ]);
        } else {
            $plan = SubscriptionPlan::query()->create([
                'name' => $this->name,
                'slug' => $slug,
                'seats' => $this->seats,
                'days' => $this->days,
                'features' => $features,
            ]);
        }

        $plan->prices()->whereNull('country_id')->delete();

        foreach ($this->priceRows as $row) {
            if (! isset($row['currency_id'], $row['amount'])) {
                continue;
            }
            Price::query()->create([
                'priceable_type' => SubscriptionPlan::class,
                'priceable_id' => $plan->id,
                'country_id' => null,
                'currency_id' => (int) $row['currency_id'],
                'amount' => $row['amount'],
            ]);
        }

        session()->flash('status', __('Subscription plan saved.'));
        $this->closeModal();
    }

    public function deletePlan(int $id): void
    {
        $plan = SubscriptionPlan::query()->findOrFail($id);
        if ($plan->subscriptions()->exists()) {
            session()->flash('error', __('Cannot delete a plan that has subscriptions.'));

            return;
        }
        $plan->prices()->delete();
        $plan->delete();
        session()->flash('status', __('Plan deleted.'));
    }

    public function render()
    {
        $plans = SubscriptionPlan::query()
            ->with(['prices' => fn ($q) => $q->whereNull('country_id')->with('currency')])
            ->orderBy('name','asc')
            ->get();

        $currencies = Currency::query()->where('is_active', true)->orderBy('code')->get();

        return view('livewire.admin.settings.admin-subscription-plans', [
            'plans' => $plans,
            'currencies' => $currencies,
        ]);
    }
}
