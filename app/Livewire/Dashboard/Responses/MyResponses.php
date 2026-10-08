<?php

namespace App\Livewire\Dashboard\Responses;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Response;
use App\Notifications\NewOfferNotification;
use App\Services\Commercial\NegotiationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dash')]
class MyResponses extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'cat')]
    public string $categoryFilter = 'all';

    #[Url(as: 'type')]
    public string $typeFilter = 'all'; // 'all', 'item', 'service', 'advice'

    #[Url(as: 'req_status')]
    public string $requestStatusFilter = 'all'; // 'all', 'open', 'fulfilled', 'closed'

    #[Url(as: 'offer_status')]
    public string $offerStatusFilter = 'all'; // 'all', 'pending', 'countered', 'accepted', 'declined', 'cancelled', 'none'

    #[Url(as: 'sort')]
    public string $sortBy = 'latest'; // 'latest', 'oldest', 'most_offers'

    // Edit Response & Attach Offer Modal State
    public bool $showEditModal = false;
    public ?int $editingResponseId = null;
    public ?Response $editingResponse = null;

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

    public function updatingRequestStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOfferStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->typeFilter = 'all';
        $this->requestStatusFilter = 'all';
        $this->offerStatusFilter = 'all';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function openEditModal(int $responseId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $resp = Response::with(['discussion', 'offers'])->where('user_id', $user->id)->find($responseId);
        if (! $resp) {
            session()->flash('warning', 'Response not found.');
            return;
        }

        if (($resp->discussion?->status ?? 'open') === 'closed') {
            session()->flash('warning', 'Cannot edit response: The community request is closed.');
            return;
        }

        if ($resp->offers->isNotEmpty()) {
            session()->flash('warning', 'Cannot edit response: A commercial offer is already attached.');
            return;
        }

        $this->editingResponseId = $resp->id;
        $this->editingResponse = $resp;
        $this->responseText = $resp->body;

        // Reset offer composer fields
        $this->isOfferActive = false;
        $this->composerListingId = null;
        $this->composerItemDescription = '';
        $this->composerItemPrice = '';
        $this->composerItemWarranty = 14;
        $this->composerIncludeService = false;
        $this->composerServiceDescription = '';
        $this->composerServicePrice = '';
        $this->composerServiceWarranty = 14;
        $this->composerIncludePickup = false;
        $this->composerPickupFee = '';
        $this->composerIncludeDelivery = false;
        $this->composerDeliveryFee = '';
        $this->composerOfferDelivery = 'flexible';
        $this->composerOfferMessage = '';

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingResponseId = null;
        $this->editingResponse = null;
    }

    public function toggleOfferComposer(): void
    {
        $this->isOfferActive = ! $this->isOfferActive;
    }

    public function updatedComposerListingId($value): void
    {
        if ($value) {
            $listing = Listing::find($value);
            if ($listing) {
                $this->composerItemDescription = $listing->title;
                $this->composerItemPrice = (string) $listing->price;
                $this->composerItemWarranty = $listing->warranty_period_days ?? 14;
            }
        }
    }

    public function getMyListingsProperty()
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }

        return Listing::where('user_id', $user->id)
            ->where('is_published', true)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
    }

    public function saveEditedResponse(): void
    {
        $this->validate([
            'responseText' => ['required', 'string', 'min:2'],
            'composerItemDescription' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        if (! $user || ! $this->editingResponseId) {
            return;
        }

        $resp = Response::with('discussion')->where('id', $this->editingResponseId)->where('user_id', $user->id)->first();
        if (! $resp) {
            session()->flash('warning', 'Response not found.');
            return;
        }

        if (($resp->discussion?->status ?? 'open') === 'closed') {
            session()->flash('warning', 'Cannot edit response: The community request is closed.');
            return;
        }

        if ($resp->offers()->count() > 0) {
            session()->flash('warning', 'Cannot edit response: A commercial offer is already attached.');
            return;
        }

        // Update response text
        $resp->update([
            'body' => $this->responseText,
        ]);

        // Attach Commercial Offer if active
        if ($this->isOfferActive) {
            $offerItems = [];

            // 1. Hardware Item
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

            // 2. Repair Service
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

            // 3. Pickup Shipment
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

            // 4. Delivery Shipment
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
                $offer = app(NegotiationService::class)->createOfferFromResponse($user, $resp->discussion_id, $resp->id, [
                    'items' => $offerItems,
                    'price' => $totalProposed,
                    'delivery_method' => $this->composerOfferDelivery,
                    'message' => $this->composerOfferMessage ?: $this->responseText,
                ]);

                // Notify discussion author
                if ($resp->discussion?->user && $resp->discussion->user_id !== $user->id) {
                    try {
                        $resp->discussion->user->notify(new NewOfferNotification($offer));
                    } catch (\Throwable $e) {}
                }
            }
        }

        $this->closeEditModal();
        session()->flash('message', 'Your response has been updated' . ($this->isOfferActive ? ' and commercial offer attached successfully!' : ' successfully.'));
    }

    public function render()
    {
        $user = Auth::user();
        $userResponses = collect();
        $totalCount = 0;
        $categories = collect();

        if ($user) {
            $base = Response::where('user_id', $user->id);
            $totalCount = (clone $base)->count();

            $categories = Category::whereHas('discussions.responses', fn ($r) => $r->where('user_id', $user->id))
                ->orderBy('name')
                ->get();

            if ($categories->isEmpty()) {
                $categories = Category::whereHas('discussions')->orderBy('name')->get();
            }

            $query = Response::with([
                'discussion.user.primaryLocation.state',
                'discussion.category',
                'discussion.brand',
                'discussion.deviceModel',
                'discussion.location.state',
                'discussion.offers.items',
                'offers.items',
            ])->where('user_id', $user->id);

            // Request Status Filter
            if ($this->requestStatusFilter === 'open') {
                $query->whereHas('discussion', fn ($d) => $d->where('status', 'open'));
            } elseif ($this->requestStatusFilter === 'fulfilled') {
                $query->whereHas('discussion', fn ($d) => $d->whereIn('status', ['resolved', 'fulfilled']));
            } elseif ($this->requestStatusFilter === 'closed') {
                $query->whereHas('discussion', fn ($d) => $d->where('status', 'closed'));
            }

            // Discussion Type Filter
            if (in_array($this->typeFilter, ['item', 'service', 'advice'])) {
                $query->whereHas('discussion', fn ($d) => $d->where('type', $this->typeFilter));
            }

            // Category Filter
            if ($this->categoryFilter !== 'all' && is_numeric($this->categoryFilter)) {
                $query->whereHas('discussion', fn ($d) => $d->where('category_id', (int) $this->categoryFilter));
            }

            // Offer Status Filter
            if ($this->offerStatusFilter === 'pending') {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('offers', fn ($o) => $o->where('status', 'pending'))
                      ->orWhereHas('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'pending'));
                });
            } elseif ($this->offerStatusFilter === 'countered') {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('offers', fn ($o) => $o->where('status', 'countered'))
                      ->orWhereHas('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'countered'));
                });
            } elseif ($this->offerStatusFilter === 'accepted') {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('offers', fn ($o) => $o->where('status', 'accepted'))
                      ->orWhereHas('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'accepted'));
                });
            } elseif ($this->offerStatusFilter === 'declined') {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('offers', fn ($o) => $o->where('status', 'declined'))
                      ->orWhereHas('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->where('status', 'declined'));
                });
            } elseif ($this->offerStatusFilter === 'cancelled') {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('offers', fn ($o) => $o->whereIn('status', ['cancelled', 'expired']))
                      ->orWhereHas('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))->whereIn('status', ['cancelled', 'expired']));
                });
            } elseif ($this->offerStatusFilter === 'none') {
                $query->whereDoesntHave('offers')
                    ->whereDoesntHave('discussion.offers', fn ($o) => $o->where(fn ($sub) => $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id)));
            }

            // Search (in request or response)
            if (!empty(trim($this->search))) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function (Builder $q) use ($term) {
                    $q->where('body', 'like', $term)
                      ->orWhereHas('discussion', function (Builder $d) use ($term) {
                          $d->where('title', 'like', $term)
                            ->orWhere('body', 'like', $term)
                            ->orWhere('budget', 'like', $term)
                            ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $term))
                            ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $term))
                            ->orWhereHas('deviceModel', fn ($m) => $m->where('name', 'like', $term));
                      });
                });
            }

            // Sort
            if ($this->sortBy === 'oldest') {
                $query->oldest('id');
            } elseif ($this->sortBy === 'most_offers') {
                $query->withCount('offers')->orderByDesc('offers_count')->latest('id');
            } else {
                $query->latest('id');
            }

            $userResponses = $query->paginate(10);
        }

        return view('livewire.dashboard.responses.my-responses', [
            'userResponses' => $userResponses,
            'totalCount' => $totalCount,
            'categories' => $categories,
        ]);
    }
}
