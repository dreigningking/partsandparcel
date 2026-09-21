<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmPaymentJob;
use App\Models\Payout;
use App\Services\Payment\FlutterwaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlutterwaveWebhookController extends Controller
{
    public function __construct(
        protected FlutterwaveService $flutterwaveService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        if (! $this->flutterwaveService->verifyWebhookSignature($request)) {
            Log::warning('FlutterwaveWebhookController: Invalid webhook signature received.');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? ($payload['event.type'] ?? '');
        $data = $payload['data'] ?? [];

        Log::info("FlutterwaveWebhookController: Event received: {$event}", [
            'tx_ref' => $data['tx_ref'] ?? null,
            'id' => $data['id'] ?? null,
        ]);

        switch ($event) {
            case 'charge.completed':
                $txRef = $data['tx_ref'] ?? null;
                $status = strtolower($data['status'] ?? '');
                if ($txRef && $status === 'successful') {
                    ConfirmPaymentJob::dispatch($txRef, 'flutterwave', $data);
                }
                break;

            case 'transfer.completed':
                $reference = $data['reference'] ?? null;
                $status = strtoupper($data['status'] ?? '');
                if ($reference) {
                    $payout = Payout::with('settlements')->where('reference', $reference)->first();
                    if ($payout) {
                        if ($status === 'SUCCESSFUL') {
                            DB::transaction(function () use ($payout, $data) {
                                $payout->update([
                                    'status' => 'successful',
                                    'paid_at' => now(),
                                    'metadata' => array_merge($payout->metadata ?? [], ['webhook' => $data]),
                                ]);

                                foreach ($payout->settlements as $settlement) {
                                    $settlement->update([
                                        'status' => 'settled',
                                        'settled_at' => now(),
                                    ]);
                                }
                            });
                            Log::info("FlutterwaveWebhookController: Payout {$reference} marked successful.");
                        } else {
                            $payout->update([
                                'status' => 'failed',
                                'metadata' => array_merge($payout->metadata ?? [], ['failed_webhook' => $data]),
                            ]);
                            Log::warning("FlutterwaveWebhookController: Payout {$reference} marked failed.");
                        }
                    }
                }
                break;

            default:
                Log::info("FlutterwaveWebhookController: Unhandled event {$event}");
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }
}
