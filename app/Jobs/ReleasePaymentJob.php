<?php

namespace App\Jobs;

use App\Models\Settlement;
use App\Services\Payment\EscrowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReleasePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public ?int $settlementId = null
    ) {}

    public function handle(EscrowService $escrowService): void
    {
        if ($this->settlementId) {
            $settlement = Settlement::find($this->settlementId);

            if (! $settlement) {
                Log::warning("ReleasePaymentJob: Settlement ID {$this->settlementId} not found.");
                return;
            }

            $released = $escrowService->releaseSettlement($settlement);
            if ($released) {
                Log::info("ReleasePaymentJob: Settlement #{$settlement->id} released successfully.");
            } else {
                Log::info("ReleasePaymentJob: Settlement #{$settlement->id} was not eligible for release.");
            }

            return;
        }

        // Batch scan all settlements ready for release
        $settlements = Settlement::where('status', 'pending')
            ->whereNotNull('eligible_at')
            ->where('eligible_at', '<=', now())
            ->get();

        $releasedCount = 0;
        foreach ($settlements as $settlement) {
            if ($escrowService->releaseSettlement($settlement)) {
                $releasedCount++;
            }
        }

        Log::info("ReleasePaymentJob: Released {$releasedCount} eligible escrow settlements.");
    }
}
