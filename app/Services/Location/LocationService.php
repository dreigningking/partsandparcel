<?php

namespace App\Services\Location;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    public const CACHE_TTL_SECONDS = 604800; // 7 days

    /**
     * Resolve location context based on User profile -> Redis cache -> IP API lookup.
     */
    public function resolveForRequest(Request $request): array
    {
        $ip = $this->getRealIp($request);

        // 1. If user is logged in, resolve from user profile
        if ($user = $request->user()) {
            $userCountry = Country::where('code', strtoupper($user->country_code ?? 'NG'))
                ->where('is_active', true)
                ->first() ?? $this->getDefaultCountry();

            $primaryLocation = $user->primaryLocation;

            $payload = [
                'ip' => $ip,
                'country_id' => $userCountry->id,
                'country_name' => $userCountry->name,
                'country_code' => $userCountry->code,
                'currency' => $userCountry->currency,
                'currency_symbol' => $userCountry->currency_symbol,
                'timezone' => $userCountry->timezone,
                'city' => $primaryLocation?->city,
                'state' => $primaryLocation?->state,
                'source' => 'user_profile',
            ];

            session()->put('current_location', $payload);

            return $payload;
        }

        // 2. If guest, check Redis cache first
        $cacheKey = "visitor_location:{$ip}";
        $cached = Cache::get($cacheKey);

        if ($cached && is_array($cached) && ! empty($cached['country_code'])) {
            $cached['source'] = 'redis_cache';
            session()->put('current_location', $cached);

            return $cached;
        }

        // 3. New visitor IP: lookup via ip-api and store in Redis
        $resolved = $this->fetchFromIpApi($ip);

        // Cache in Redis for 7 days
        Cache::put($cacheKey, $resolved, self::CACHE_TTL_SECONDS);
        session()->put('current_location', $resolved);

        return $resolved;
    }

    /**
     * Query ip-api.com for visitor geolocation.
     */
    public function fetchFromIpApi(string $ip): array
    {
        // Handle local development / private loopback addresses
        if ($this->isLocalOrPrivateIp($ip)) {
            $default = $this->getDefaultCountry();

            return [
                'ip' => $ip,
                'country_id' => $default->id,
                'country_name' => $default->name,
                'country_code' => $default->code,
                'currency' => $default->currency,
                'currency_symbol' => $default->currency_symbol,
                'timezone' => $default->timezone,
                'city' => 'Lagos',
                'state' => 'Lagos',
                'source' => 'local_fallback',
            ];
        }

        try {
            $response = Http::timeout(4)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,country,countryCode,region,regionName,city,lat,lon,timezone,query',
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json();
                $countryCode = strtoupper($data['countryCode'] ?? 'NG');

                $country = Country::where('code', $countryCode)
                    ->where('is_active', true)
                    ->first() ?? $this->getDefaultCountry();

                return [
                    'ip' => $ip,
                    'country_id' => $country->id,
                    'country_name' => $country->name,
                    'country_code' => $country->code,
                    'currency' => $country->currency,
                    'currency_symbol' => $country->currency_symbol,
                    'timezone' => $data['timezone'] ?? $country->timezone,
                    'city' => $data['city'] ?? null,
                    'state' => $data['regionName'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'source' => 'ip_api',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('LocationService IP-API lookup failed: ' . $e->getMessage(), ['ip' => $ip]);
        }

        // Fallback to default active country if lookup fails
        $default = $this->getDefaultCountry();

        return [
            'ip' => $ip,
            'country_id' => $default->id,
            'country_name' => $default->name,
            'country_code' => $default->code,
            'currency' => $default->currency,
            'currency_symbol' => $default->currency_symbol,
            'timezone' => $default->timezone,
            'city' => null,
            'state' => null,
            'source' => 'default_fallback',
        ];
    }

    /**
     * Get real visitor client IP handling reverse proxies and Cloudflare.
     */
    public function getRealIp(Request $request): string
    {
        return $request->header('CF-Connecting-IP')
            ?? $request->header('X-Forwarded-For')
            ?? $request->ip()
            ?? '127.0.0.1';
    }

    /**
     * Get default active country.
     */
    public function getDefaultCountry(): Country
    {
        return Country::where('code', 'NG')->where('is_active', true)->first()
            ?? Country::where('is_active', true)->first()
            ?? new Country([
                'name' => 'Nigeria',
                'code' => 'NG',
                'phone_code' => '+234',
                'currency' => 'NGN',
                'currency_symbol' => '₦',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ]);
    }

    /**
     * Check if IP is localhost or RFC1918 private subnet.
     */
    protected function isLocalOrPrivateIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost'])
            || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Format money in local currency without cross-rate conversion.
     */
    public function formatMoney(float|int $amount, ?string $currency = null): string
    {
        $symbol = '₦';
        $curr = $currency ?? session('current_location.currency', 'NGN');

        if ($curr === 'USD') {
            $symbol = '$';
        }

        return $symbol . ' ' . number_format($amount, 2);
    }
}
