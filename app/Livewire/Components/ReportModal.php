<?php

namespace App\Livewire\Components;

use App\Models\Discussion;
use App\Models\Listing;
use App\Models\Report;
use App\Models\Response;
use App\Notifications\ListingReportedNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportModal extends Component
{
    public bool $isOpen = false;
    public string $reportableType = ''; // 'listing', 'discussion', 'response'
    public ?int $reportableId = null;

    // Entity Preview
    public string $targetTypeLabel = '';
    public string $targetTitle = '';
    public string $targetSubtitle = '';
    public string $targetAuthor = '';
    public string $targetExcerpt = '';
    public ?string $targetImageUrl = null;

    // Existing Report Status
    public bool $hasAlreadyReported = false;
    public ?array $existingReport = null;

    // Form inputs
    public string $reportTitle = 'Spam or Misleading';
    public string $reportDescription = '';

    public array $reasonOptions = [
        'Spam or Misleading' => 'Spam, advertisement or promotional content',
        'Fraud or Scam' => 'Suspicious behavior, fake item or payment scam',
        'Prohibited or Illegal Content' => 'Violates safety or regulatory policies',
        'Harassment or Hate Speech' => 'Inappropriate, abusive or offensive conduct',
        'Inaccurate Information' => 'Incorrect specifications, misleading condition or false details',
        'Other' => 'Other reason not covered above',
    ];

    #[On('open-report-modal')]
    public function loadEntity($type, $id)
    {
        $this->reportableType = strtolower((string) $type);
        $this->reportableId = (int) $id;
        $this->reportTitle = 'Spam or Misleading';
        $this->reportDescription = '';

        $resolvedClass = $this->resolveModelClass($this->reportableType);
        if (! $resolvedClass) {
            return;
        }

        $user = Auth::user();

        // Check if user already reported this item
        $existing = null;
        if ($user) {
            $existing = Report::with('resolvedBy')
                ->where('user_id', $user->id)
                ->where('reportable_type', $resolvedClass)
                ->where('reportable_id', $this->reportableId)
                ->first();
        }

        if ($existing) {
            $this->hasAlreadyReported = true;
            $this->existingReport = [
                'id' => $existing->id,
                'title' => $existing->title,
                'description' => $existing->description,
                'status' => $existing->status, // 'pending', 'reviewed', 'resolved', 'dismissed'
                'resolution_notes' => $existing->resolution_notes,
                'resolved_by' => $existing->resolvedBy?->name ?? 'Moderation Team',
                'created_at' => $existing->created_at->format('M d, Y · h:i A'),
                'updated_at' => $existing->updated_at->format('M d, Y · h:i A'),
            ];
        } else {
            $this->hasAlreadyReported = false;
            $this->existingReport = null;
        }

        // Populate preview details
        if ($resolvedClass === Listing::class) {
            $this->targetTypeLabel = 'Listing';
            $listing = Listing::with(['item.media', 'seller'])->find($this->reportableId);
            if ($listing) {
                $this->targetTitle = $listing->item?->name ?? "Listing #{$listing->id}";
                $this->targetSubtitle = '₦' . number_format((float) ($listing->selling_price ?? $listing->price ?? 0));
                $this->targetAuthor = $listing->seller?->business_name ?: $listing->seller?->name ?: 'Seller';
                $this->targetExcerpt = $listing->description ?: 'No additional description provided.';
                $this->targetImageUrl = $listing->item?->firstImageUrl();
            }
        } elseif ($resolvedClass === Discussion::class) {
            $this->targetTypeLabel = 'Discussion Request';
            $discussion = Discussion::with(['user'])->find($this->reportableId);
            if ($discussion) {
                $this->targetTitle = $discussion->title;
                $this->targetSubtitle = $discussion->budget ? "Budget: {$discussion->budget}" : 'Flexible Budget';
                $this->targetAuthor = $discussion->user?->name ?? 'Community Member';
                $this->targetExcerpt = $discussion->body;
                $this->targetImageUrl = null;
            }
        } elseif ($resolvedClass === Response::class) {
            $this->targetTypeLabel = 'Discussion Reply';
            $resp = Response::with(['user', 'discussion'])->find($this->reportableId);
            if ($resp) {
                $this->targetTitle = 'Reply on: ' . ($resp->discussion?->title ?? 'Discussion');
                $this->targetSubtitle = 'Community Response';
                $this->targetAuthor = $resp->user?->business_name ?: $resp->user?->name ?: 'Vendor';
                $this->targetExcerpt = $resp->body;
                $this->targetImageUrl = null;
            }
        }

        $this->isOpen = true;
    }

    public function submitReport()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to submit a report.');
            return redirect()->route('login');
        }

        $this->validate([
            'reportTitle' => ['required', 'string', 'max:150'],
            'reportDescription' => ['nullable', 'string', 'max:2000'],
        ]);

        $resolvedClass = $this->resolveModelClass($this->reportableType);
        if (! $resolvedClass || ! $this->reportableId) {
            return;
        }

        $report = Report::create([
            'reportable_type' => $resolvedClass,
            'reportable_id' => $this->reportableId,
            'user_id' => $user->id,
            'title' => $this->reportTitle,
            'description' => $this->reportDescription,
            'status' => 'pending',
        ]);

        // If it's a listing, send courtesy notice to seller
        if ($resolvedClass === Listing::class) {
            $listing = Listing::with('seller')->find($this->reportableId);
            if ($listing && $listing->seller && $listing->seller->id !== $user->id) {
                try {
                    $listing->seller->notify(new ListingReportedNotification($listing, $report));
                } catch (\Throwable $e) {
                    // Suppress mail failure in development
                }
            }
        }

        $this->hasAlreadyReported = true;
        $this->existingReport = [
            'id' => $report->id,
            'title' => $report->title,
            'description' => $report->description,
            'status' => 'pending',
            'resolution_notes' => null,
            'resolved_by' => null,
            'created_at' => now()->format('M d, Y · h:i A'),
            'updated_at' => now()->format('M d, Y · h:i A'),
        ];

        $this->dispatch('report-submitted', [
            'type' => $this->reportableType,
            'id' => $this->reportableId,
        ]);

        session()->flash('message', 'Thank you. Your report has been submitted and will be reviewed by platform moderation.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    protected function resolveModelClass(string $type): ?string
    {
        return match (strtolower($type)) {
            'listing', Listing::class => Listing::class,
            'discussion', Discussion::class => Discussion::class,
            'response', Response::class => Response::class,
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.components.report-modal');
    }
}
