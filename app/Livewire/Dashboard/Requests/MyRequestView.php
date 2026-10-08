<?php

namespace App\Livewire\Dashboard\Requests;

use App\Models\Discussion;
use App\Models\Offer;
use App\Models\Response;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyRequestView extends Component
{
    public $requestId = 'REQ-8402';
    public $discussion = null;
    public bool $isOwner = false;

    // Request details
    public string $title = 'Looking for HP EliteBook 840 G5 Motherboard';
    public string $body = 'Need a tested, clean working motherboard for HP EliteBook 840 G5 (Core i5 8th Gen). Must have zero motherboard thermal repair history.';
    public string $status = 'open';
    public string $budget = '₦70,000 - ₦90,000';
    public string $locationText = 'Ikeja, Lagos';
    public string $categoryName = 'Laptops & Computers';
    public string $deviceInfo = 'HP EliteBook 840 G5';
    public string $urgency = 'Flexible';
    public string $fulfillment = 'Flexible';
    public string $postedTime = '2 hours ago';
    public array $mediaUrls = [];

    // Offers & Responses
    public array $offers = [];
    public array $responses = [];
    public string $replyText = '';

    public function mount($id = null)
    {
        $idParam = $id ?? request()->route('id') ?? request()->query('id', 'REQ-8402');
        $this->requestId = (string) $idParam;
        $this->loadRequestData();
    }

    public function loadRequestData()
    {
        $cleanId = preg_replace('/[^0-9]/', '', $this->requestId);
        if ($cleanId && is_numeric($cleanId)) {
            $user = Auth::user();
            $d = Discussion::with([
                'user.primaryLocation',
                'category',
                'brand',
                'deviceModel',
                'location',
                'media',
                'latestModeration',
                'responses.user.primaryLocation',
                'responses.offers.sender.primaryLocation',
                'responses.offers.items',
                'offers.sender.primaryLocation',
                'offers.items',
            ])->find($cleanId);

            if ($d) {
                $this->discussion = $d;
                $this->isOwner = ($user && $user->id === $d->user_id);

                $this->title = $d->title;
                $this->body = $d->body;
                $this->status = $d->status;
                $this->budget = $d->budget ?: ($d->attachments['budget'] ?? 'Flexible');
                $this->locationText = $d->location_text ?? ($d->attachments['location'] ?? 'Nigeria');
                $this->categoryName = $d->category?->name ?? 'General Category';
                $this->deviceInfo = trim(($d->brand?->name ?? '') . ' ' . ($d->deviceModel?->name ?? ''));
                $this->urgency = $d->urgency;
                $this->fulfillment = $d->fulfillment;
                $this->postedTime = $d->created_at->diffForHumans();

                // Attached Media Photos
                $this->mediaUrls = $d->media->pluck('url')->filter()->values()->toArray();
                if (empty($this->mediaUrls) && !empty($d->attachments['images']) && is_array($d->attachments['images'])) {
                    $this->mediaUrls = $d->attachments['images'];
                }

                // Direct discussion offers + response offers
                $allOffers = Offer::with(['sender.primaryLocation', 'items'])
                    ->where('discussion_id', $d->id)
                    ->orWhereIn('response_id', $d->responses->pluck('id'))
                    ->latest()
                    ->get();

                $loadedOffers = [];
                foreach ($allOffers as $off) {
                    $loadedOffers[] = [
                        'id' => $off->id,
                        'vendor_id' => $off->sender_id,
                        'vendor_name' => $off->sender?->business_name ?: $off->sender?->name ?: 'Vendor Store',
                        'vendor_city' => $off->sender?->primaryLocation?->city ? "{$off->sender->primaryLocation->city}, {$off->sender->primaryLocation->state->name}" : 'Nigeria',
                        'vendor_verified' => (bool) $off->sender?->is_verified,
                        'price' => $off->total(),
                        'warranty' => $off->maxWarrantyDays() ? "{$off->maxWarrantyDays()} DAYS" : 'Standard',
                        'delivery' => $off->delivery_method === 'seller_responsible' ? 'Seller Delivery' : ($off->delivery_method === 'platform_responsible' ? 'Platform Courier' : 'Buyer Pickup'),
                        'status' => $off->status,
                        'time' => $off->created_at->diffForHumans(),
                        'items' => $off->items->map(fn ($item) => [
                            'description' => $item->description,
                            'type' => $item->type,
                            'quantity' => $item->quantity,
                            'price' => (float) $item->unit_price,
                        ])->toArray(),
                        'terms' => $off->terms,
                    ];
                }
                $this->offers = $loadedOffers;

                // Responses
                $loadedResponses = [];
                foreach ($d->responses()->with('user.primaryLocation')->latest()->get() as $resp) {
                    $loadedResponses[] = [
                        'id' => $resp->id,
                        'user_name' => $resp->user?->business_name ?: $resp->user?->name ?: 'Community Member',
                        'user_city' => $resp->user?->primaryLocation?->city ?? 'Nigeria',
                        'body' => $resp->body,
                        'time' => $resp->created_at->diffForHumans(),
                        'is_requester' => ($resp->user_id === $d->user_id),
                    ];
                }
                $this->responses = $loadedResponses;

                return;
            }
        }

        // Demo sample fallback
        $this->loadDemoData();
    }

    protected function loadDemoData(): void
    {
        $this->offers = [
            [
                'id' => 9021,
                'vendor_id' => 201,
                'vendor_name' => 'Abel Electronics',
                'vendor_city' => 'Computer Village, Ikeja',
                'vendor_verified' => true,
                'price' => 80000,
                'warranty' => '14 DAYS',
                'delivery' => 'Buyer Pickup (Computer Village)',
                'status' => 'pending',
                'time' => '2 hours ago',
                'items' => [
                    ['description' => 'HP EliteBook 840 G5 Motherboard (Tested OEM Pull)', 'type' => 'item', 'quantity' => 1, 'price' => 80000],
                ],
                'terms' => 'Fully tested with 14 days replacement warranty. Pick up at our shop or we can arrange dispatch.',
            ],
            [
                'id' => 9022,
                'vendor_id' => 202,
                'vendor_name' => 'Seth Tech Hub',
                'vendor_city' => 'Oregun, Ikeja',
                'vendor_verified' => true,
                'price' => 82000,
                'warranty' => '30 DAYS',
                'delivery' => 'Seller Free Delivery Included',
                'status' => 'pending',
                'time' => '1 hour ago',
                'items' => [
                    ['description' => 'HP EliteBook 840 G5 Logic Board + Thermal Paste Application', 'type' => 'item', 'quantity' => 1, 'price' => 82000],
                ],
                'terms' => '30 days warranty included. Fast same-day dispatch anywhere in Lagos.',
            ],
        ];

        $this->responses = [
            [
                'id' => 1,
                'user_name' => 'Abel Electronics',
                'user_city' => 'Computer Village, Ikeja',
                'body' => 'Is your unit the UMA graphics or discrete AMD graphics version? We have both available.',
                'time' => '3 hours ago',
                'is_requester' => false,
            ],
            [
                'id' => 2,
                'user_name' => 'You (Requester)',
                'user_city' => 'Ikeja, Lagos',
                'body' => 'Mine is the standard Intel UHD UMA graphics board. No discrete Radeon chip.',
                'time' => '2 hours ago',
                'is_requester' => true,
            ],
        ];
    }

    public function markFulfilled(): void
    {
        if ($this->discussion) {
            $this->discussion->update(['status' => 'resolved']);
            session()->flash('message', 'Request has been marked as fulfilled!');
            $this->loadRequestData();
        } else {
            $this->status = 'resolved';
            session()->flash('message', 'Demo request status updated to fulfilled.');
        }
    }

    public function closeRequest(): void
    {
        if ($this->discussion) {
            $this->discussion->update(['status' => 'closed']);
            session()->flash('message', 'Request has been closed.');
            $this->loadRequestData();
        } else {
            $this->status = 'closed';
            session()->flash('message', 'Demo request status updated to closed.');
        }
    }

    public function reopenRequest(): void
    {
        if ($this->discussion) {
            $this->discussion->update(['status' => 'open']);
            session()->flash('message', 'Request has been reopened for proposals.');
            $this->loadRequestData();
        } else {
            $this->status = 'open';
            session()->flash('message', 'Demo request status updated to open.');
        }
    }

    public function acceptOffer(int $offerId)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $offer = Offer::find($offerId);
        if (! $offer) {
            session()->flash('error', 'Offer not found.');
            return;
        }

        try {
            $negotiationService = app(NegotiationService::class);
            $invoice = $negotiationService->acceptOffer($user, $offer);

            session()->flash('message', "Offer accepted successfully! Invoice #{$invoice->invoice_number} has been generated.");
            return redirect()->route('invoice.view', ['invoice' => $invoice->id]);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function declineOffer(int $offerId): void
    {
        $offer = Offer::find($offerId);
        if ($offer) {
            $offer->update(['status' => 'declined']);
            session()->flash('message', "Offer #OFF-{$offerId} has been declined.");
            $this->loadRequestData();
        }
    }

    public function postReply(): void
    {
        $this->validate([
            'replyText' => 'required|min:3|max:1000',
        ]);

        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->discussion) {
            Response::create([
                'discussion_id' => $this->discussion->id,
                'user_id' => $user->id,
                'body' => $this->replyText,
                'status' => 'visible',
            ]);

            $this->replyText = '';
            session()->flash('message', 'Your reply has been posted.');
            $this->loadRequestData();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.requests.my-request-view');
    }
}
