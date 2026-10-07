<?php

namespace App\Livewire\Admin;

use App\Models\Dispute;
use App\Models\DisputeEvidence;
use App\Services\PostSale\IssueResolutionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Dispute Arbitration Desk — Admin Control Center')]
class AdminDisputeView extends Component
{
    public ?int $disputeId = null;
    public string $caseId = 'DSP-0001';
    public string $invoiceNumber = 'INV-8910';
    public int $invoiceId = 1;
    public string $originCategory = 'Transaction Issue';
    public string $typeLabel = 'Rejection Contested';
    public string $currencySymbol = '₦';
    public float $orderTotal = 145000;
    public float $escrowFee = 3500;
    public float $logisticsFee = 5000;
    public float $totalFrozenCapital = 153500;
    public string $settlementRef = 'STL-491';

    // Complainant (Buyer)
    public string $buyerName = 'TechSam Autos';
    public string $buyerEmail = 'techsam.repair@gmail.com';
    public string $buyerPhone = '0803 123 4567';
    public string $buyerHub = 'Ikeja Auto Area, Lagos';
    public int $buyerCompletedOrders = 19;
    public string $buyerDisputeRate = '5.2% (1)';

    // Respondent (Seller)
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

    // Navigation Tabs (4 focused tabs — chat is removed!)
    public string $activeTab = 'evidence'; // 'evidence', 'claims', 'logistics', 'timeline'

    // Arbitration Ruling Controls
    public string $decision = 'buyer_favor'; // 'buyer_favor', 'seller_favor', 'split'
    public float $refundAmount = 145000;
    public bool $requireReturn = true;
    public string $resolutionNotes = 'After examining high-resolution photos of the unit, the manufacturer serial number on the PCB matches the invoice, but physical inspection of the microchip pin 14 reveals thermal damage prior to customer delivery. Ruling in buyer\'s favor for full refund upon return of the physical module to the seller.';
    public string $internalNotes = 'Seller has had 2 recent disputes regarding SAM modules. Flag merchant for supplier inventory check.';
    public bool $isResolved = false;

    // Evidence Lightbox Preview Modal
    public ?string $previewImageModalUrl = null;
    public ?string $previewImageModalTitle = null;

    // Evidence Request System
    public bool $showRequestModal = false;
    public string $requestTarget = 'seller'; // 'buyer' or 'seller'
    public string $requestTitle = '';
    public string $requestInstructions = '';
    public string $requestDeadline = '24_hours';

    // Evidence Requests List
    public array $evidenceRequests = [];

    // Chronological Lifecycle Timeline Milestones
    public array $timelineEvents = [];

    public function mount($id = null): void
    {
        $this->loadDispute($id);
    }

    public function loadDispute($id = null): void
    {
        $numericId = $id ? (int) preg_replace('/[^0-9]/', '', (string) $id) : null;

        $dispute = null;
        if ($numericId) {
            $dispute = Dispute::with([
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
            ])->find($numericId);
        }

        if (! $dispute) {
            $dispute = Dispute::with([
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
            ])->latest()->first();
        }

        if ($dispute) {
            $this->disputeId = $dispute->id;
            $this->caseId = 'DSP-' . str_pad((string) $dispute->id, 4, '0', STR_PAD_LEFT);
            $this->originCategory = $dispute->originCategory();
            $this->typeLabel = $dispute->typeLabel();
            $this->isResolved = ($dispute->status === 'resolved');
            $this->decision = $dispute->decision ?? 'buyer_favor';
            $this->requireReturn = (bool) ($dispute->require_return ?? true);
            $this->resolutionNotes = $dispute->resolution_notes ?? ($dispute->resolution ?? $this->resolutionNotes);
            $this->internalNotes = $dispute->internal_notes ?? $this->internalNotes;

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
                    $this->buyerEmail = $buyer->email;
                    $this->buyerPhone = $buyer->phone ?? $this->buyerPhone;
                }

                $seller = $invoice->seller ?? $dispute->respondent;
                if ($seller) {
                    $this->sellerName = $seller->name;
                    $this->sellerEmail = $seller->email;
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
            }
            if ($dispute->respondent_defense) {
                $this->respondentDefense = $dispute->respondent_defense;
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
                'description' => '₦153,500.00 secured via Paystack escrow gateway.',
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
                'description' => 'Requested supplier procurement receipt and verified donor vehicle mileage log.',
                'time' => 'Oct 04, 2026 · 09:15',
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
                'target' => 'seller',
                'target_name' => 'Abel Auto Parts (Seller)',
                'title' => 'Pre-dispatch bench test telemetry & serialized tamper seal',
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
                'id' => 102,
                'target' => 'buyer',
                'target_name' => 'TechSam Autos (Buyer)',
                'title' => 'Macro unboxing photograph showing pin 14 scorch mark',
                'instructions' => 'Upload clear high-resolution photo under direct lighting showing the circuit connector and courier outer box.',
                'status' => 'submitted',
                'requested_at' => 'Oct 03, 2026 · 14:30',
                'submitted_at' => 'Oct 03, 2026 · 16:10',
                'party_notes' => 'Macro photo of scorch mark on pin 14 and box condition showing no external crush damage from delivery transit.',
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
                        'label' => 'Unboxing Package Condition',
                    ],
                ],
            ],
            [
                'id' => 103,
                'target' => 'seller',
                'target_name' => 'Abel Auto Parts (Seller)',
                'title' => 'Original procurement receipt & supplier part warranty certificate',
                'instructions' => 'Please provide documentation proving this unit was sourced from a certified donor vehicle with mileage log.',
                'status' => 'pending',
                'requested_at' => 'Oct 04, 2026 · 09:15',
                'deadline' => 'Within 24 Hours (Due Oct 05, 09:15)',
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

    public function setDecision(string $decision): void
    {
        if (! in_array($decision, ['buyer_favor', 'seller_favor', 'split'])) {
            return;
        }

        $this->decision = $decision;

        if ($decision === 'seller_favor') {
            $this->refundAmount = 0;
        } elseif ($decision === 'buyer_favor') {
            $this->refundAmount = $this->orderTotal;
        } elseif ($decision === 'split') {
            $this->refundAmount = round($this->orderTotal / 2, 2);
        }
    }

    public function executeArbitration(): void
    {
        if ($this->disputeId && ($dispute = Dispute::find($this->disputeId))) {
            $adminUser = Auth::user();
            if ($adminUser) {
                app(IssueResolutionService::class)->adminResolveDispute(
                    dispute: $dispute,
                    admin: $adminUser,
                    decision: $this->decision,
                    refundAmount: $this->refundAmount,
                    resolutionNotes: $this->resolutionNotes,
                    requireReturn: $this->requireReturn,
                    internalNotes: $this->internalNotes
                );
            }
        }

        $this->isResolved = true;
        session()->flash('status', "Official Arbitration Judgment Executed: Case #{$this->caseId} resolved with verdict ({$this->decision}). Settlement payout dispatched.");
    }

    // Evidence Requests Handling
    public function openRequestModal(?string $target = null): void
    {
        if ($target && in_array($target, ['buyer', 'seller'])) {
            $this->requestTarget = $target;
        }
        $this->requestTitle = '';
        $this->requestInstructions = '';
        $this->requestDeadline = '24_hours';
        $this->showRequestModal = true;
    }

    public function closeRequestModal(): void
    {
        $this->showRequestModal = false;
    }

    public function submitEvidenceRequest(): void
    {
        if (empty(trim($this->requestTitle))) {
            $this->addError('requestTitle', 'Please describe what evidence document or photo is requested.');
            return;
        }

        $targetName = $this->requestTarget === 'buyer' ? "{$this->buyerName} (Buyer)" : "{$this->sellerName} (Seller)";

        if ($this->disputeId && ($dispute = Dispute::find($this->disputeId))) {
            $adminUser = Auth::user();
            if ($adminUser) {
                $ev = app(IssueResolutionService::class)->requestEvidence(
                    dispute: $dispute,
                    admin: $adminUser,
                    targetParty: $this->requestTarget,
                    title: trim($this->requestTitle),
                    instructions: trim($this->requestInstructions) ?: null,
                    deadlinePreset: $this->requestDeadline
                );

                $this->evidenceRequests = $this->transformEvidences($dispute);
                $this->timelineEvents = $dispute->generateLifecycleTimeline();
                $this->showRequestModal = false;
                session()->flash('status', "Official evidence request #REQ-{$ev->id} sent to {$targetName}. Party has been alerted via email.");
                return;
            }
        }

        // Fallback for demo state
        $deadlineText = match ($this->requestDeadline) {
            '12_hours' => 'Within 12 Hours',
            '48_hours' => 'Within 48 Hours',
            default => 'Within 24 Hours',
        };

        $newId = count($this->evidenceRequests) + 101;
        $newRequest = [
            'id' => $newId,
            'target' => $this->requestTarget,
            'target_name' => $targetName,
            'title' => trim($this->requestTitle),
            'instructions' => trim($this->requestInstructions) ?: 'Please provide requested evidence photos or documentation promptly.',
            'status' => 'pending',
            'requested_at' => now()->format('M d, Y · H:i'),
            'deadline' => $deadlineText,
            'party_notes' => null,
            'files' => [],
        ];

        array_unshift($this->evidenceRequests, $newRequest);
        $this->showRequestModal = false;
        session()->flash('status', "Official evidence request #REQ-{$newId} sent to {$targetName}. Party has been alerted via email.");
    }

    public function simulatePartySubmission(int $requestId): void
    {
        if ($this->disputeId) {
            $ev = DisputeEvidence::find($requestId);
            if ($ev && $ev->status === 'pending') {
                $submitter = $ev->targetUser ?? Auth::user();
                if ($submitter) {
                    $mockFiles = [
                        [
                            'name' => 'submitted_verification_proof.jpg',
                            'url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1000&auto=format&fit=crop&q=80',
                            'thumb' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                            'label' => 'Verified Submission Document',
                        ],
                    ];

                    app(IssueResolutionService::class)->submitEvidence(
                        evidence: $ev,
                        submitter: $submitter,
                        partyNotes: 'Uploaded official documentation as requested by platform arbitration desk.',
                        files: $mockFiles
                    );

                    if ($dispute = Dispute::find($this->disputeId)) {
                        $this->evidenceRequests = $this->transformEvidences($dispute);
                        $this->timelineEvents = $dispute->generateLifecycleTimeline();
                    }

                    session()->flash('status', "Simulated party submission received for Request #REQ-{$requestId}. Evidence exhibits are now available below for review.");
                    return;
                }
            }
        }

        // Fallback for demo state
        foreach ($this->evidenceRequests as &$req) {
            if ($req['id'] === $requestId && $req['status'] === 'pending') {
                $req['status'] = 'submitted';
                $req['submitted_at'] = now()->format('M d, Y · H:i');
                $req['party_notes'] = 'Uploaded official documentation as requested by platform arbitration desk.';
                $req['files'] = [
                    [
                        'name' => 'submitted_verification_proof.jpg',
                        'url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1000&auto=format&fit=crop&q=80',
                        'thumb' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                        'label' => 'Verified Submission Document',
                    ],
                ];
                session()->flash('status', "Simulated party submission received for Request #REQ-{$requestId}. Evidence exhibits are now available below for review.");
                return;
            }
        }
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
        return view('livewire.admin.admin-dispute-view');
    }
}
