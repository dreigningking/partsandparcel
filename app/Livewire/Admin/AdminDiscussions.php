<?php

namespace App\Livewire\Admin;

use App\Models\Discussion;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Discussions Moderation — Admin Control Center')]
class AdminDiscussions extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $selectedDiscussionId = null;

    public function showDiscussion(int $id): void
    {
        $this->selectedDiscussionId = $id;
    }

    public function closeDiscussion(): void
    {
        $this->selectedDiscussionId = null;
    }

    public function togglePin(int $id): void
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->is_pinned = !$discussion->is_pinned;
        $discussion->save();
        session()->flash('status', __('Discussion pin status updated.'));
    }

    public function toggleLock(int $id): void
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->is_locked = !$discussion->is_locked;
        $discussion->save();
        session()->flash('status', __('Discussion lock status updated.'));
    }

    public function deleteDiscussion(int $id): void
    {
        Discussion::findOrFail($id)->delete();
        $this->selectedDiscussionId = null;
        session()->flash('status', __('Discussion removed.'));
    }

    public function render()
    {
        $discussions = Discussion::query()
            ->with(['user', 'responses.user'])
            ->when($this->search !== '', function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            })
            ->latest()
            ->paginate(12);

        $selectedDiscussion = $this->selectedDiscussionId
            ? Discussion::with(['user', 'responses.user'])->find($this->selectedDiscussionId)
            : null;

        return view('livewire.admin.admin-discussions', [
            'discussions' => $discussions,
            'selectedDiscussion' => $selectedDiscussion,
            'totalCount' => Discussion::count(),
        ]);
    }
}
