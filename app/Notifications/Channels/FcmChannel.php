<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                'tokens' => $tokens,
                'payload' => $payload,
            ]);
            return;
        }

        $serverKey = config('services.fcm.server_key');

        if (! $serverKey) {
            return;
        }

        Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'registration_ids' => $tokens,
            'notification'     => [
                'title' => $payload['title'],
                'body'  => $payload['body'],
            ],
            'data' => $payload['data'] ?? [],
        ]);
    }
}
