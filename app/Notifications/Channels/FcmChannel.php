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
            Log::warning('FCM skipped: no device tokens registered for notifiable', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id'   => $notifiable->getKey(),
            ]);
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

        if (! $accessToken) {
            Log::warning('FCM skipped: could not obtain OAuth access token (check FCM_CREDENTIALS path/content, or the service-account key may need to be regenerated).');
            return;
        }

        if (! $projectId) {
            Log::warning('FCM skipped: FCM_PROJECT_ID is not set in .env.');
            return;
        }

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ((array) $tokens as $token) {
            try {
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
            } catch (\Throwable $e) {
                Log::error('FCM v1 send connection failed', [
                    'token' => $token,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            if ($response->successful()) {
                Log::info('FCM v1 send success', ['token' => $token]);
            }

            if (! $response->successful()) {
                $errorBody = $response->json();
                Log::error('FCM v1 send failed', [
                    'token'    => $token,
                    'response' => $errorBody,
                ]);

                // ✅ رفض دائم من فايربيز (الجهاز أزال التطبيق، أو تثبيت جديد
                // أبطل التوكن القديم) — لا فائدة من إعادة المحاولة لاحقاً،
                // فنحذف التوكن نهائياً من قاعدة البيانات فوراً.
                $errorStatus = $errorBody['error']['status'] ?? null;
                if (in_array($errorStatus, ['NOT_FOUND', 'UNREGISTERED', 'INVALID_ARGUMENT'], true)) {
                    \App\Models\DeviceToken::where('token', $token)->delete();
                    Log::info('FCM: removed dead device token', ['token' => $token]);
                }
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

        $cached = Cache::get('fcm_access_token');
        if ($cached) {
            return $cached;
        }

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

        try {
            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);
        } catch (\Throwable $e) {
            // ⚠️ فشل اتصال حقيقي (وليس رد خطأ HTTP عادي) — غالباً مشكلة
            // شهادات SSL شائعة بـ PHP على ويندوز، أو انقطاع إنترنت مؤقت.
            // لا نسمح لهذا بإسقاط الـ Job بالكامل، فقط نسجّله ونُرجع null
            // بدون تخزينه بالكاش، حتى تُعاد المحاولة بالمرة القادمة مباشرة.
            \Illuminate\Support\Facades\Log::error('FCM OAuth token request failed: ' . $e->getMessage());
            return null;
        }

        if (! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('FCM OAuth token request returned error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $token = $response->json('access_token');

        // ✅ نخزّن بالكاش فقط عند النجاح الفعلي — لا نُبقي فشلاً مؤقتاً محفوظاً 55 دقيقة
        if ($token) {
            Cache::put('fcm_access_token', $token, 3300);
        }

        return $token;
    }
}
