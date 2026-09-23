<?php

use App\Jobs\AbandonedCartJob;
use App\Jobs\SubscriptionAutoRenewJob;
use App\Jobs\SubscriptionExpiredJob;
use App\Jobs\SubscriptionExpiringJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SubscriptionExpiringJob)->daily();
Schedule::job(new SubscriptionExpiredJob)->daily();
Schedule::job(new SubscriptionAutoRenewJob)->hourly();
Schedule::job(new AbandonedCartJob)->daily();
