<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Country;
use App\Models\PromotionPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Promotion Plans & Rates — Admin Settings')]
class AdminPromotionPlans extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public ?int $country_id = null;

    public string $views = '0.0050';

    public string $clicks = '20.00';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';

        $defaultCountry = Country::query()
            ->where('is_default', true)
            ->first() ?? Country::query()->where('code', 'NG')->first() ?? Country::query()->where('is_active', true)->first();

        $this->country_id = $defaultCountry?->id;
        $this->views = '0.0050';
        $this->clicks = '20.00';

        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $plan = PromotionPlan::query()->findOrFail($id);

        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->slug = (string) ($plan->slug ?? '');
        $this->country_id = $plan->country_id;
        $this->views = (string) $plan->views;
        $this->clicks = (string) $plan->clicks;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function savePlan(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('promotion_plans', 'slug')->ignore($this->editingId)],
            'country_id' => ['nullable', 'exists:countries,id'],
            'views' => ['required', 'numeric', 'min:0'],
            'clicks' => ['required', 'numeric', 'min:0'],
        ]);

        $baseSlug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        PromotionPlan::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $baseSlug,
                'country_id' => $this->country_id,
                'views' => $this->views,
                'clicks' => $this->clicks,
            ]
        );

        session()->flash('status', $this->editingId ? __('Promotion plan updated successfully.') : __('Promotion plan created successfully.'));
        $this->closeModal();
    }

    public function deletePlan(int $id): void
    {
        $plan = PromotionPlan::query()->findOrFail($id);
        $plan->delete();

        session()->flash('status', __('Promotion plan deleted successfully.'));
    }

    public function render()
    {
        $plansQuery = PromotionPlan::query()
            ->with('country')
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $sub) {
                    $sub->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%')
                        ->orWhereHas('country', function (Builder $cq) {
                            $cq->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('code', 'like', '%'.$this->search.'%')
                                ->orWhere('currency', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('name', 'asc');

        $plans = $plansQuery->get();

        $countries = Country::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalPlans = PromotionPlan::query()->count();
        $coveredCountriesCount = PromotionPlan::query()->whereNotNull('country_id')->distinct('country_id')->count('country_id');

        return view('livewire.admin.settings.admin-promotion-plans', [
            'plans' => $plans,
            'countries' => $countries,
            'totalPlans' => $totalPlans,
            'coveredCountriesCount' => $coveredCountriesCount,
            'showModal' => $this->showModal,
            'editingId' => $this->editingId,
            'name' => $this->name,
            'slug' => $this->slug,
            'country_id' => $this->country_id,
            'views' => $this->views,
            'clicks' => $this->clicks,
            'search' => $this->search,
        ]);
    }
}
