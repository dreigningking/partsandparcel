<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Discussion;
use App\Models\Moderation;
use App\Models\Offer;
use App\Models\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Discussions & Requests Moderation — Admin Control Center')]
class AdminDiscussions extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = '';

    #[Url(as: 'category')]
    public string $categoryFilter = '';

    #[Url(as: 'moderation')]
    public string $moderationFilter = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'sort')]
    public string $sort = 'latest';

    public int $perPage = 15;

    // Discussion View Modal
    public ?int $selectedDiscussionId = null;
    public string $activeModalTab = 'details'; // 'details', 'offers', 'replies', 'audit'

    // Moderation Rejection Modal
    public bool $showRejectModal = false;
    public ?int $selectedDiscussionIdForReject = null;
    public string $rejectionReason = '';
    public string $presetReason = '';

    public array $standardReasons = [
        'Prohibited Item / Service' => 'Requests for weapons, counterfeit parts, stolen goods, or unlicensed services are strictly prohibited.',
        'Insufficient Technical Information' => 'Please include specific part numbers, device brand, model year, or clear reference photos so vendors can assist you.',
        'Spam / Commercial Promotion' => 'Self-promotional advertising or spam messages are not allowed as community requests.',
        'Inappropriate Contact Information' => 'Sharing personal off-platform payment handles or unverified phone numbers in public requests is not allowed for security reasons.',
        'Duplicate Request' => 'An identical or very similar request is already active in the community hub.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModerationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'typeFilter',
            'categoryFilter',
            'moderationFilter',
            'statusFilter',
            'sort',
        ]);
        $this->resetPage();
    }

    public function showDiscussion(int $id, string $tab = 'details'): void
    {
        $this->selectedDiscussionId = $id;
        $this->activeModalTab = $tab;
    }

    public function closeDiscussion(): void
    {
        $this->selectedDiscussionId = null;
        $this->activeModalTab = 'details';
    }

    public function setModalTab(string $tab): void
    {
        $this->activeModalTab = $tab;
    }

    public function approve(int $id): void
    {
        $discussion = Discussion::findOrFail($id);

        $hasPending = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $discussion->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where('moderatable_type', Discussion::class)
                ->where('moderatable_id', $discussion->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'action' => 'approved_by_admin',
                    'moderated_by' => Auth::id(),
                ]);
        } else {
            Moderation::create([
                'moderatable_type' => Discussion::class,
                'moderatable_id' => $discussion->id,
                'moderated_by' => Auth::id(),
                'status' => 'approved',
                'action' => 'approved_by_admin',
            ]);
        }

        if ($discussion->status === 'closed') {
            $discussion->update(['status' => 'open']);
        }

        session()->flash('status', "Request #REQ-{$discussion->id} has been approved.");
    }

    public function openRejectModal(int $id): void
    {
        $this->selectedDiscussionIdForReject = $id;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedDiscussionIdForReject = null;
        $this->rejectionReason = '';
        $this->presetReason = '';
    }

    public function updatedPresetReason(string $val): void
    {
        if (! empty($val)) {
            $this->rejectionReason = $val;
        }
    }

    public function submitReject(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:1000',
        ]);

        $discussion = Discussion::findOrFail($this->selectedDiscussionIdForReject);

        $hasPending = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $discussion->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where('moderatable_type', Discussion::class)
                ->where('moderatable_id', $discussion->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'action' => 'rejected_by_admin',
                    'reason' => $this->rejectionReason,
                    'moderated_by' => Auth::id(),
                ]);
        } else {
            Moderation::create([
                'moderatable_type' => Discussion::class,
                'moderatable_id' => $discussion->id,
                'moderated_by' => Auth::id(),
                'status' => 'rejected',
                'action' => 'rejected_by_admin',
                'reason' => $this->rejectionReason,
            ]);
        }

        $discussion->update(['status' => 'closed']);

        $this->showRejectModal = false;
        $targetId = $this->selectedDiscussionIdForReject;
        $this->selectedDiscussionIdForReject = null;
        $this->rejectionReason = '';

        session()->flash('status', "Request #REQ-{$targetId} has been rejected with reason recorded.");
    }

    public function togglePin(int $id): void
    {
        $discussion = Discussion::findOrFail($id);
        $attachments = $discussion->attachments ?? [];
        $newState = ! ($attachments['is_pinned'] ?? false);
        $attachments['is_pinned'] = $newState;
        $discussion->attachments = $attachments;
        $discussion->save();

        $text = $newState ? 'pinned to the top of community' : 'unpinned';
        session()->flash('status', "Request #REQ-{$id} is now {$text}.");
    }

    public function toggleLock(int $id): void
    {
        $discussion = Discussion::findOrFail($id);
        $attachments = $discussion->attachments ?? [];
        $newState = ! ($attachments['is_locked'] ?? false);
        $attachments['is_locked'] = $newState;
        $discussion->attachments = $attachments;
        $discussion->save();

        $text = $newState ? 'locked (replies disabled)' : 'unlocked for community discussion';
        session()->flash('status', "Request #REQ-{$id} is now {$text}.");
    }

    public function changeStatus(int $id, string $status): void
    {
        if (! in_array($status, ['open', 'fulfilled', 'closed'], true)) {
            return;
        }

        $discussion = Discussion::findOrFail($id);
        $discussion->update(['status' => $status]);

        session()->flash('status', "Request #REQ-{$id} status updated to " . strtoupper($status) . ".");
    }

    public function deleteDiscussion(int $id): void
    {
        $discussion = Discussion::findOrFail($id);

        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $discussion->id,
            'moderated_by' => Auth::id(),
            'status' => 'rejected',
            'action' => 'deleted_by_admin',
            'reason' => 'Permanently removed from community by admin',
        ]);

        $discussion->delete();

        if ($this->selectedDiscussionId === $id) {
            $this->selectedDiscussionId = null;
        }

        session()->flash('status', "Request #REQ-{$id} permanently deleted.");
    }

    public function deleteResponse(int $responseId): void
    {
        $response = Response::findOrFail($responseId);
        $discId = $response->discussion_id;
        $response->delete();

        session()->flash('status', "Reply #{$responseId} removed from Request #REQ-{$discId}.");
    }

    public function render()
    {
        // 1. Metric Calculations
        $metrics = [
            'total' => Discussion::count(),
            'approved' => Discussion::approved()->count(),
            'pending' => Discussion::pending()->count(),
            'offers' => Offer::whereNotNull('discussion_id')->count(),
            'fulfilled' => Discussion::where('status', 'fulfilled')->count(),
        ];

        // 2. Query Discussions
        $query = Discussion::query()
            ->with([
                'user.country',
                'user.primaryLocation',
                'category',
                'brand',
                'deviceModel',
                'location',
                'media',
                'latestModeration',
                'responses',
                'offers',
            ])
            ->withCount(['responses', 'offers', 'reports']);

        // Search
        if ($this->search !== '') {
            $query->where(function (Builder $q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('deviceModel', fn ($mq) => $mq->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        // Type filter
        if ($this->typeFilter !== '') {
            $query->where('type', $this->typeFilter);
        }

        // Category filter
        if ($this->categoryFilter !== '') {
            $query->where('category_id', $this->categoryFilter);
        }

        // Moderation filter
        if ($this->moderationFilter === 'pending') {
            $query->pending();
        } elseif ($this->moderationFilter === 'approved') {
            $query->approved();
        } elseif ($this->moderationFilter === 'rejected') {
            $query->rejected();
        }

        // Status filter
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        match ($this->sort) {
            'oldest' => $query->oldest(),
            'offers_desc' => $query->orderByDesc('offers_count'),
            'responses_desc' => $query->orderByDesc('responses_count'),
            'budget_desc' => $query->orderByDesc('budget'),
            default => $query->latest(),
        };

        $discussions = $query->paginate($this->perPage);

        // 3. Eager load Selected Discussion for modal
        $selectedDiscussion = null;
        if ($this->selectedDiscussionId) {
            $selectedDiscussion = Discussion::with([
                'user.country',
                'user.primaryLocation',
                'category',
                'brand',
                'deviceModel',
                'location',
                'media',
                'responses.user',
                'offers.sender',
                'offers.items',
                'offers.serviceJobs',
                'offers.invoice',
                'moderations.moderator',
                'reports.user',
            ])->find($this->selectedDiscussionId);
        }

        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.admin-discussions', [
            'discussions' => $discussions,
            'selectedDiscussion' => $selectedDiscussion,
            'metrics' => $metrics,
            'categories' => $categories,
        ]);
    }
}
