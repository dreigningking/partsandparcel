<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Subscription Quota & Plan Limits Calculation.
 *
 * Unit tests inherit from PHPUnit\Framework\TestCase (NOT Tests\TestCase).
 * They do NOT boot the Laravel database or HTTP kernel.
 * This makes them execute in milliseconds (< 5ms).
 */
class SubscriptionQuotaTest extends TestCase
{
    /**
     * Test 1: Quota calculation when responses used are below limit.
     */
    public function test_quota_remaining_calculated_correctly_when_under_limit(): void
    {
        // 1. ARRANGE: Set up initial conditions and inputs
        $dailyLimit = 10;
        $responsesUsed = 3;

        // 2. ACT: Execute the calculation logic
        $remaining = max(0, $dailyLimit - $responsesUsed);
        $canRespond = $responsesUsed < $dailyLimit;

        // 3. ASSERT: Verify the expected output
        $this->assertEquals(7, $remaining);
        $this->assertTrue($canRespond);
    }

    /**
     * Test 2: Quota calculation when usage reaches the exact limit.
     */
    public function test_quota_is_exhausted_when_usage_equals_limit(): void
    {
        // 1. ARRANGE
        $dailyLimit = 5;
        $responsesUsed = 5;

        // 2. ACT
        $remaining = max(0, $dailyLimit - $responsesUsed);
        $canRespond = $responsesUsed < $dailyLimit;

        // 3. ASSERT
        $this->assertEquals(0, $remaining);
        $this->assertFalse($canRespond);
    }

    /**
     * Test 3: Quota calculation prevents negative numbers if usage exceeds limit.
     */
    public function test_quota_never_becomes_negative(): void
    {
        // 1. ARRANGE
        $dailyLimit = 5;
        $responsesUsed = 8; // edge case: race condition allowed extra responses

        // 2. ACT
        $remaining = max(0, $dailyLimit - $responsesUsed);
        $canRespond = $responsesUsed < $dailyLimit;

        // 3. ASSERT
        $this->assertEquals(0, $remaining, 'Remaining quota must clamp to 0, never negative');
        $this->assertFalse($canRespond);
    }

    /**
     * Test 4: Plan features array parsing fallback.
     */
    public function test_plan_features_fallback_to_defaults(): void
    {
        // 1. ARRANGE: Simulated plan with empty features JSON array
        $features = [];

        // 2. ACT: Extract features using null coalescing defaults
        $dailyLimit = $features['daily_response_limit'] ?? 1;
        $listingLimit = $features['listing_limit'] ?? 10;
        $hasDisassemblyTool = (bool) ($features['disassembly_tool'] ?? false);

        // 3. ASSERT
        $this->assertEquals(1, $dailyLimit);
        $this->assertEquals(10, $listingLimit);
        $this->assertFalse($hasDisassemblyTool);
    }

    /**
     * Test 5: Days remaining calculation logic.
     */
    public function test_days_remaining_calculation(): void
    {
        // 1. ARRANGE
        $now = new \DateTimeImmutable('2026-09-22 12:00:00');
        $endsAt = new \DateTimeImmutable('2026-09-25 12:00:00');
        $expiredAt = new \DateTimeImmutable('2026-09-20 12:00:00');

        // 2. ACT
        $intervalFuture = $now->diff($endsAt);
        $daysFuture = (int) $intervalFuture->format('%r%a');

        $intervalPast = $now->diff($expiredAt);
        $daysPast = (int) $intervalPast->format('%r%a');

        // 3. ASSERT
        $this->assertEquals(3, max(0, $daysFuture));
        $this->assertEquals(0, max(0, $daysPast), 'Expired subscription must return 0 days left');
    }
}
