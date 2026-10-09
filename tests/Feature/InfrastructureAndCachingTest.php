<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\Setting;
use App\Models\User;
use App\Services\Payment\EscrowService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InfrastructureAndCachingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_setting_get_value_caches_result(): void
    {
        Setting::setValue('test_cache_key', 42, 'integer');

        $val = Setting::getValue('test_cache_key');
        $this->assertSame(42, $val);

        $this->assertTrue(Cache::has('app_setting:test_cache_key'));

        // Direct DB update bypasses Eloquent events
        DB::table('settings')->where('name', 'test_cache_key')->update(['value' => '999']);

        // Since it is cached, getValue returns the cached 42
        $cachedVal = Setting::getValue('test_cache_key');
        $this->assertSame(42, $cachedVal);
    }

    public function test_setting_set_value_invalidates_cache(): void
    {
        Setting::setValue('platform_currency', 'NGN');
        $this->assertSame('NGN', Setting::getValue('platform_currency'));

        // Update via setValue
        Setting::setValue('platform_currency', 'USD');
        $this->assertSame('USD', Setting::getValue('platform_currency'));
    }

    public function test_setting_model_lifecycle_invalidates_cache(): void
    {
        Setting::setValue('tax_rate', 7.5, 'string');
        $this->assertSame('7.5', Setting::getValue('tax_rate'));

        $setting = Setting::where('name', 'tax_rate')->first();
        $setting->value = '10.0';
        $setting->save();

        // Model saved hook cleared the cache
        $this->assertSame('10.0', Setting::getValue('tax_rate'));

        $setting->delete();
        // Model deleted hook cleared the cache
        $this->assertSame('0.0', Setting::getValue('tax_rate', '0.0'));
    }

    public function test_escrow_release_settlement_thread_safe_and_idempotent(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-INFRA-01',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'subtotal' => 10000.00,
            'total' => 10000.00,
            'currency' => 'NGN',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $settlement = Settlement::create([
            'invoice_id' => $invoice->id,
            'seller_id' => $seller->id,
            'amount' => 9500.00,
            'platform_fee' => 500.00,
            'currency' => 'NGN',
            'status' => 'pending',
            'eligible_at' => now()->subDay(),
        ]);

        $escrowService = app(EscrowService::class);

        // First release succeeds
        $result = $escrowService->releaseSettlement($settlement);
        $this->assertTrue($result);

        $settlement->refresh();
        $this->assertSame('eligible', $settlement->status);
        $this->assertNotNull($invoice->fresh()->completed_at);

        // Duplicate release attempt fails gracefully
        $secondResult = $escrowService->releaseSettlement($settlement);
        $this->assertFalse($secondResult);
    }

    public function test_console_scheduler_jobs_have_without_overlapping_configured(): void
    {
        $schedule = app(Schedule::class);
        $events = $schedule->events();

        $this->assertNotEmpty($events);

        foreach ($events as $event) {
            $this->assertTrue(
                $event->withoutOverlapping,
                "Scheduled event [{$event->command}] or callback does not have withoutOverlapping enabled."
            );
        }
    }
}

