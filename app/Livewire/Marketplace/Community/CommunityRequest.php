<?php

namespace App\Livewire\Marketplace\Community;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Location;
use App\Models\Offer;
use App\Models\Report;
use App\Models\Response;
use App\Models\Watchlist;
use App\Notifications\DiscussionResponseNotification;
use App\Notifications\NewOfferNotification;
use App\Services\Commercial\NegotiationService;
use App\Services\Commercial\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class CommunityRequest extends Component
{
    public $discussionId = null;
    public $discussion = null;
    public bool $isOwner = false;

    // Response Composer
    public string $responseText = '';
    public bool $isOfferActive = false;
    public ?int $composerListingId = null;
    public string $composerItemDescription = '';
    public string $composerItemPrice = '';
    public int $composerItemWarranty = 14;

    public bool $composerIncludeService = false;
    public string $composerServiceDescription = '';
    public string $composerServicePrice = '';
    public int $composerServiceWarranty = 14;

    public bool $composerIncludePickup = false;
    public string $composerPickupFee = '';

    public bool $composerIncludeDelivery = false;
    public string $composerDeliveryFee = '';

    public string $composerOfferDelivery = 'flexible';
    public string $composerOfferMessage = '';

    // Subscription quotas
    public int $dailyResponsesRemaining = 3;
    public int $dailyResponseLimit = 3;

    // Watchlist State
    public bool $isWatched = false;
    public int $watchersCount = 0;

    // Reporting Status
    public bool $isDiscussionReported = false;
    public array $reportedResponseIds = [];

    // Edit Discussion Modal State
    public bool $showEditModal = false;
    public string $editTitle = '';
    public string $editBody = '';
    public string $editBudget = '';
    public ?int $editCategoryId = null;
    public ?int $editBrandId = null;
    public ?int $editModelId = null;
    public ?int $editLocationId = null;
    public string $editFulfillment = 'flexible';
    public string $editUrgency = 'standard';
    public string $editStatus = 'open';

    public $responses = [];
    public $mediaItems = [];

    public function mount($id = null)
    {
        $this->discussionId = $id;
        $this->loadDiscussionAndResponses();
    }

    public function loadDiscussionAndResponses()
    {
        $user = Auth::user();

        // Calculate dynamic response quota if authenticated
        if ($user) {
            $subscriptionService = app(SubscriptionService::class);
            $usage = $subscriptionService->getUsageStats($user);
            $this->dailyResponsesRemaining = $usage['daily_responses_remaining'];
            $this->dailyResponseLimit = $usage['daily_response_limit'];
        }

        if ($this->discussionId && is_numeric($this->discussionId)) {
            $dbDiscussion = Discussion::with([
                'user.primaryLocation',
                'category',
                'brand',
                'deviceModel',
                'location',
                'media',
                'watchlists',
                'responses.user.primaryLocation',
                'responses.offers.items'
            ])->find($this->discussionId);

            if ($dbDiscussion) {
                $this->discussion = $dbDiscussion;
                $this->isOwner = $user && ($user->id === $dbDiscussion->user_id);

                // Increment view counter in attachments
                $views = ($dbDiscussion->attachments['views'] ?? 15) + 1;
                $attachments = $dbDiscussion->attachments ?? [];
                $attachments['views'] = $views;
                $dbDiscussion->update(['attachments' => $attachments]);

                // Watchlist state
                $this->watchersCount = $dbDiscussion->watchlists()->count();
                $this->isWatched = $user ? $dbDiscussion->isWatchedBy($user) : false;

                // Reporting state
                if ($user) {
                    $this->isDiscussionReported = Report::where('user_id', $user->id)
                        ->where('reportable_type', Discussion::class)
                        ->where('reportable_id', $dbDiscussion->id)
                        ->exists();

                    $respIds = $dbDiscussion->responses->pluck('id')->toArray();
                    $this->reportedResponseIds = Report::where('user_id', $user->id)
                        ->where('reportable_type', Response::class)
                        ->whereIn('reportable_id', $respIds)
                        ->pluck('reportable_id')
                        ->toArray();
                }

                // Map dynamic media items
                $loadedMedia = [];
                foreach ($dbDiscussion->media as $m) {
                    $badge = match ($m->media_type) {
                        'video' => 'Video',
                        'document' => 'PDF Doc',
                        default => 'Photo',
                    };
                    $sizeFormatted = $m->size ? number_format($m->size / (1024 * 1024), 1) . ' MB' : '';
                    $loadedMedia[] = [
                        'id' => $m->id,
                        'type' => $m->media_type === 'document' ? 'pdf' : $m->media_type,
                        'title' => $m->name ?: $m->file_name,
                        'src' => $m->url,
                        'size' => $sizeFormatted,
                        'badge' => $badge,
                    ];
                }
                $this->mediaItems = $loadedMedia;

                $loadedResponses = [];
                foreach ($dbDiscussion->responses as $r) {
                    $isParticipant = $user && ($user->id === $dbDiscussion->user_id || $user->id === $r->user_id);
                    $negotiation = [];

                    if ($isParticipant) {
                        $offers = Offer::with('items')->where('response_id', $r->id)->latest()->get();
                        foreach ($offers as $off) {
                            $negotiation[] = [
                                'id' => $off->id,
                                'from' => $off->sender?->name ?? 'Vendor',
                                'to' => $off->recipient?->name ?? 'Buyer',
                                'price' => '₦' . number_format($off->total()),
                                'warranty' => $off->maxWarrantyDays() ? "{$off->maxWarrantyDays()}-day warranty" : 'No warranty',
                                'delivery' => $off->delivery_method === 'seller_responsible' ? 'Seller delivery' : 'Buyer pickup',
                                'message' => $off->terms ?? '',
                                'time' => $off->created_at->diffForHumans(),
                                'status' => $off->status,
                                'edited' => false,
                            ];
                        }
                    }

                    $loadedResponses[] = [
                        'id' => $r->id,
                        'user_id' => $r->user_id,
                        'author' => $r->user?->business_name ?: $r->user?->name ?: 'Vendor',
                        'verified' => (bool) ($r->user?->is_verified ?? false),
                        'location' => $r->user?->primaryLocation?->city ? "{$r->user->primaryLocation->city}, {$r->user->primaryLocation->state}" : 'Lagos, Nigeria',
                        'time' => $r->created_at->diffForHumans(),
                        'text' => $r->body,
                        'negotiation' => $negotiation,
                    ];
                }

                $this->responses = $loadedResponses;
                return;
            }
        }

        // Demo fallback media items for non-existent IDs
        $this->mediaItems = [
            ['type' => 'image', 'title' => 'Motherboard Front (Clean Pull)', 'src' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80', 'badge' => 'Photo'],
            ['type' => 'image', 'title' => 'Motherboard Back & Serial Tag', 'src' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=800&q=80', 'badge' => 'Photo'],
            ['type' => 'video', 'title' => 'Power Boot Test Video (0:15)', 'src' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'badge' => 'Video'],
            ['type' => 'pdf', 'title' => 'Diagnostic Report & Specs.pdf', 'src' => '#', 'size' => '1.4 MB', 'badge' => 'PDF Doc'],
        ];

        // Demo sample responses fallback
        $this->responses = [
            [
                'id' => 1,
                'user_id' => 9991,
                'author' => 'Abel Electronics',
                'verified' => true,
                'location' => 'Computer Village, Ikeja',
                'time' => '2 hours ago',
                'text' => "I have the motherboard you're looking for. It's from an EliteBook 840 G5 with a working motherboard. You can test it before buying.",
                'negotiation' => [
                    [
                        'id' => 101,
                        'from' => 'Abel Electronics',
                        'to' => 'TechSam',
                        'price' => '₦85,000',
                        'warranty' => '14-day warranty',
                        'delivery' => 'Buyer pickup',
                        'message' => 'Original motherboard, clean condition. Tested working.',
                        'time' => '2 hours ago',
                        'status' => 'declined',
                        'edited' => false
                    ],
                    [
                        'id' => 105,
                        'from' => 'Abel Electronics',
                        'to' => 'TechSam',
                        'price' => '₦80,000',
                        'warranty' => '14-day warranty',
                        'delivery' => 'Buyer pickup',
                        'message' => '₦80k final offer brother. Clean board with 14-day replacement warranty.',
                        'time' => '15 mins ago',
                        'status' => 'pending',
                        'edited' => false
                    ]
                ]
            ],
            [
                'id' => 2,
                'user_id' => 9992,
                'author' => 'Seth Tech Hub',
                'verified' => true,
                'location' => 'Oregun, Ikeja',
                'time' => '1 hour ago',
                'text' => "We have 2 units of HP 840 G5 motherboards available at our shop. Core i5 8th Gen, tested with 30 days warranty.",
                'negotiation' => [
                    [
                        'id' => 201,
                        'from' => 'Seth Tech Hub',
                        'to' => 'TechSam',
                        'price' => '₦90,000',
                        'warranty' => '30-day warranty',
                        'delivery' => 'Seller delivery',
                        'message' => 'Grade A tested motherboard with 30 days warranty. Free delivery in Ikeja.',
                        'time' => '1 hour ago',
                        'status' => 'declined',
                        'edited' => false
                    ]
                ]
            ]
        ];
    }

    public function toggleOfferComposer()
    {
        $this->isOfferActive = ! $this->isOfferActive;
    }

    public function toggleWatch()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to watch this discussion.');
            return redirect()->route('login');
        }

        if (! $this->discussion) {
            $this->isWatched = ! $this->isWatched;
            $this->watchersCount += $this->isWatched ? 1 : -1;
            return;
        }

        $watch = $this->discussion->watchlists()->where('user_id', $user->id)->first();

        if ($watch) {
            $watch->delete();
            $this->isWatched = false;
            $this->watchersCount = max(0, $this->watchersCount - 1);
            session()->flash('message', 'Discussion removed from your watchlist.');
        } else {
            $this->discussion->watchlists()->create([
                'user_id' => $user->id,
            ]);
            $this->isWatched = true;
            $this->watchersCount++;
            session()->flash('message', 'You are now watching this discussion. You will receive notifications when new responses are posted.');
        }
    }

    public function openEditModal()
    {
        if (! $this->discussion || ! $this->isOwner) {
            return;
        }

        $this->editTitle = $this->discussion->title;
        $this->editBody = $this->discussion->body;
        $this->editBudget = (string) ($this->discussion->budget ?? '');
        $this->editCategoryId = $this->discussion->category_id;
        $this->editBrandId = $this->discussion->brand_id;
        $this->editModelId = $this->discussion->model_id;
        $this->editLocationId = $this->discussion->location_id;
        $this->editFulfillment = $this->discussion->attachments['fulfillment_raw'] ?? 'flexible';
        $this->editUrgency = $this->discussion->attachments['urgency_raw'] ?? 'standard';
        $this->editStatus = $this->discussion->status ?? 'open';

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function updateDiscussion()
    {
        $user = Auth::user();
        if (! $user || ! $this->discussion || $this->discussion->user_id !== $user->id) {
            session()->flash('warning', 'Unauthorized: Only the discussion author can edit this request.');
            return;
        }

        $this->validate([
            'editTitle' => ['required', 'string', 'min:5', 'max:180'],
            'editBody' => ['required', 'string', 'min:10'],
        ]);

        $fulfillmentLabel = match ($this->editFulfillment) {
            'buyer_pickup' => 'Buyer pickup',
            'seller_delivery' => 'Seller delivery',
            'shop_pickup' => 'Pickup in Shop',
            default => 'Pickup / Delivery',
        };

        $urgencyLabel = match ($this->editUrgency) {
            'urgent' => 'Urgent (Today)',
            'within_48h' => 'Within 24–48 hours',
            'this_week' => 'This week',
            default => 'Flexible',
        };

        $attachments = $this->discussion->attachments ?? [];
        $attachments['budget'] = $this->editBudget ?: 'Flexible';
        $attachments['fulfillment'] = $fulfillmentLabel;
        $attachments['fulfillment_raw'] = $this->editFulfillment;
        $attachments['urgency'] = $urgencyLabel;
        $attachments['urgency_raw'] = $this->editUrgency;

        $this->discussion->update([
            'title' => $this->editTitle,
            'body' => $this->editBody,
            'budget' => $this->editBudget ?: null,
            'category_id' => $this->editCategoryId,
            'brand_id' => $this->editBrandId,
            'model_id' => $this->editModelId,
            'location_id' => $this->editLocationId,
            'status' => $this->editStatus,
            'attachments' => $attachments,
        ]);

        $this->showEditModal = false;
        $this->loadDiscussionAndResponses();
        session()->flash('message', 'Your request has been updated successfully!');
    }

    public function updatedComposerListingId($value)
    {
        if ($value) {
            $listing = \App\Models\Listing::find($value);
            if ($listing) {
                $this->composerItemDescription = $listing->title;
                $this->composerItemPrice = (string) $listing->price;
                $this->composerItemWarranty = $listing->warranty_days ?? 14;
            }
        }
    }

    public function getMyListingsProperty()
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }
        return \App\Models\Listing::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();
    }

    public function submitResponse()
    {
        if (trim($this->responseText) === '') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please log in to submit a response.');
            return redirect()->route('login');
        }

        // The discussion author cannot respond to his own discussion
        if ($this->discussion && $this->discussion->user_id === $user->id) {
            session()->flash('warning', 'As the author of this request, you cannot reply to your own discussion. You can edit your request above.');
            return;
        }

        // Subscription dynamic quota check
        $subscriptionService = app(SubscriptionService::class);
        $usage = $subscriptionService->getUsageStats($user);

        if (! $usage['can_respond']) {
            session()->flash('warning', "You have reached your daily response quota ({$usage['daily_responses_used']}/{$usage['daily_response_limit']} for {$usage['plan_name']}). Please upgrade your subscription for higher daily limits or try again tomorrow.");
            return;
        }

        // Database persist if real discussion exists
        if ($this->discussion) {
            $response = Response::create([
                'discussion_id' => $this->discussion->id,
                'user_id' => $user->id,
                'body' => $this->responseText,
                'status' => 'visible',
            ]);

            // Create Offer if option selected
            if ($this->isOfferActive) {
                $offerItems = [];

                // 1. Physical Item or Component
                $itemPrice = (float) str_replace(',', '', $this->composerItemPrice);
                if (! empty($this->composerItemDescription) || $this->composerListingId) {
                    $offerItems[] = [
                        'listing_id' => $this->composerListingId ?: null,
                        'description' => $this->composerItemDescription ?: 'Offered Component / Item',
                        'type' => 'item',
                        'quantity' => 1,
                        'unit_price' => $itemPrice,
                        'warranty_period_days' => (int) $this->composerItemWarranty,
                        'warranty_terms' => "{$this->composerItemWarranty}-day hardware testing warranty",
                    ];
                }

                // 2. Workmanship / Repair Service
                if ($this->composerIncludeService && ! empty($this->composerServiceDescription)) {
                    $servicePrice = (float) str_replace(',', '', $this->composerServicePrice);
                    $offerItems[] = [
                        'listing_id' => null,
                        'description' => $this->composerServiceDescription,
                        'type' => 'service',
                        'quantity' => 1,
                        'unit_price' => $servicePrice,
                        'warranty_period_days' => (int) $this->composerServiceWarranty,
                        'warranty_terms' => "{$this->composerServiceWarranty}-day workmanship warranty",
                    ];
                }

                // 3. Pickup Shipment (Buyer to Seller / Technician)
                if ($this->composerIncludePickup && (float) str_replace(',', '', $this->composerPickupFee) > 0) {
                    $offerItems[] = [
                        'listing_id' => null,
                        'description' => 'Pickup Courier Dispatch (Buyer to Technician)',
                        'type' => 'pickup',
                        'quantity' => 1,
                        'unit_price' => (float) str_replace(',', '', $this->composerPickupFee),
                        'warranty_period_days' => null,
                        'warranty_terms' => null,
                    ];
                }

                // 4. Delivery Shipment (Seller / Technician to Buyer)
                if ($this->composerIncludeDelivery && (float) str_replace(',', '', $this->composerDeliveryFee) > 0) {
                    $offerItems[] = [
                        'listing_id' => null,
                        'description' => 'Delivery Courier Dispatch (Technician to Buyer)',
                        'type' => 'delivery',
                        'quantity' => 1,
                        'unit_price' => (float) str_replace(',', '', $this->composerDeliveryFee),
                        'warranty_period_days' => null,
                        'warranty_terms' => null,
                    ];
                }

                if (! empty($offerItems)) {
                    $totalProposed = array_sum(array_column($offerItems, 'unit_price'));
                    $offer = app(NegotiationService::class)->createOfferFromResponse($user, $this->discussion->id, $response->id, [
                        'items' => $offerItems,
                        'price' => $totalProposed,
                        'delivery_method' => $this->composerOfferDelivery,
                        'message' => $this->composerOfferMessage ?: $this->responseText,
                    ]);

                    // Notify discussion author of new offer
                    if ($this->discussion->user && $this->discussion->user_id !== $user->id) {
                        try {
                            $this->discussion->user->notify(new NewOfferNotification($offer));
                        } catch (\Throwable $e) {}
                    }
                }
            }

            // Notify discussion watchers of new response
            $watchers = $this->discussion->watchlists()
                ->where('user_id', '!=', $user->id)
                ->with('user')
                ->get();

            foreach ($watchers as $w) {
                if ($w->user) {
                    try {
                        $w->user->notify(new DiscussionResponseNotification($this->discussion, $response));
                    } catch (\Throwable $e) {}
                }
            }

            // Also notify author if not already in watchers
            if ($this->discussion->user && $this->discussion->user_id !== $user->id) {
                $alreadyWatching = $watchers->contains('user_id', $this->discussion->user_id);
                if (! $alreadyWatching) {
                    try {
                        $this->discussion->user->notify(new DiscussionResponseNotification($this->discussion, $response));
                    } catch (\Throwable $e) {}
                }
            }

            $this->loadDiscussionAndResponses();
        } else {
            // Memory mock for demo
            $newResponse = [
                'id' => count($this->responses) + 1,
                'user_id' => $user->id,
                'author' => $user->name . ' (You)',
                'verified' => true,
                'location' => 'Computer Village, Ikeja',
                'time' => 'Just now',
                'text' => $this->responseText,
                'negotiation' => []
            ];

            if ($this->isOfferActive && $this->composerOfferPrice) {
                $newResponse['negotiation'][] = [
                    'id' => rand(500, 999),
                    'from' => $user->name . ' (You)',
                    'to' => 'TechSam',
                    'price' => '₦' . number_format((float) str_replace(',', '', $this->composerOfferPrice)),
                    'warranty' => $this->composerOfferWarranty,
                    'delivery' => $this->composerOfferDelivery,
                    'message' => $this->composerOfferMessage ?: $this->responseText,
                    'time' => 'Just now',
                    'status' => 'pending',
                    'edited' => false
                ];
            }

            array_unshift($this->responses, $newResponse);
        }

        $this->responseText = '';
        $this->composerOfferPrice = '';
        $this->composerOfferMessage = '';
        $this->isOfferActive = false;

        session()->flash('message', 'Your response has been posted successfully!');
    }

    public function openOffer($responseId)
    {
        $this->dispatch('open-quick-view-offer', response_id: $responseId);
    }

    public function openVendorConversation($responseId)
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to message this vendor.');
            return redirect()->route('login');
        }

        $response = Response::with('user')->find($responseId);
        $vendorId = $response?->user_id;

        if (! $vendorId && isset($this->responses)) {
            // Check memory mock
            $match = collect($this->responses)->firstWhere('id', $responseId);
            $vendorId = $match['user_id'] ?? null;
        }

        if (! $vendorId) {
            $this->dispatch('open-conversation', id: 'abel');
            return;
        }

        // Find or create Conversation between Discussion Author and Responder
        $discussionId = $this->discussion?->id;
        $conversation = Conversation::where('contextable_type', Discussion::class)
            ->where('contextable_id', $discussionId)
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $vendorId))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'contextable_type' => Discussion::class,
                'contextable_id' => $discussionId,
                'created_by' => $user->id,
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'joined_at' => now(),
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $vendorId,
                'joined_at' => now(),
            ]);
        }

        $this->dispatch('open-conversation', id: $conversation->id);
    }

    public function openReportModal(string $type, int $id)
    {
        $this->dispatch('open-report-modal', type: $type, id: $id);
    }

    #[On('report-submitted')]
    public function onReportSubmitted($payload = null)
    {
        if (is_array($payload)) {
            $type = $payload['type'] ?? '';
            $id = (int) ($payload['id'] ?? 0);
            if ($type === 'discussion' && $id === $this->discussion?->id) {
                $this->isDiscussionReported = true;
            } elseif ($type === 'response') {
                if (! in_array($id, $this->reportedResponseIds)) {
                    $this->reportedResponseIds[] = $id;
                }
            }
        }
    }

    public function getSimilarRequests()
    {
        if (! $this->discussion) {
            return Discussion::approved()->with(['location', 'category', 'media'])->latest()->take(3)->get();
        }

        $similar = Discussion::approved()
            ->where('id', '!=', $this->discussion->id)
            ->where(function ($q) {
                if ($this->discussion->category_id) {
                    $q->where('category_id', $this->discussion->category_id);
                }
                if ($this->discussion->brand_id) {
                    $q->orWhere('brand_id', $this->discussion->brand_id);
                }
            })
            ->latest()
            ->take(3)
            ->get();

        if ($similar->count() < 3) {
            $more = Discussion::approved()
                ->where('id', '!=', $this->discussion->id)
                ->whereNotIn('id', $similar->pluck('id'))
                ->latest()
                ->take(3 - $similar->count())
                ->get();
            $similar = $similar->concat($more);
        }

        return $similar;
    }

    public function render()
    {
        $allCategories = Category::orderBy('name')->get();
        $allBrands = Brand::orderBy('name')->get();
        $allModels = $this->editCategoryId
            ? DeviceModel::where('category_id', $this->editCategoryId)->orderBy('name')->get()
            : DeviceModel::orderBy('name')->take(100)->get();

        $allLocations = Auth::check()
            ? Location::where('user_id', Auth::id())->orderBy('is_primary', 'desc')->get()
            : Location::orderBy('city')->get();

        return view('livewire.marketplace.community.community-request', [
            'similarRequests' => $this->getSimilarRequests(),
            'mediaItems' => $this->mediaItems,
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'allModels' => $allModels,
            'allLocations' => $allLocations,
        ]);
    }
}