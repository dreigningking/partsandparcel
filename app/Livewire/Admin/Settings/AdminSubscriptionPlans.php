<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Subscription Plans & Pricing — Admin Settings')]
class AdminSubscriptionPlans extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public int $request_limit = 1;

    public int $response_limit = 1;

    public int $listing_limit = 10;

    public string $escrow_percentage = '10.00';

    public bool $is_active = true;

    public bool $is_default = false;

    // Feature toggles
    public bool $priority_placement = false;

    public bool $verified_badge = false;

    public bool $dedicated_support = false;

    public bool $dedicated_arbitration = false;

    public string $description = '';

    /** @var list<array{country_id: int|null, price_monthly: string, price_annual: string, is_active: bool}> */
    public array $priceRows = [];

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->request_limit = 1;
        $this->response_limit = 1;
        $this->listing_limit = 10;
        $this->escrow_percentage = '10.00';
        $this->is_active = true;
        $this->is_default = false;
        $this->priority_placement = false;
        $this->verified_badge = false;
        $this->dedicated_support = false;
        $this->dedicated_arbitration = false;
        $this->description = '';

        $defaultCountry = Country::query()
            ->where('is_default', true)
            ->first() ?? Country::query()->where('code', 'NG')->first() ?? Country::query()->where('is_active', true)->first();

        $this->priceRows = [
            [
                'country_id' => $defaultCountry?->id,
                'price_monthly' => '0.00',
                'price_annual' => '0.00',
                'is_active' => true,
            ],
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $plan = SubscriptionPlan::query()->with(['prices.country'])->findOrFail($id);

        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->slug = (string) ($plan->slug ?? '');
        $this->request_limit = (int) $plan->request_limit;
        $this->response_limit = (int) $plan->response_limit;
        $this->listing_limit = (int) $plan->listing_limit;
        $this->escrow_percentage = (string) $plan->escrow_percentage;
        $this->is_active = (bool) $plan->is_active;
        $this->is_default = (bool) $plan->is_default;

        $features = $plan->features ?? [];
        $this->priority_placement = (bool) ($features['priority_placement'] ?? false);
        $this->verified_badge = (bool) ($features['verified_badge'] ?? false);
        $this->dedicated_support = (bool) ($features['dedicated_support'] ?? false);
        $this->dedicated_arbitration = (bool) ($features['dedicated_arbitration'] ?? false);
        $this->description = (string) ($features['description'] ?? '');

        $this->priceRows = [];
        foreach ($plan->prices as $p) {
            $this->priceRows[] = [
                'country_id' => $p->country_id,
                'price_monthly' => (string) $p->price_monthly,
                'price_annual' => (string) $p->price_annual,
                'is_active' => (bool) $p->is_active,
            ];
        }

        if ($this->priceRows === []) {
            $defaultCountry = Country::query()->where('is_default', true)->first() ?? Country::query()->first();
            $this->priceRows = [
                [
                    'country_id' => $defaultCountry?->id,
                    'price_monthly' => '0.00',
                    'price_annual' => '0.00',
                    'is_active' => true,
                ],
            ];
        }

        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function addPriceRow(): void
    {
        $usedCountryIds = array_filter(array_column($this->priceRows, 'country_id'));
        $nextCountry = Country::query()
            ->where('is_active', true)
            ->whereNotIn('id', $usedCountryIds)
            ->first() ?? Country::query()->where('is_active', true)->first();

        $this->priceRows[] = [
            'country_id' => $nextCountry?->id,
            'price_monthly' => '0.00',
            'price_annual' => '0.00',
            'is_active' => true,
        ];
    }

    public function removePriceRow(int $index): void
    {
        unset($this->priceRows[$index]);
        $this->priceRows = array_values($this->priceRows);

        if ($this->priceRows === []) {
            $this->addPriceRow();
        }
    }

    public function toggleActive(int $id): void
    {
        $plan = SubscriptionPlan::query()->findOrFail($id);
        $plan->update(['is_active' => ! $plan->is_active]);

        session()->flash('status', __('Plan status updated.'));
    }

    public function savePlan(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('subscription_plans', 'slug')->ignore($this->editingId)],
            'request_limit' => ['required', 'integer', 'min:0'],
            'response_limit' => ['required', 'integer', 'min:0'],
            'listing_limit' => ['required', 'integer', 'min:0'],
            'escrow_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'priceRows' => ['required', 'array', 'min:1'],
            'priceRows.*.country_id' => ['required', 'exists:countries,id'],
            'priceRows.*.price_monthly' => ['required', 'numeric', 'min:0'],
            'priceRows.*.price_annual' => ['required', 'numeric', 'min:0'],
        ]);

        $baseSlug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        if ($this->is_default) {
            SubscriptionPlan::query()->where('id', '!=', $this->editingId ?? 0)->update(['is_default' => false]);
        }

        $features = [
            'daily_request_limit' => $this->request_limit,
            'daily_response_limit' => $this->response_limit,
            'listing_limit' => $this->listing_limit,
            'escrow_fee' => rtrim(rtrim((string) $this->escrow_percentage, '0'), '.').'%',
            'priority_placement' => $this->priority_placement,
            'verified_badge' => $this->verified_badge,
            'dedicated_support' => $this->dedicated_support,
            'dedicated_arbitration' => $this->dedicated_arbitration,
            'description' => $this->description ?: "{$this->request_limit} community requests/day, {$this->response_limit} quote responses/day, up to {$this->listing_limit} listings, {$this->escrow_percentage}% escrow fee",
        ];

        $plan = SubscriptionPlan::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $baseSlug,
                'request_limit' => $this->request_limit,
                'response_limit' => $this->response_limit,
                'listing_limit' => $this->listing_limit,
                'escrow_percentage' => $this->escrow_percentage,
                'features' => $features,
                'is_active' => $this->is_active,
                'is_default' => $this->is_default,
            ]
        );

        $savedCountryIds = [];
        foreach ($this->priceRows as $row) {
            if (empty($row['country_id'])) {
                continue;
            }

            SubscriptionPlanPrice::query()->updateOrCreate(
                [
                    'subscription_plan_id' => $plan->id,
                    'country_id' => $row['country_id'],
                ],
                [
                    'price_monthly' => $row['price_monthly'],
                    'price_annual' => $row['price_annual'],
                    'is_active' => $row['is_active'] ?? true,
                ]
            );

            $savedCountryIds[] = (int) $row['country_id'];
        }

        // Clean up prices for removed countries
        SubscriptionPlanPrice::query()
            ->where('subscription_plan_id', $plan->id)
            ->whereNotIn('country_id', $savedCountryIds)
            ->delete();

        session()->flash('status', $this->editingId ? __('Subscription plan updated successfully.') : __('Subscription plan created successfully.'));
        $this->closeModal();
    }

    public function deletePlan(int $id): void
    {
        $plan = SubscriptionPlan::query()->findOrFail($id);

        if ($plan->subscriptions()->exists()) {
            session()->flash('error', __('Cannot delete a subscription plan that has active or past subscribers. Consider deactivating it instead.'));

            return;
        }

        $plan->prices()->delete();
        $plan->delete();

        session()->flash('status', __('Subscription plan deleted successfully.'));
    }

    public function render()
    {
        $plansQuery = SubscriptionPlan::query()
            ->with(['prices.country'])
            ->withCount('subscriptions')
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $sub) {
                    $sub->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%')
                        ->orWhereHas('prices.country', function (Builder $cq) {
                            $cq->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('currency', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderByDesc('is_default')
            ->orderBy('id', 'asc');

        $plans = $plansQuery->get();

        $countries = Country::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalPlans = SubscriptionPlan::query()->count();
        $activeSubscribersCount = \App\Models\Subscription::query()->where('status', 'active')->count();
        $defaultPlan = SubscriptionPlan::query()->where('is_default', true)->first();

        return view('livewire.admin.settings.admin-subscription-plans', [
            'plans' => $plans,
            'countries' => $countries,
            'totalPlans' => $totalPlans,
            'activeSubscribersCount' => $activeSubscribersCount,
            'defaultPlan' => $defaultPlan,
            'showModal' => $this->showModal,
            'editingId' => $this->editingId,
            'name' => $this->name,
            'slug' => $this->slug,
            'request_limit' => $this->request_limit,
            'response_limit' => $this->response_limit,
            'listing_limit' => $this->listing_limit,
            'escrow_percentage' => $this->escrow_percentage,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'priority_placement' => $this->priority_placement,
            'verified_badge' => $this->verified_badge,
            'dedicated_support' => $this->dedicated_support,
            'dedicated_arbitration' => $this->dedicated_arbitration,
            'description' => $this->description,
            'priceRows' => $this->priceRows,
            'search' => $this->search,
        ]);
    }
}
