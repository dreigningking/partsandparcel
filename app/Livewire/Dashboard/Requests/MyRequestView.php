<?php

namespace App\Livewire\Dashboard\Requests;

use App\Models\Discussion;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyRequestView extends Component
{
    public $requestId = 'REQ-8402';
    public $discussion = null;
    public $offers = [];

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
            $d = Discussion::with(['user', 'category', 'responses.user.primaryLocation', 'responses.offers.sender.primaryLocation', 'responses.offers.items'])
                ->find($cleanId);

            if ($d) {
                $this->discussion = $d;

                $loadedOffers = [];
                // Direct discussion offers + response offers
                $allOffers = Offer::with(['sender.primaryLocation', 'items'])
                    ->where('discussion_id', $d->id)
                    ->orWhereIn('response_id', $d->responses->pluck('id'))
                    ->latest()
                    ->get();

                foreach ($allOffers as $off) {
                    $loadedOffers[] = [
                        'id' => $off->id,
                        'vendor_name' => $off->sender?->business_name ?: $off->sender?->name ?: 'Vendor',
                        'vendor_city' => $off->sender?->primaryLocation?->city ?? 'Lagos',
                        'price' => $off->total(),
                        'warranty' => $off->maxWarrantyDays() ? "{$off->maxWarrantyDays()} DAYS" : 'Standard',
                        'delivery' => $off->delivery_method === 'seller_responsible' ? 'Seller Delivery' : 'Buyer Pickup',
                        'status' => $off->status,
                        'time' => $off->created_at->diffForHumans(),
                    ];
                }

                $this->offers = $loadedOffers;
                return;
            }
        }

        // Demo sample fallback
        $this->offers = [
            [
                'id' => 9021,
                'vendor_name' => 'Abel Electronics',
                'vendor_city' => 'Computer Village, Ikeja',
                'price' => 80000,
                'warranty' => '14 DAYS',
                'delivery' => 'Buyer Pickup (Computer Village)',
                'status' => 'pending',
                'time' => '2 hours ago',
            ],
            [
                'id' => 9022,
                'vendor_name' => 'Seth Tech Hub',
                'vendor_city' => 'Oregun, Ikeja',
                'price' => 82000,
                'warranty' => '30 DAYS',
                'delivery' => 'Seller Free Delivery Included',
                'status' => 'pending',
                'time' => '1 hour ago',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.requests.my-request-view');
    }
}
