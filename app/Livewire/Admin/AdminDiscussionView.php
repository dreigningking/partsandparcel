<?php

namespace App\Livewire\Admin;

use App\Models\Discussion;
use App\Models\Moderation;
use App\Models\Report;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Discussion Moderation & Details — Admin Control Center')]
class AdminDiscussionView extends Component
{
    public Discussion $discussion;

    // Moderation Rejection Modal
    public bool $showRejectModal = false;
    public string $rejectionReason = '';
    public string $presetReason = '';

    // Workspace tab: 'overview' vs 'responses' vs 'trust'
    public string $activeTab = 'overview';

    // Active response filter: 'all' vs 'offers' vs 'reported'
    public string $responseTab = 'all';

    public array $standardReasons = [
        'Prohibited Item / Service' => 'Requests for weapons, counterfeit parts, stolen goods, or unlicensed services are strictly prohibited.',
        'Insufficient Technical Information' => 'Please include specific part numbers, device brand, model year, or clear reference photos so vendors can assist you.',
        'Spam / Commercial Promotion' => 'Self-promotional advertising or spam messages are not allowed as community requests.',
        'Inappropriate Contact Information' => 'Sharing personal off-platform payment handles or unverified phone numbers in public requests is not allowed for security reasons.',
        'Duplicate Request' => 'An identical or very similar request is already active in the community hub.',
    ];

    public function mount($discussion): void
    {
        if ($discussion instanceof Discussion) {
            $this->discussion = $discussion;
        } else {
            $this->discussion = Discussion::findOrFail($discussion);
        }

        $this->loadRelations();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'responses', 'trust'], true)) {
            $this->activeTab = $tab;
        }
    }

    protected function loadRelations(): void
    {
        $this->discussion->loadMissing([
            'user.country',
            'user.primaryLocation',
            'user.subscriptions.plan',
            'category',
            'brand',
            'deviceModel',
            'location.state',
            'location.country',
            'media',
            'moderations.moderator',
            'reports.user',
            'reports.resolvedBy',
            'watchlists',
            'views',
            'responses.user.country',
            'responses.user.primaryLocation',
            'responses.media',
            'responses.offers.items',
            'responses.offers.sender',
            'responses.reports.user',
            'responses.reports.resolvedBy',
            'offers.sender',
            'offers.items',
        ]);
    }

    public function approve(): void
    {
        $hasPending = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $this->discussion->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where('moderatable_type', Discussion::class)
                ->where('moderatable_id', $this->discussion->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'action' => 'approved_by_admin',
                    'moderated_by' => Auth::id(),
                ]);
        } else {
            Moderation::create([
                'moderatable_type' => Discussion::class,
                'moderatable_id' => $this->discussion->id,
                'moderated_by' => Auth::id(),
                'status' => 'approved',
                'action' => 'approved_by_admin',
            ]);
        }

        if ($this->discussion->status === 'closed') {
            $this->discussion->update(['status' => 'open']);
        }

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Discussion #REQ-{$this->discussion->id} has been approved and published to the live community.");
    }

    public function openRejectModal(): void
    {
        $this->rejectionReason = '';
        $this->presetReason = '';
        $this->resetErrorBag();
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
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

        $hasPending = Moderation::where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $this->discussion->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            Moderation::where('moderatable_type', Discussion::class)
                ->where('moderatable_id', $this->discussion->id)
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
                'moderatable_id' => $this->discussion->id,
                'moderated_by' => Auth::id(),
                'status' => 'rejected',
                'action' => 'rejected_by_admin',
                'reason' => $this->rejectionReason,
            ]);
        }

        $this->discussion->update(['status' => 'closed']);

        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Discussion #REQ-{$this->discussion->id} has been rejected and closed.");
    }

    public function togglePin(): void
    {
        $attachments = $this->discussion->attachments ?? [];
        $newState = ! ($attachments['is_pinned'] ?? false);
        $attachments['is_pinned'] = $newState;
        $this->discussion->attachments = $attachments;
        $this->discussion->save();

        $this->discussion->refresh();
        $text = $newState ? 'pinned to the top of community' : 'unpinned';
        session()->flash('status', "Discussion is now {$text}.");
    }

    public function toggleLock(): void
    {
        $attachments = $this->discussion->attachments ?? [];
        $newState = ! ($attachments['is_locked'] ?? false);
        $attachments['is_locked'] = $newState;
        $this->discussion->attachments = $attachments;
        $this->discussion->save();

        $this->discussion->refresh();
        $text = $newState ? 'locked (replies disabled)' : 'unlocked for replies';
        session()->flash('status', "Discussion is now {$text}.");
    }

    public function changeStatus(string $status): void
    {
        if (! in_array($status, ['open', 'fulfilled', 'closed'], true)) {
            return;
        }

        $this->discussion->update(['status' => $status]);
        $this->discussion->refresh();

        session()->flash('status', "Status updated to " . strtoupper($status) . ".");
    }

    public function resolveDiscussionReport(int $reportId, string $notes = ''): void
    {
        $report = $this->discussion->reports()->findOrFail($reportId);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Resolved by administrator.',
        ]);

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Report #{$reportId} marked as resolved.");
    }

    public function dismissDiscussionReport(int $reportId, string $notes = ''): void
    {
        $report = $this->discussion->reports()->findOrFail($reportId);

        $report->update([
            'status' => 'dismissed',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Dismissed by administrator.',
        ]);

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Report #{$reportId} has been dismissed.");
    }

    public function deleteResponse(int $responseId): void
    {
        $response = Response::where('discussion_id', $this->discussion->id)->findOrFail($responseId);
        $response->delete();

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Response #{$responseId} was deleted successfully.");
    }

    public function resolveResponseReport(int $reportId, string $notes = ''): void
    {
        $report = Report::findOrFail($reportId);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Resolved by administrator.',
        ]);

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Response report #{$reportId} marked as resolved.");
    }

    public function dismissResponseReport(int $reportId, string $notes = ''): void
    {
        $report = Report::findOrFail($reportId);

        $report->update([
            'status' => 'dismissed',
            'resolved_by' => Auth::id(),
            'resolution_notes' => $notes ?: 'Dismissed by administrator.',
        ]);

        $this->discussion->refresh();
        $this->loadRelations();

        session()->flash('status', "Response report #{$reportId} has been dismissed.");
    }

    public function delete(): mixed
    {
        $id = $this->discussion->id;

        Moderation::create([
            'moderatable_type' => Discussion::class,
            'moderatable_id' => $this->discussion->id,
            'moderated_by' => Auth::id(),
            'status' => 'rejected',
            'action' => 'deleted_by_admin',
            'reason' => 'Permanently removed from community by admin',
        ]);

        $this->discussion->delete();

        session()->flash('status', "Discussion #REQ-{$id} permanently deleted.");

        return redirect()->route('admin.discussions');
    }

    public function setResponseTab(string $tab): void
    {
        $this->responseTab = $tab;
    }

    public function render()
    {
        $this->loadRelations();

        $moderations = Moderation::query()
            ->where('moderatable_type', Discussion::class)
            ->where('moderatable_id', $this->discussion->id)
            ->with('moderator')
            ->latest('created_at')
            ->get();

        $openReports = $this->discussion->reports->whereIn('status', ['pending', 'open']);

        $reportedResponses = $this->discussion->responses->filter(function ($response) {
            return $response->reports->isNotEmpty();
        });

        $openReportedResponses = $this->discussion->responses->filter(function ($response) {
            return $response->reports->whereIn('status', ['pending', 'open'])->isNotEmpty();
        });

        // 1. Views from ViewedEntity
        $dbViews = $this->discussion->views()->count();
        $attachViews = (int) ($this->discussion->attachments['views'] ?? 0);
        $viewsCount = max($dbViews, $attachViews);
        $uniqueViewers = $this->discussion->views()->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // 2. Watchlists
        $watchersCount = $this->discussion->watchlists()->count();

        // 3. Responses & Offers
        $responsesCount = $this->discussion->responses->count();
        $responsesWithOffers = $this->discussion->responses->filter(function ($resp) {
            return $resp->offers->isNotEmpty();
        });
        $responsesWithOffersCount = $responsesWithOffers->count();
        $totalOffersCount = $this->discussion->offers->count();
        $totalOffersValue = (float) $this->discussion->offers->sum(fn ($o) => $o->total());

        // 4. Media Categorization (Image, Video, Docs)
        $allMedia = collect();
        if ($this->discussion->media) {
            $allMedia = $allMedia->merge($this->discussion->media);
        }
        if (! empty($this->discussion->attachments['photos']) && is_array($this->discussion->attachments['photos'])) {
            foreach ($this->discussion->attachments['photos'] as $photo) {
                $url = is_string($photo) ? $photo : ($photo['url'] ?? '');
                if ($url) {
                    $allMedia->push((object) [
                        'id' => null,
                        'url' => $url,
                        'file_name' => basename($url) ?: 'attached_reference.jpg',
                        'is_image' => true,
                        'is_video' => false,
                        'is_document' => false,
                        'mime_type' => 'image/jpeg',
                        'size' => null,
                    ]);
                }
            }
        }

        $mediaImages = $allMedia->filter(fn ($m) => ! empty($m->is_image));
        $mediaVideos = $allMedia->filter(fn ($m) => ! empty($m->is_video));
        $mediaDocs = $allMedia->filter(fn ($m) => empty($m->is_image) && empty($m->is_video));

        // Filter responses
        $filteredResponses = match ($this->responseTab) {
            'offers' => $responsesWithOffers,
            'reported' => $reportedResponses,
            default => $this->discussion->responses,
        };

        return view('livewire.admin.admin-discussion-view', [
            'moderations' => $moderations,
            'openReports' => $openReports,
            'reportedResponses' => $reportedResponses,
            'openReportedResponses' => $openReportedResponses,
            'viewsCount' => $viewsCount,
            'uniqueViewers' => $uniqueViewers,
            'watchersCount' => $watchersCount,
            'responsesCount' => $responsesCount,
            'responsesWithOffersCount' => $responsesWithOffersCount,
            'totalOffersCount' => $totalOffersCount,
            'totalOffersValue' => $totalOffersValue,
            'allMedia' => $allMedia,
            'mediaImages' => $mediaImages,
            'mediaVideos' => $mediaVideos,
            'mediaDocs' => $mediaDocs,
            'filteredResponses' => $filteredResponses,
        ]);
    }
}
