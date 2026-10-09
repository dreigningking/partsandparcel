<?php

namespace App\Livewire\Dashboard\Disputes;

use App\Models\Dispute;
use App\Models\DisputeEvidence;
use App\Services\PostSale\IssueResolutionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Dispute Case File — Customer Resolution Desk')]
class DisputeView extends Component
{
    public ?int $disputeId = null;
    public string $caseId = 'DSP-0001';
    public string $status = 'open'; // 'open' | 'resolved'
    public string $originCategory = 'Transaction Issue';
    public string $typeLabel = 'Rejection Contested';
    public string $currencySymbol = '₦';
    public float $orderTotal = 145000;
    public float $escrowFee = 3500;
    public float $logisticsFee = 5000;
    public float $totalFrozenCapital = 153500;
    public string $settlementRef = 'STL-491';
    public string $invoiceNumber = 'INV-8910';
    public int $invoiceId = 1;

    // Legacy aliases for backward compatibility
    public string $openerName = 'TechSam Autos';
    public string $openerEmail = 'techsam.repair@gmail.com';
    public string $respondentName = 'Abel Auto Parts & Diagnostics';
    public string $respondentEmail = 'sales@abelelectronics.ng';
    public string $reason = 'Pin #14 is completely burned with black thermal scorch marks on the internal motherboard layer.';
    public string $resolution = 'Ruling in buyer\'s favor for full refund upon return of the physical module to the seller.';

    // Parties
    public string $buyerName = 'TechSam Autos';
    public string $buyerEmail = 'techsam.repair@gmail.com';
    public string $buyerPhone = '0803 123 4567';
    public string $buyerHub = 'Ikeja Auto Area, Lagos';
    public int $buyerCompletedOrders = 19;
    public string $buyerDisputeRate = '5.2% (1)';

    public string $sellerName = 'Abel Auto Parts & Diagnostics';
    public string $sellerEmail = 'sales@abelelectronics.ng';
    public string $sellerPhone = '0818 987 6543';
    public string $sellerHub = 'Ladipo Market, Lagos';
    public int $sellerFulfilledSales = 84;
    public string $sellerDisputeRate = '2.3% (2)';

    // Claims statements
    public string $complainantStatement = 'We ordered this Bosch MEVD17.2 ECU module described as grade-A tested working. Upon delivery by courier yesterday, we unboxed and inspected the circuit pins before connection. Pin #14 is completely burned with black thermal scorch marks on the internal motherboard layer. This was pre-existing burnt hardware and not caused by us. We rejected delivery immediately.';
    public string $respondentDefense = 'This ECU was pulled from a running 2011 328i and bench-tested on our oscilloscope prior to packing. We sealed the casing with yellow tamper tape (Serial #TK-992). The buyer attempted to flash foreign software or shorted their vehicle harness during fitment before claiming it arrived defective. We contest the rejection and request escrow payout.';
    public string $disputedItemTitle = 'BMW E90 / E92 Bosch DME Engine ECU (MEVD17.2)';
    public string $disputedItemPartNumber = 'Part #12147572342 • 6-Cylinder N52 / N54 Platform Engine Control Unit';
    public string $disputedItemDefect = 'Pin #14 Thermal Board Scorch';
    public float $disputedItemAmount = 145000;

    // Logistics Waybills
    public string $outboundCarrier = 'GIG Logistics Nigeria';
    public string $outboundWaybill = 'GIG-8947-LAG-01';
    public string $outboundDispatchedAt = 'Oct 02, 2026 · 09:30';
    public string $outboundRoute = 'Ladipo Market, Mushin › Ikeja Auto Hub, Lagos';
    public string $outboundStatus = 'Rejected at Destination';

    public string $returnWaybill = 'RET-TRACK-9941';
    public string $returnCarrier = 'Platform Secure Return Service';
    public string $returnDestination = 'Ladipo Hub Store #14, Lagos';
    public string $returnStatus = 'Pending Arbitrator Order';

    // Current viewer perspective for interactive previewing ('buyer' or 'seller')
    public string $viewAs = 'buyer';

    // Navigation Tabs (4 focused tabs — chat is removed!)
    public string $activeTab = 'evidence'; // 'evidence', 'claims', 'logistics', 'timeline'

    // Evidence Requests by Admin
    public array $evidenceRequests = [];

    // Chronological Lifecycle Timeline Milestones
    public array $timelineEvents = [];

    // Upload Evidence Modal State
    public bool $showUploadModal = false;
    public ?int $activeRequestId = null;
    public string $uploadNotes = '';

    // Admin Ruling Record (Read-only for parties)
    public string $decision = 'buyer_favor';
    public float $refundAmount = 145000;
    public string $resolutionNotes = 'After examining high-resolution photos of the unit, the manufacturer serial number on the PCB matches the invoice, but physical inspection of the microchip pin 14 reveals thermal damage prior to customer delivery. Ruling in buyer\'s favor for full refund upon return of the physical module to the seller.';
    public string $resolverName = 'Platform Arbitration Panel';
    public string $resolvedAt = 'Oct 04, 2026 · 03:30 PM';

    // Lightbox image preview modal
    public ?string $previewImageModalUrl = null;
    public ?string $previewImageModalTitle = null;

    public function mount($dispute_id = null): void
    {
        $this->loadDispute($dispute_id);
    }

    public function loadDispute($id = null): void
    {
        $numericId = $id ? (int) preg_replace('/[^0-9]/', '', (string) $id) : null;

        $relations = [
            'invoice.buyer',
            'invoice.seller',
            'invoice.items',
            'invoice.payments',
            'invoice.settlement',
            'opener',
            'respondent',
            'resolver',
            'issue',
            'returnRecord',
            'replacement',
            'warrantyClaim',
            'evidences.requester',
            'evidences.targetUser',
            'evidences.submitter',
            'returnShipment',
        ];

        $user = Auth::user();
        $dispute = null;

        if ($numericId) {
            $dispute = Dispute::with($relations)->find($numericId);
            if (! $dispute && Dispute::exists()) {
                abort(404, 'Dispute not found.');
            }
        } elseif ($user) {
            // Load authenticated user's latest dispute
            $dispute = Dispute::with($relations)
                ->where(function ($q) use ($user) {
                    $q->where('opened_by', $user->id)
                      ->orWhere('respondent_id', $user->id)
                      ->orWhereHas('invoice', fn ($iq) => $iq->where('buyer_id', $user->id)->orWhere('seller_id', $user->id));
                })
                ->latest()
                ->first();
        }

        // Authorization check: User must be a party to this dispute or platform admin
        if ($dispute && $user) {
            $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();
            $isParty = $dispute->opened_by === $user->id
                || $dispute->respondent_id === $user->id
                || ($dispute->invoice && ($dispute->invoice->buyer_id === $user->id || $dispute->invoice->seller_id === $user->id));

            if (! $isParty && ! $isAdmin) {
                abort(403, 'Unauthorized access to this dispute case.');
            }
        }

        if ($dispute) {
            $this->disputeId = $dispute->id;
            $this->caseId = 'DSP-' . str_pad((string) $dispute->id, 4, '0', STR_PAD_LEFT);
            $this->originCategory = $dispute->originCategory();
            $this->typeLabel = $dispute->typeLabel();
            $this->status = ($dispute->status === 'resolved') ? 'resolved' : 'open';
            $this->decision = $dispute->decision ?? 'buyer_favor';
            $this->resolutionNotes = $dispute->resolution_notes ?? ($dispute->resolution ?? $this->resolutionNotes);
            $this->resolverName = $dispute->resolver?->name ?? 'Platform Arbitration Panel';
            $this->resolvedAt = $dispute->resolved_at ? $dispute->resolved_at->format('M d, Y · H:i A') : $this->resolvedAt;

            $invoice = $dispute->invoice;
            if ($invoice) {
                $this->invoiceId = $invoice->id;
                $this->invoiceNumber = $invoice->invoice_number;
                $this->currencySymbol = $invoice->currency_symbol ?? '₦';
                $this->orderTotal = (float) $invoice->total;
                $this->refundAmount = (float) ($dispute->refund_amount ?? $invoice->total);

                if ($invoice->settlement) {
                    $this->settlementRef = "STL-{$invoice->settlement->id}";
                }

                $buyer = $invoice->buyer ?? $dispute->opener;
                if ($buyer) {
                    $this->buyerName = $buyer->name;
                    $this->openerName = $buyer->name;
                    $this->buyerEmail = $buyer->email;
                    $this->openerEmail = $buyer->email;
                    $this->buyerPhone = $buyer->phone ?? $this->buyerPhone;
                }

                $seller = $invoice->seller ?? $dispute->respondent;
                if ($seller) {
                    $this->sellerName = $seller->name;
                    $this->respondentName = $seller->name;
                    $this->sellerEmail = $seller->email;
                    $this->respondentEmail = $seller->email;
                    $this->sellerPhone = $seller->phone ?? $this->sellerPhone;
                }

                $firstItem = $invoice->items->first();
                if ($firstItem) {
                    $this->disputedItemTitle = $firstItem->description;
                    $this->disputedItemAmount = (float) $firstItem->amount;
                }
            }

            if ($dispute->reason) {
                $this->complainantStatement = $dispute->reason;
                $this->reason = $dispute->reason;
            }
            if ($dispute->respondent_defense) {
                $this->respondentDefense = $dispute->respondent_defense;
            }

            // Determine initial view perspective based on current logged in user
            $currentUserId = Auth::id();
            if ($currentUserId && $invoice) {
                if ($invoice->seller_id === $currentUserId) {
                    $this->viewAs = 'seller';
                } else {
                    $this->viewAs = 'buyer';
                }
            }

            // Load evidences
            $this->evidenceRequests = $this->transformEvidences($dispute);

            // Generate timeline from real invoice & post-sale lifecycle models
            $this->timelineEvents = $dispute->generateLifecycleTimeline();
        } else {
            if ($numericId) {
                $this->caseId = 'DSP-' . str_pad((string) $numericId, 4, '0', STR_PAD_LEFT);
            }
            // Default demo fallback if database has zero dispute records
            $this->loadDefaultDemoData();
        }
    }

    protected function transformEvidences(Dispute $dispute): array
    {
        $evidences = $dispute->evidences()->latest()->get();

        if ($evidences->isEmpty()) {
            return $this->getDefaultDemoEvidences();
        }

        return $evidences->map(function (DisputeEvidence $ev) {
            $targetName = $ev->targetUser?->name
                ? "{$ev->targetUser->name} (" . ucfirst($ev->target_party) . ")"
                : ($ev->target_party === 'buyer' ? "{$this->buyerName} (Buyer)" : "{$this->sellerName} (Seller)");

            return [
                'id' => $ev->id,
                'target' => $ev->target_party,
                'target_name' => $targetName,
                'title' => $ev->title,
                'instructions' => $ev->instructions ?: 'Please provide requested evidence photos or documentation promptly.',
                'status' => $ev->status,
                'requested_at' => $ev->created_at ? $ev->created_at->format('M d, Y · H:i') : null,
                'submitted_at' => $ev->submitted_at ? $ev->submitted_at->format('M d, Y · H:i') : null,
                'deadline' => $ev->formattedDeadline(),
                'party_notes' => $ev->party_notes,
                'files' => $ev->files ?? [],
            ];
        })->toArray();
    }

    protected function loadDefaultDemoData(): void
    {
        $this->evidenceRequests = $this->getDefaultDemoEvidences();
        $this->timelineEvents = [
            [
                'title' => 'Escrow Payment Confirmed',
                'description' => '₦153,500.00 secured via Paystack gateway.',
                'time' => 'Oct 01, 2026 · 11:15',
                'icon' => 'fa-shield-halved',
                'color' => 'emerald',
            ],
            [
                'title' => 'Courier Waybill Dispatched',
                'description' => 'Seller Abel Auto Parts handed package to GIG Logistics.',
                'time' => 'Oct 02, 2026 · 09:30',
                'icon' => 'fa-truck',
                'color' => 'blue',
            ],
            [
                'title' => 'Delivery Rejected by Buyer',
                'description' => 'Buyer filed defect report with pin 14 scorch macro evidence.',
                'time' => 'Oct 03, 2026 · 14:22',
                'icon' => 'fa-circle-xmark',
                'color' => 'rose',
            ],
            [
                'title' => 'Seller Contested Rejection',
                'description' => 'Seller formally disputed claim. Escrow funds locked for mediation.',
                'time' => 'Oct 03, 2026 · 17:06',
                'icon' => 'fa-scale-balanced',
                'color' => 'purple',
            ],
            [
                'title' => 'Admin Opened Evidence Requisition #REQ-103',
                'description' => 'Requested high-resolution photo of vehicle wiring harness pin connector.',
                'time' => 'Oct 04, 2026 · 11:00',
                'icon' => 'fa-camera',
                'color' => 'blue',
            ],
        ];
    }

    protected function getDefaultDemoEvidences(): array
    {
        return [
            [
                'id' => 101,
                'target' => 'buyer',
                'target_name' => 'TechSam Autos (Buyer)',
                'title' => 'Macro unboxing photo showing pin 14 scorch mark',
                'instructions' => 'Please provide clear close-up photos of the connector pins under daylight and photo of courier outer packaging.',
                'status' => 'submitted',
                'requested_at' => 'Oct 03, 2026 · 14:30',
                'submitted_at' => 'Oct 03, 2026 · 16:10',
                'party_notes' => 'Macro photo showing burned pin 14 and box condition with intact yellow tamper seal.',
                'files' => [
                    [
                        'name' => 'pin14_thermal_damage.jpg',
                        'url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1000&auto=format&fit=crop&q=80',
                        'thumb' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                        'label' => 'Pin 14 Board Scorch Macro Inspection',
                    ],
                    [
                        'name' => 'unboxing_box_state.jpg',
                        'url' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=1000&auto=format&fit=crop&q=80',
                        'thumb' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=600&auto=format&fit=crop&q=80',
                        'label' => 'Unboxing Package Seal State',
                    ],
                ],
            ],
            [
                'id' => 102,
                'target' => 'seller',
                'target_name' => 'Abel Auto Parts (Seller)',
                'title' => 'Pre-dispatch bench oscilloscope telemetry & serialized tape',
                'instructions' => 'Provide oscilloscope wave test log and clear photo of serialized yellow security tape before parcel was sealed.',
                'status' => 'submitted',
                'requested_at' => 'Oct 02, 2026 · 10:15',
                'submitted_at' => 'Oct 02, 2026 · 14:40',
                'party_notes' => 'Attached oscilloscope telemetry log and yellow tamper seal TK-992 photographed prior to courier handover.',
                'files' => [
                    [
                        'name' => 'bench_test_telemetry.jpg',
                        'url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1000&auto=format&fit=crop&q=80',
                        'thumb' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80',
                        'label' => 'Oscilloscope Wave Telemetry',
                    ],
                    [
                        'name' => 'tamper_seal_tk992.jpg',
                        'url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1000&auto=format&fit=crop&q=80',
                        'thumb' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=80',
                        'label' => 'Serialized Tamper Seal TK-992',
                    ],
                ],
            ],
            [
                'id' => 103,
                'target' => 'buyer',
                'target_name' => 'TechSam Autos (Buyer)',
                'title' => 'Photo of vehicle wiring harness pin connector',
                'instructions' => 'Admin requests a clear photo of your car harness plug to verify no short circuit pins or carbon buildup exists on vehicle side.',
                'status' => 'pending',
                'requested_at' => 'Oct 04, 2026 · 11:00',
                'deadline' => 'Within 24 Hours (Due Oct 05, 11:00)',
                'party_notes' => null,
                'files' => [],
            ],
        ];
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['evidence', 'claims', 'logistics', 'timeline'])) {
            $this->activeTab = $tab;
        }
    }

    public function setPerspective(string $role): void
    {
        if (in_array($role, ['buyer', 'seller'])) {
            $this->viewAs = $role;
        }
    }

    public function toggleResolutionStatus(): void
    {
        $this->status = $this->status === 'resolved' ? 'open' : 'resolved';
    }

    // Party Evidence Upload Actions
    public function openUploadModal(int $requestId): void
    {
        $this->activeRequestId = $requestId;
        $this->uploadNotes = '';
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->activeRequestId = null;
    }

    public function submitEvidence(): void
    {
        $mockFiles = [
            [
                'name' => 'harness_socket_photo.jpg',
                'url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1000&auto=format&fit=crop&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                'label' => 'Vehicle Harness Connector Photo',
            ],
        ];

        if ($this->disputeId) {
            $ev = DisputeEvidence::find($this->activeRequestId);
            if ($ev && $ev->status === 'pending') {
                $submitter = Auth::user() ?? ($ev->targetUser ?? User::first());
                if ($submitter) {
                    app(IssueResolutionService::class)->submitEvidence(
                        evidence: $ev,
                        submitter: $submitter,
                        partyNotes: trim($this->uploadNotes) ?: 'Attached requested photos and documentation for arbitrator review.',
                        files: $mockFiles
                    );

                    if ($dispute = Dispute::find($this->disputeId)) {
                        $this->evidenceRequests = $this->transformEvidences($dispute);
                        $this->timelineEvents = $dispute->generateLifecycleTimeline();
                    }

                    $this->showUploadModal = false;
                    $this->activeRequestId = null;
                    session()->flash('status', 'Your evidence documents have been successfully submitted to the platform arbitrator.');
                    return;
                }
            }
        }

        // Fallback for demo state
        foreach ($this->evidenceRequests as &$req) {
            if ($req['id'] === $this->activeRequestId) {
                $req['status'] = 'submitted';
                $req['submitted_at'] = now()->format('M d, Y · H:i');
                $req['party_notes'] = trim($this->uploadNotes) ?: 'Attached requested photos and documentation for arbitrator review.';
                $req['files'] = $mockFiles;
                break;
            }
        }

        $this->showUploadModal = false;
        $this->activeRequestId = null;
        session()->flash('status', 'Your evidence documents have been successfully submitted to the platform arbitrator.');
    }

    public function getActiveRequestProperty(): ?array
    {
        if (! $this->activeRequestId) {
            return null;
        }

        foreach ($this->evidenceRequests as $req) {
            if ($req['id'] === $this->activeRequestId) {
                return $req;
            }
        }

        return null;
    }

    public function openImagePreview(string $url, string $title = 'Evidence Document'): void
    {
        $this->previewImageModalUrl = $url;
        $this->previewImageModalTitle = $title;
    }

    public function closeImagePreview(): void
    {
        $this->previewImageModalUrl = null;
        $this->previewImageModalTitle = null;
    }

    public function render()
    {
        return view('livewire.dashboard.disputes.dispute-view');
    }
}
