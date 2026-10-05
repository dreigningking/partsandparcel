<?php

namespace App\Livewire\Admin;

use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Repair & Service Jobs — Admin Control Center')]
class AdminServices extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $selectedJobId = null;

    public function showJob(int $id): void
    {
        $this->selectedJobId = $id;
    }

    public function closeJob(): void
    {
        $this->selectedJobId = null;
    }

    public function updateJobStatus(int $id, string $status): void
    {
        $job = ServiceJob::findOrFail($id);
        $job->status = $status;
        if ($status === 'completed' && !$job->completed_at) {
            $job->completed_at = now();
        } elseif ($status === 'cancelled' && !$job->cancelled_at) {
            $job->cancelled_at = now();
        }
        $job->save();

        session()->flash('status', __("Service Job #{$job->id} status updated to {$status}."));
    }

    public function render()
    {
        $services = ServiceJob::query()
            ->with(['customer', 'provider', 'category', 'brand', 'deviceModel', 'invoice'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('provider', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(12);

        $selectedJob = $this->selectedJobId
            ? ServiceJob::with(['customer', 'provider', 'category', 'brand', 'deviceModel', 'location', 'review', 'invoice'])->find($this->selectedJobId)
            : null;

        return view('livewire.admin.admin-services', [
            'services' => $services,
            'selectedJob' => $selectedJob,
            'status' => $this->status,
            'search' => $this->search,
            'totalCount' => ServiceJob::count(),
            'activeCount' => ServiceJob::whereIn('status', ['pending', 'in_progress', 'started'])->count(),
            'completedCount' => ServiceJob::where('status', 'completed')->count(),
        ]);
    }
}
