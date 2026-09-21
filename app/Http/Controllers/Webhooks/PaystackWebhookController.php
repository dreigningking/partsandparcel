<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmPaymentJob;
use App\Models\Payout;
use App\Services\Payment\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        protected PaystackService $paystackService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        if (! $this->paystackService->verifyWebhookSignature($request)) {
            Log::warning('PaystackWebhookController: Invalid webhook signature received.');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        Log::info("PaystackWebhookController: Event received: {$event}", [
            'reference' => $data['reference'] ?? null,
        ]);

        switch ($event) {
            case 'charge.success':
                $reference = $data['reference'] ?? null;
                if ($reference) {
                    ConfirmPaymentJob::dispatch($reference, 'paystack', $data);
                }
                break;

            case 'transfer.success':
                $reference = $data['reference'] ?? null;
                if ($reference) {
                    $payout = Payout::with('settlements')->where('reference', $reference)->first();
                    if ($payout) {
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
                        Log::info("PaystackWebhookController: Payout {$reference} marked successful.");
                    }
                }
                break;

            case 'transfer.failed':
            case 'transfer.reversed':
                $reference = $data['reference'] ?? null;
                if ($reference) {
                    Payout::where('reference', $reference)->update([
                        'status' => 'failed',
                        'metadata' => DB::raw("json_set(coalesce(metadata, '{}'), '$.failure_webhook', '" . json_encode($data) . "')"),
                    ]);
                    Log::warning("PaystackWebhookController: Payout {$reference} failed/reversed.");
                }
                break;

            default:
                Log::info("PaystackWebhookController: Unhandled event {$event}");
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }
}
