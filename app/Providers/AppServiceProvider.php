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

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'listing' => \App\Models\Listing::class,
            'post' => \App\Models\Post::class,
            'discussion' => \App\Models\Discussion::class,
            'user' => \App\Models\User::class,
            'offer' => \App\Models\Offer::class,
            'item' => \App\Models\Item::class,
            'response' => \App\Models\Response::class,
            'conversation' => \App\Models\Conversation::class,
            'conversation_message' => \App\Models\ConversationMessage::class,
        ]);

        Blade::directive('money', function ($expression) {
            return "<?php echo app(\\App\\Services\\Location\\LocationService::class)->formatMoney($expression); ?>";
        });
    }
}
