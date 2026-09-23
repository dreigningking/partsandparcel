<?php

namespace App\Services\Notification;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Register or update a device token for a user.
     */
    public function registerToken(User $user, string $token, string $platform = 'web', ?string $deviceName = null): DeviceToken
    {
        return DeviceToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id' => $user->id,
                'platform' => $platform,
                'device_name' => $deviceName,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Remove a device token (e.g. on logout).
     */
    public function removeToken(string $token): bool
    {
        return (bool) DeviceToken::where('token', $token)->delete();
    }

    /**
     * Send push notification to all registered devices of a given user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => true,
                'sent_count' => 0,
                'message' => 'No registered device tokens for user',
            ];
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendToToken($token, $title, $body, $data);
        }

        return [
            'success' => true,
            'sent_count' => count(array_filter($results)),
            'results' => $results,
        ];
    }

    /**
     * Send push notification to a single FCM device token.
     * Fails gracefully without throwing unhandled exceptions.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        $serverKey = config('services.fcm.server_key') ?? env('FCM_SERVER_KEY');

        // If FCM credentials are not configured or in local test environment, log and return gracefully
        if (empty($serverKey)) {
            Log::info("FCM Notification simulated for token [{$token}]: {$title} - {$body}", [
                'data' => $data,
            ]);
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]),
            ]);

            if ($response->successful()) {
                $bodyRes = $response->json();
                // Check if token was reported as invalid or unregistered
                if (! empty($bodyRes['results'][0]['error'])) {
                    $error = $bodyRes['results'][0]['error'];
                    if (in_array($error, ['NotRegistered', 'InvalidRegistration', 'MismatchSenderId'])) {
                        DeviceToken::where('token', $token)->delete();
                        Log::info("Pruned invalid FCM token: {$token}");
                    }
                    return false;
                }
                return true;
            }

            // HTTP 400/401/404 handling
            if ($response->status() === 404) {
                DeviceToken::where('token', $token)->delete();
            }

            Log::warning("FCM delivery failed for token [{$token}]: HTTP {$response->status()}", [
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error("FCM Exception for token [{$token}]: " . $e->getMessage());
            return false;
        }
    }
}
