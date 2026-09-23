<?php

namespace App\Providers;

use App\Services\Location\LocationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LocationService::class, function () {
            return new LocationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Media::observe(\App\Observers\MediaObserver::class);

        Blade::directive('money', function ($expression) {
            return "<?php echo app(\\App\\Services\\Location\\LocationService::class)->formatMoney($expression); ?>";
        });
    }
}
