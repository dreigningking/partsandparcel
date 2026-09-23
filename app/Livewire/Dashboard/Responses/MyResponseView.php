<?php

namespace App\Livewire\Dashboard\Responses;

use App\Models\Offer;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MyResponseView extends Component
{
    public $responseId = 'RESP-1092';
    public $response = null;
    public $activeOffer = null;

    public function mount($id = null)
    {
        $idParam = $id ?? request()->route('id') ?? request()->query('id', 'RESP-1092');
        $this->responseId = (string) $idParam;
        $this->loadResponseData();
    }

    public function loadResponseData()
    {
        $cleanId = preg_replace('/[^0-9]/', '', $this->responseId);
        if ($cleanId && is_numeric($cleanId)) {
            $r = Response::with(['discussion.user.primaryLocation', 'offers.items', 'user'])
                ->find($cleanId);

            if ($r) {
                $this->response = $r;
                $this->activeOffer = $r->offers->last();
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.responses.my-response-view');
    }
}
