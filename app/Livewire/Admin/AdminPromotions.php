<?php

namespace App\Livewire\Admin;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Promotions & Sponsored Campaigns — Admin Control Center')]
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

    public ?int $selectedPromotionId = null;

    public bool $showDetailsModal = false;

    public bool $showEditModal = false;

    public string $editStatus = 'active';

    public string $editType = 'clicks';

    public int $editAchievedCount = 0;

    public int $editTargetCount = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->type = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function openDetails(int $id): void
    {
        $this->selectedPromotionId = $id;
        $this->showDetailsModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedPromotionId = null;
    }

    public function openEdit(int $id): void
    {
        $promotion = Promotion::query()->findOrFail($id);

        $this->selectedPromotionId = $promotion->id;
        $this->editStatus = $promotion->status;
        $this->editType = $promotion->type;
        $this->editAchievedCount = (int) $promotion->achieved_count;
        $this->editTargetCount = (int) ($promotion->target_count ?? 0);

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->selectedPromotionId = null;
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editStatus' => ['required', Rule::in(['pending', 'active', 'inactive', 'completed'])],
            'editType' => ['required', Rule::in(['clicks', 'views'])],
            'editAchievedCount' => ['required', 'integer', 'min:0'],
            'editTargetCount' => ['required', 'integer', 'min:0'],
        ]);

        $promotion = Promotion::query()->findOrFail($this->selectedPromotionId);
        $promotion->update([
            'status' => $this->editStatus,
            'type' => $this->editType,
            'achieved_count' => $this->editAchievedCount,
            'target_count' => $this->editTargetCount,
        ]);

        session()->flash('status', __('Promotion campaign updated successfully.'));
        $this->closeEdit();
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        if (! in_array($newStatus, ['pending', 'active', 'inactive', 'completed'], true)) {
            return;
        }

        $promotion = Promotion::query()->findOrFail($id);
        $promotion->update(['status' => $newStatus]);

        session()->flash('status', __('Promotion status changed to :status.', ['status' => ucfirst($newStatus)]));
    }

    public function deletePromotion(int $id): void
    {
        $promotion = Promotion::query()->findOrFail($id);
        $promotion->delete();

        if ($this->selectedPromotionId === $id) {
            $this->closeDetails();
            $this->closeEdit();
        }

        session()->flash('status', __('Promotion deleted successfully.'));
    }

    public function render()
    {
        $promotions = Promotion::query()
            ->with(['user', 'listing.item', 'listing.media', 'payments'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $sub) {
                    $sub->whereHas('user', function (Builder $uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })->orWhereHas('listing', function (Builder $lq) {
                        $lq->where('slug', 'like', '%'.$this->search.'%')
                            ->orWhereHas('item', fn (Builder $iq) => $iq->where('name', 'like', '%'.$this->search.'%'));
                    });
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->type !== '', fn (Builder $query) => $query->where('type', $this->type))
            ->when($this->dateFrom !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->paginate(12);

        $totalPromotions = Promotion::query()->count();
        $activePromotions = Promotion::query()->where('status', 'active')->count();
        $totalClicksAchieved = (int) Promotion::query()->where('type', 'clicks')->sum('achieved_count');
        $totalViewsAchieved = (int) Promotion::query()->where('type', 'views')->sum('achieved_count');

        $selectedPromotion = $this->selectedPromotionId
            ? Promotion::query()->with(['user', 'listing.item', 'listing.media', 'payments'])->find($this->selectedPromotionId)
            : null;

        return view('livewire.admin.admin-promotions', [
            'promotions' => $promotions,
            'totalPromotions' => $totalPromotions,
            'activePromotions' => $activePromotions,
            'totalClicksAchieved' => $totalClicksAchieved,
            'totalViewsAchieved' => $totalViewsAchieved,
            'selectedPromotion' => $selectedPromotion,
            'search' => $this->search,
            'status' => $this->status,
            'type' => $this->type,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'showDetailsModal' => $this->showDetailsModal,
            'showEditModal' => $this->showEditModal,
            'editStatus' => $this->editStatus,
            'editType' => $this->editType,
            'editAchievedCount' => $this->editAchievedCount,
            'editTargetCount' => $this->editTargetCount,
        ]);
    }
}
