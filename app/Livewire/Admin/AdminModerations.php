<?php

namespace App\Livewire\Admin;

use App\Jobs\ModerationNotifierJob;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Moderation;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Verification;
use App\Models\Watchlist;
use App\Notifications\PostCommentApprovedNotification;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
#[Title('Content Moderation — Admin Control Center')]
class AdminModerations extends Component
{
    use WithPagination;

    public string $filter = 'pending'; // 'pending', 'approved', 'rejected', 'processed', 'all'

    public string $type = 'all'; // 'all', 'listing', 'discussion', 'post_comment'

    public string $search = '';

    public int $perPage = 15;

    public string $sortOrder = 'desc'; // 'desc', 'asc'

    // Rejection Modal
    public bool $showRejectModal = false;

    public ?int $selectedModerationId = null;

    public string $rejectionReason = '';

    public string $presetReason = '';

    // Preview Modal
    public bool $showPreviewModal = false;

    public ?int $previewModerationId = null;

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $moderation = Moderation::with('moderatable')->findOrFail($id);

        $moderation->update([
            'status' => 'approved',
            'moderated_by' => auth()->id(),
            'reason' => null,
        ]);

        $item = $moderation->moderatable;

        if ($item instanceof Listing) {
            $item->updateQuietly(['is_published' => true]);
        } elseif ($item instanceof Discussion) {
            $item->updateQuietly(['status' => 'open']);
        } elseif ($item instanceof PostComment) {
            $post = $item->post;
            if ($post) {
                $watchers = $post->watchlists()->with('user')->get();

                foreach ($watchers as $watcher) {
                    if ($watcher->user && strtolower($watcher->user->email) !== strtolower($item->email)) {
                        $watcher->user->notify(new PostCommentApprovedNotification($post, $item));
                    }
                }
            }
        } elseif ($item instanceof Location) {
            // Location status and review metadata are stored in moderations table
        } elseif ($item instanceof Verification) {
            // Verification status and review metadata are stored in moderations table
            if ($item->user && $item->user->is_fully_verified) {
                $item->user->update(['is_verified' => true]);
            }
        }

        session()->flash('status', __('The item (:type) was approved successfully.', [
            'type' => $moderation->type_label,
        ]));
    }

    public function openRejectModal(int $id): void
    {
        $this->selectedModerationId = $id;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->resetErrorBag();
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedModerationId = null;
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->resetErrorBag();
    }

    public function setPresetReason(string $reason): void
    {
        $this->presetReason = $reason;
        $this->rejectionReason = $reason;
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'rejectionReason.required' => 'Please provide a reason or select a preset reason for rejection.',
        ]);

        $moderation = Moderation::with('moderatable')->findOrFail($this->selectedModerationId);

        $moderation->update([
            'status' => 'rejected',
            'moderated_by' => auth()->id(),
            'reason' => $this->rejectionReason,
        ]);

        $item = $moderation->moderatable;

        if ($item instanceof Listing) {
            $item->updateQuietly(['is_published' => false]);
        } elseif ($item instanceof Discussion) {
            $item->updateQuietly(['status' => 'closed']);
        } elseif ($item instanceof Location) {
            // Rejection reason and status are recorded on Moderation model
        } elseif ($item instanceof Verification) {
            // Rejection reason and status are recorded on Moderation model
        }

        $this->closeRejectModal();

        session()->flash('status', __('The item (:type) was rejected.', [
            'type' => $moderation->type_label,
        ]));
    }

    public function preview(int $id): void
    {
        $this->previewModerationId = $id;
        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewModerationId = null;
    }

    public function sendNotifierAlert(): void
    {
        ModerationNotifierJob::dispatch();
        session()->flash('status', __('Moderation notifier job dispatched to staff.'));
    }

    public function render()
    {
        // 1. KPI Counts
        $pendingListingsCount = Moderation::where('status', 'pending')
            ->where('moderatable_type', Listing::class)
            ->count();

        $pendingDiscussionsCount = Moderation::where('status', 'pending')
            ->where('moderatable_type', Discussion::class)
            ->count();

        $pendingCommentsCount = Moderation::where('status', 'pending')
            ->where('moderatable_type', PostComment::class)
            ->count();

        $pendingLocationsCount = Moderation::where('status', 'pending')
            ->where('moderatable_type', Location::class)
            ->count();

        $pendingVerificationsCount = Moderation::where('status', 'pending')
            ->where('moderatable_type', Verification::class)
            ->count();

        $pendingCount = Moderation::where('status', 'pending')->count();
        $approvedCount = Moderation::where('status', 'approved')->count();
        $rejectedCount = Moderation::where('status', 'rejected')->count();
        $totalCount = Moderation::count();

        // 2. Query Moderations
        $query = Moderation::query()
            ->with(['moderator'])
            ->with(['moderatable' => function ($morphTo) {
                $morphTo->morphWith([
                    Listing::class => ['user', 'item.deviceModel.brand', 'item.deviceModel.category', 'media'],
                    Discussion::class => ['user', 'category', 'brand', 'deviceModel', 'location'],
                    PostComment::class => ['post'],
                    Location::class => ['user', 'state', 'country'],
                    Verification::class => ['user'],
                ]);
            }]);

        // Status Filter
        if ($this->filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->filter === 'approved') {
            $query->where('status', 'approved');
        } elseif ($this->filter === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($this->filter === 'processed') {
            $query->whereIn('status', ['approved', 'rejected']);
        }

        // Type Filter
        if ($this->type === 'listing') {
            $query->where('moderatable_type', Listing::class);
        } elseif ($this->type === 'discussion') {
            $query->where('moderatable_type', Discussion::class);
        } elseif ($this->type === 'post_comment') {
            $query->where('moderatable_type', PostComment::class);
        } elseif ($this->type === 'location') {
            $query->where('moderatable_type', Location::class);
        } elseif ($this->type === 'verification') {
            $query->where('moderatable_type', Verification::class);
        }

        // Search Filter
        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('reason', 'like', $searchTerm)
                    ->orWhere('action', 'like', $searchTerm)
                    ->orWhereHasMorph('moderatable', [Listing::class], function (Builder $lq) use ($searchTerm) {
                        $lq->where('slug', 'like', $searchTerm)
                            ->orWhereHas('item', fn ($iq) => $iq->where('name', 'like', $searchTerm))
                            ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm));
                    })
                    ->orWhereHasMorph('moderatable', [Discussion::class], function (Builder $dq) use ($searchTerm) {
                        $dq->where('title', 'like', $searchTerm)
                            ->orWhere('body', 'like', $searchTerm)
                            ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm));
                    })
                    ->orWhereHasMorph('moderatable', [Location::class], function (Builder $locQ) use ($searchTerm) {
                        $locQ->where('label', 'like', $searchTerm)
                            ->orWhere('city', 'like', $searchTerm)
                            ->orWhere('address_line_1', 'like', $searchTerm)
                            ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm));
                    })
                    ->orWhereHasMorph('moderatable', [Verification::class], function (Builder $vq) use ($searchTerm) {
                        $vq->where('document_type', 'like', $searchTerm)
                            ->orWhere('document_number', 'like', $searchTerm)
                            ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm));
                    })
                    ->orWhereHasMorph('moderatable', [PostComment::class], function (Builder $cq) use ($searchTerm) {
                        $cq->where('comment', 'like', $searchTerm)
                            ->orWhere('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm);
                    });
            });
        }

        // Sorting
        $query->orderBy('created_at', $this->sortOrder);

        $moderations = $query->paginate($this->perPage);

        // Preview item if open
        $previewItem = null;
        if ($this->showPreviewModal && $this->previewModerationId) {
            $previewItem = Moderation::with(['moderator'])
                ->with(['moderatable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Listing::class => ['user', 'item.deviceModel.brand', 'item.deviceModel.category', 'media'],
                        Discussion::class => ['user', 'category', 'brand', 'deviceModel', 'location'],
                        PostComment::class => ['post'],
                        Location::class => ['user', 'state', 'country'],
                        Verification::class => ['user'],
                    ]);
                }])
                ->find($this->previewModerationId);
        }

        return view('livewire.admin.admin-moderations', [
            'moderations' => $moderations,
            'previewItem' => $previewItem,
            'filter' => $this->filter,
            'type' => $this->type,
            'search' => $this->search,
            'sortOrder' => $this->sortOrder,
            'showRejectModal' => $this->showRejectModal,
            'rejectionReason' => $this->rejectionReason,
            'presetReason' => $this->presetReason,
            'showPreviewModal' => $this->showPreviewModal,
            'pendingCount' => $pendingCount,
            'pendingListingsCount' => $pendingListingsCount,
            'pendingDiscussionsCount' => $pendingDiscussionsCount,
            'pendingCommentsCount' => $pendingCommentsCount,
            'pendingLocationsCount' => $pendingLocationsCount,
            'pendingVerificationsCount' => $pendingVerificationsCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
