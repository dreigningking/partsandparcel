<?php

namespace App\Services\Location;

use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeographyService
{
    private const BASE_URL = 'https://api.countrystatecity.in/v1/countries';

    /**
     * Fetch and persist states + cities for the given country.
     * Uses the countrystatecity.in API with X-CSCAPI-KEY header.
     * Safe to call multiple times — uses updateOrCreate internally.
     */
    public function fetchAndSave(Country $country): void
    {
        try {
            $this->fetchStates($country);
        } catch (\Throwable $e) {
            Log::warning('GeographyService: failed to fetch geography for country '.$country->code, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    protected function apiKey(): string
    {
        return config('services.countrystatecity.key', '');
    }

    /**
     * Fetch all states for a country and save them.
     * GET https://api.countrystatecity.in/v1/countries/{country_code}/states
     */
    protected function fetchStates(Country $country): void
    {
        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => $this->apiKey(),
        ])->timeout(15)->get(self::BASE_URL."/{$country->code}/states");

        if (! $response->successful()) {
            Log::warning('GeographyService: HTTP error fetching states', [
                'country' => $country->code,
                'status'  => $response->status(),
                'message' => $response->body(),
            ]);

            return;
        }

        $states = $response->json();

        if (! is_array($states)) {
            return;
        }

        foreach ($states as $stateData) {
            $stateName = $stateData['name'] ?? null;
            if (! $stateName) {
                continue;
            }

            State::query()->updateOrCreate(
                ['country_id' => $country->id, 'name' => $stateName],
                [
                    'code'      => $stateData['iso2'] ?? strtoupper(substr(Str::slug($stateName), 0, 4)),
                    'is_active' => true,
                ]
            );
        }
    }

}
