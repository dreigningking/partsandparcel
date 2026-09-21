<?php

namespace App\Http\Middleware;

use App\Models\Country;
use App\Services\Location\LocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveVisitorLocation
{
    public function __construct(protected LocationService $locationService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user manually requested a country switch (e.g., ?switch_country=US)
        if ($request->has('switch_country')) {
            $switchCode = strtoupper($request->query('switch_country'));
            $switchCountry = Country::where('code', $switchCode)->where('is_active', true)->first();

            if ($switchCountry) {
                session()->put('current_location', [
                    'country_id' => $switchCountry->id,
                    'country_name' => $switchCountry->name,
                    'country_code' => $switchCountry->code,
                    'currency' => $switchCountry->currency,
                    'currency_symbol' => $switchCountry->currency_symbol,
                    'timezone' => $switchCountry->timezone,
                    'source' => 'manual_selection',
                ]);

                if ($user = $request->user()) {
                    $user->update([
                        'country_code' => $switchCountry->code,
                        'currency' => $switchCountry->currency,
                    ]);
                }
            }
        }

        // Resolve location via LocationService if session is missing
        if (! session()->has('current_location')) {
            $this->locationService->resolveForRequest($request);
        }

        $currentLocation = session('current_location', []);

        // Dynamically set timezone if available
        if (! empty($currentLocation['timezone'])) {
            try {
                date_default_timezone_set($currentLocation['timezone']);
            } catch (\Throwable $e) {
                // Keep default if invalid timezone string
            }
        }

        // Share globally with all views
        View::share('currentLocation', $currentLocation);
        View::share('currentCountryCode', $currentLocation['country_code'] ?? 'NG');
        View::share('currentCountryName', $currentLocation['country_name'] ?? 'Nigeria');
        View::share('currentCurrency', $currentLocation['currency'] ?? 'NGN');
        View::share('currentCurrencySymbol', $currentLocation['currency_symbol'] ?? '₦');

        return $next($request);
    }
}
