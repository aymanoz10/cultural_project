<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * قناة إشعارات FCM عبر واجهة HTTP v1 (البديل عن Legacy API المُوقَف).
 * المصادقة عبر حساب خدمة (Service Account) وتوليد access token مؤقّت.
 * تبقى في وضع 'log' افتراضيًا؛ فعّل FCM_MODE=v1 وحدّد project_id والاعتمادات لإرسال فعلي.
 */
class FcmChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $tokens = $notifiable->routeNotificationForFcm();

        if (empty($tokens)) {
            return;
        }

        $payload = $notification->toFcm($notifiable);
        $mode = config('services.fcm.mode', 'log');

        if ($mode === 'log') {
            Log::info('FCM Push', [
                'tokens'  => $tokens,
                'payload' => $payload,
            ]);
            return;
        }

        $accessToken = $this->accessToken();
        $projectId   = config('services.fcm.project_id');

        if (! $accessToken || ! $projectId) {
            Log::warning('FCM skipped: missing project_id or service-account credentials');
            return;
        }

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ((array) $tokens as $token) {
            $response = Http::withToken($accessToken)
                ->timeout(10)->retry(2, 500)
                ->post($endpoint, [
                    'message' => [
                        'token'        => $token,
                        'notification' => [
                            'title' => $payload['title'] ?? '',
                            'body'  => $payload['body'] ?? '',
                        ],
                        'data' => array_map('strval', $payload['data'] ?? []),
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('FCM v1 send failed', [
                    'token'    => $token,
                    'response' => $response->json(),
                ]);
            }
        }
    }

    /**
     * توليد access token عبر تدفّق JWT الخاص بحساب الخدمة (RS256)، مع تخزينه مؤقتًا 55 دقيقة.
     */
    private function accessToken(): ?string
    {
        $path = config('services.fcm.credentials');

        if (! $path || ! is_file($path)) {
            return null;
        }

        return Cache::remember('fcm_access_token', 3300, function () use ($path) {
            $sa = json_decode((string) file_get_contents($path), true);

            if (! isset($sa['client_email'], $sa['private_key'])) {
                return null;
            }

            $now = time();
            $claim = [
                'iss'   => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ];

            $base64Url = fn (string $data): string => rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

            $segments = [
                $base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])),
                $base64Url(json_encode($claim)),
            ];
            $signingInput = implode('.', $segments);

            $signature = '';
            openssl_sign($signingInput, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = $signingInput . '.' . $base64Url($signature);

            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');
        });
    }
}
