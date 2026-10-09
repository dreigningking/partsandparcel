<?php

use App\Jobs\AbandonedCartJob;
use App\Jobs\ModerationNotifierJob;
use App\Jobs\SubscriptionAutoRenewJob;
use App\Jobs\SubscriptionExpiredJob;
use App\Jobs\SubscriptionExpiringJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SubscriptionExpiringJob)->daily()->withoutOverlapping();
Schedule::job(new SubscriptionExpiredJob)->daily()->withoutOverlapping();
Schedule::job(new SubscriptionAutoRenewJob)->hourly()->withoutOverlapping();
Schedule::job(new AbandonedCartJob)->daily()->withoutOverlapping();
Schedule::job(new ModerationNotifierJob)->hourly()->withoutOverlapping();
Schedule::job(new \App\Jobs\ProcessOrderFulfillmentTimelinesJob)->hourly()->withoutOverlapping();
Schedule::job(new \App\Jobs\ReleasePaymentJob)->hourly()->withoutOverlapping();
