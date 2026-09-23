<?php

namespace App\Livewire\Marketplace\Community;

use App\Models\Discussion;
use App\Models\Offer;
use App\Models\Response;
use App\Services\Commercial\NegotiationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CommunityRequest extends Component
{
    public $discussionId = null;
    public $discussion = null;
    public $isOwner = true; // Set to true for request owner view, false for public viewers

    public $responseText = '';
    public $composerOfferPrice = '';
    public $composerOfferWarranty = '14 days';
    public $composerOfferDelivery = 'Buyer pickup';
    public $composerOfferMessage = '';
    public $isOfferActive = false;

    // Report modal inputs
    public $reportReason = 'spam';
    public $reportDetails = '';

    public $responses = [];

    public function mount($id = null)
    {
        $this->discussionId = $id;
        $this->loadDiscussionAndResponses();
    }

    public function loadDiscussionAndResponses()
    {
        $user = Auth::user();

        if ($this->discussionId && is_numeric($this->discussionId)) {
            $dbDiscussion = Discussion::with(['user.primaryLocation', 'category', 'responses.user.primaryLocation', 'responses.offers.items'])
                ->find($this->discussionId);

            if ($dbDiscussion) {
                $this->discussion = $dbDiscussion;
                $this->isOwner = $user && ($user->id === $dbDiscussion->user_id);

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
                        'author' => $r->user?->name ?? 'Vendor',
                        'verified' => (bool) $r->user?->is_verified,
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

        // Demo sample responses fallback
        $this->responses = [
            [
                'id' => 1,
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
        $this->isOfferActive = !$this->isOfferActive;
    }

    public function submitResponse()
    {
        if (trim($this->responseText) === '') return;

        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please log in to submit a response.');
            return redirect()->route('login');
        }

        // Subscription dynamic quota check
        $subscriptionService = app(\App\Services\Commercial\SubscriptionService::class);
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

            if ($this->isOfferActive && $this->composerOfferPrice) {
                $price = (float) str_replace(',', '', $this->composerOfferPrice);
                $warrantyDays = match ($this->composerOfferWarranty) {
                    '7 days' => 7,
                    '30 days' => 30,
                    default => 14,
                };

                $offer = app(NegotiationService::class)->createOfferFromResponse($user, $this->discussion->id, $response->id, [
                    'price' => $price,
                    'warranty_days' => $warrantyDays,
                    'delivery_method' => $this->composerOfferDelivery,
                    'message' => $this->composerOfferMessage ?: $this->responseText,
                ]);

                // Notify discussion owner of new offer
                if ($this->discussion->user && $this->discussion->user_id !== $user->id) {
                    $this->discussion->user->notify(new \App\Notifications\NewOfferNotification($offer));
                }
            }

            $this->loadDiscussionAndResponses();
        } else {
            // Memory mock for demo
            $newResponse = [
                'id' => count($this->responses) + 1,
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

    public function openVendorChat($vendorName)
    {
        $id = strtolower(strtok($vendorName, ' '));
        $this->dispatch('open-conversation', id: $id);
    }

    public function render()
    {
        return view('livewire.marketplace.community.community-request');
    }
}