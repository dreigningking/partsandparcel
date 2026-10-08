<?php

namespace App\Livewire\Dashboard\Requests;

use App\Jobs\NotifyDiscussionEditedJob;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Offer;
use App\Models\Response;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    // Offers & Responses (Limited to last 5)
    public array $offers = [];
    public array $responses = [];

    // Edit Request Modal State & Form Fields
    public bool $showEditModal = false;
    public string $editTitle = '';
    public string $editBody = '';
    public string $editBudget = '';
    public ?int $editCategoryId = null;
    public ?int $editBrandId = null;
    public ?int $editModelId = null;
    public string $editUrgency = 'Flexible';
    public string $editFulfillment = 'Flexible';

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
                $this->isOwner = ($user && (int) $user->id === (int) $d->user_id);

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

                // Load ONLY the last 5 responses & identify those with offers/replies
                $loadedResponses = [];
                $recentResponses = $d->responses()
                    ->with(['user.primaryLocation', 'offers.items'])
                    ->latest()
                    ->take(5)
                    ->get();

                foreach ($recentResponses as $resp) {
                    $attachedOffer = $resp->offers->first();
                    $hasAttachedOffers = ! is_null($attachedOffer);
                    $hasConversation = Conversation::where('contextable_type', Discussion::class)
                        ->where('contextable_id', $d->id)
                        ->whereHas('participants', fn ($q) => $q->where('user_id', $resp->user_id))
                        ->whereHas('messages')
                        ->exists();

                    $hasReplies = $hasAttachedOffers || $hasConversation;
                    $offerPrice = $hasAttachedOffers ? $attachedOffer->total() : null;

                    $loadedResponses[] = [
                        'id' => $resp->id,
                        'user_name' => $resp->user?->business_name ?: $resp->user?->name ?: 'Community Member',
                        'user_city' => $resp->user?->primaryLocation?->city ?? 'Nigeria',
                        'body' => $resp->body,
                        'time' => $resp->created_at->diffForHumans(),
                        'is_requester' => ($resp->user_id === $d->user_id),
                        'has_replies' => $hasReplies,
                        'reply_count' => $resp->offers->count(),
                        'has_offer' => $hasAttachedOffers,
                        'offer_id' => $attachedOffer?->id,
                        'offer_price' => $offerPrice,
                        'offer_status' => $attachedOffer?->status,
                        'offer_warranty' => $attachedOffer?->maxWarrantyDays() ? "{$attachedOffer->maxWarrantyDays()} DAYS" : 'Standard',
                        'offer_delivery' => $attachedOffer?->delivery_method === 'seller_responsible' ? 'Seller Delivery' : ($attachedOffer?->delivery_method === 'platform_responsible' ? 'Platform Courier' : 'Buyer Pickup'),
                        'offer_terms' => $attachedOffer?->terms,
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

        // Demo responses (up to 5) with offer indicators
        $this->responses = [
            [
                'id' => 101,
                'user_name' => 'Abel Electronics',
                'user_city' => 'Computer Village, Ikeja',
                'body' => 'Is your unit the UMA graphics or discrete AMD graphics version? We have both available.',
                'time' => '3 hours ago',
                'is_requester' => false,
                'has_replies' => true,
                'reply_count' => 1,
                'has_offer' => true,
                'offer_id' => 9021,
                'offer_price' => 80000,
                'offer_status' => 'pending',
                'offer_warranty' => '14 DAYS',
                'offer_delivery' => 'Buyer Pickup (Computer Village)',
                'offer_terms' => 'Fully tested with 14 days replacement warranty. Pick up at our shop or we can arrange dispatch.',
            ],
            [
                'id' => 102,
                'user_name' => 'Seth Tech Hub',
                'user_city' => 'Oregun, Ikeja',
                'body' => 'We have clean pull boards for G5 and G6 models in stock with thermal testing report.',
                'time' => '2 hours ago',
                'is_requester' => false,
                'has_replies' => true,
                'reply_count' => 1,
                'has_offer' => true,
                'offer_id' => 9022,
                'offer_price' => 82000,
                'offer_status' => 'pending',
                'offer_warranty' => '30 DAYS',
                'offer_delivery' => 'Seller Free Delivery Included',
                'offer_terms' => '30 days warranty included. Fast same-day dispatch anywhere in Lagos.',
            ],
            [
                'id' => 103,
                'user_name' => 'Lagos Component Exchange',
                'user_city' => 'Alaba International',
                'body' => 'Please confirm if you also need the internal heatsink and fan assembly.',
                'time' => '1 hour ago',
                'is_requester' => false,
                'has_replies' => false,
                'reply_count' => 0,
                'has_offer' => false,
                'offer_id' => null,
                'offer_price' => null,
                'offer_status' => null,
                'offer_warranty' => null,
                'offer_delivery' => null,
                'offer_terms' => null,
            ],
        ];
    }

    public function openEditModal(): void
    {
        if ($this->discussion) {
            $this->editTitle = $this->discussion->title;
            $this->editBody = $this->discussion->body;
            $this->editBudget = $this->discussion->budget ?: ($this->discussion->attachments['budget'] ?? '');
            $this->editCategoryId = $this->discussion->category_id;
            $this->editBrandId = $this->discussion->brand_id;
            $this->editModelId = $this->discussion->model_id;
            $this->editUrgency = $this->discussion->urgency ?? 'Flexible';
            $this->editFulfillment = $this->discussion->fulfillment ?? 'Flexible';
        } else {
            $this->editTitle = $this->title;
            $this->editBody = $this->body;
            $this->editBudget = $this->budget;
            $this->editUrgency = $this->urgency;
            $this->editFulfillment = $this->fulfillment;
        }

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function saveRequest(): void
    {
        $this->validate([
            'editTitle' => 'required|string|min:5|max:200',
            'editBody' => 'required|string|min:10|max:3000',
            'editBudget' => 'nullable|string|max:100',
            'editCategoryId' => 'nullable|exists:categories,id',
            'editBrandId' => 'nullable|exists:brands,id',
            'editModelId' => 'nullable|exists:device_models,id',
            'editUrgency' => 'nullable|string|max:50',
            'editFulfillment' => 'nullable|string|max:50',
        ]);

        if ($this->discussion && $this->isOwner) {
            $attachments = $this->discussion->attachments ?? [];
            $attachments['urgency'] = $this->editUrgency ?: 'Flexible';
            $attachments['fulfillment'] = $this->editFulfillment ?: 'Flexible';
            if ($this->editBudget) {
                $attachments['budget'] = $this->editBudget;
            }

            $this->discussion->update([
                'title' => $this->editTitle,
                'body' => $this->editBody,
                'budget' => $this->editBudget,
                'category_id' => $this->editCategoryId,
                'brand_id' => $this->editBrandId,
                'model_id' => $this->editModelId,
                'attachments' => $attachments,
            ]);

            // Cache latest edit timestamp for debouncing
            $timestamp = now()->timestamp;
            Cache::put(
                "discussion_edit_timestamp_{$this->discussion->id}",
                $timestamp,
                now()->addMinutes(30)
            );

            // Dispatch notification job with 5-minute debounce delay
            NotifyDiscussionEditedJob::dispatch(
                $this->discussion->id,
                $timestamp
            )->delay(now()->addMinutes(5));

            $this->showEditModal = false;
            session()->flash('message', 'Request updated successfully! All responders and watchers will be notified.');
            $this->loadRequestData();
        } else {
            // Demo mode fallback
            $this->title = $this->editTitle;
            $this->body = $this->editBody;
            $this->budget = $this->editBudget;
            $this->urgency = $this->editUrgency;
            $this->fulfillment = $this->editFulfillment;
            $this->showEditModal = false;
            session()->flash('message', 'Request updated successfully in preview mode.');
        }
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

    public function openQuickViewOffer(int $responseId): void
    {
        $this->dispatch('open-quick-view-offer', response_id: $responseId);
    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $deviceModels = $this->editBrandId
            ? DeviceModel::where('brand_id', $this->editBrandId)->orderBy('name')->get()
            : collect();

        return view('livewire.dashboard.requests.my-request-view', [
            'categories' => $categories,
            'brands' => $brands,
            'deviceModels' => $deviceModels,
        ]);
    }
}
