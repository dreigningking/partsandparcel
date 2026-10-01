<?php

namespace App\Livewire\Admin;

use App\Models\FeaturedListing;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminPromotions extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    public ?int $analyticsPromotionId = null;

    public function openAnalytics(int $promotionId): void
    {
        $this->analyticsPromotionId = $promotionId;
    }

    public function closeAnalytics(): void
    {
        $this->analyticsPromotionId = null;
    }

    public function render()
    {
        $promotions = Promotion::query()
            ->with(['user', 'listing', 'plan'])
            ->when($this->search !== '', function (Builder $query) {
                $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->type !== '', fn (Builder $query) => $query->where('promotable_type', $this->type))
            ->when($this->dateFrom !== '', fn (Builder $query) => $query->whereDate('start_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn (Builder $query) => $query->whereDate('start_at', '<=', $this->dateTo))
            ->latest('start_at')
            ->paginate(10);

        $analytics = null;
        if ($this->analyticsPromotionId) {
            $promotion = Promotion::query()->with('listing')->find($this->analyticsPromotionId);
            if ($promotion) {
                $featured = FeaturedListing::query()
                    ->where('listing_id', $promotion->listing_id)
                    ->latest('id')
                    ->first();
                $analytics = [
                    'promotion' => $promotion,
                    'views' => (int) ($featured->views_count ?? 0),
                    'clicks' => (int) ($featured->click_count ?? 0),
                    'action_counts' => $featured->action_counts ?? [],
                    'target_type' => $featured->target_type,
                    'target_count' => $featured->target_count,
                ];
            }
        }

        return view('livewire.admin.admin-promotions', [
            'promotions' => $promotions,
            'statuses' => Promotion::query()->distinct()->pluck('status')->filter()->values(),
            'types' => Promotion::query()->distinct()->pluck('promotable_type')->filter()->values(),
            'analytics' => $analytics,
        ]);
    }
}
