<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppService
{
    /**
     * إرسال رسالة نصية عبر UltraMsg
     */
    public function sendMessage(string $phone, string $message): void
    {
        $mode = config('services.whatsapp.mode', 'log');

        if ($mode === 'log') {
            Log::info('WhatsApp Notification', [
                'phone'   => $phone,
                'message' => $message,
            ]);
            return;
        }

        $instanceId = config('services.whatsapp.instance_id');
        $token = config('services.whatsapp.token');

        if (! $instanceId || ! $token) {
            Log::warning('WhatsApp notification skipped: missing UltraMsg credentials');
            return;
        }

        $response = Http::asForm()->post(
            "https://api.ultramsg.com/{$instanceId}/messages/chat",
            [
                'token' => $token,
                'to'    => $phone,
                'body'  => $message,
            ]
        );

        if (! $response->successful()) {
            Log::error('WhatsApp notification failed', [
                'phone'    => $phone,
                'response' => $response->json(),
            ]);
        }
    }

    /**
     * إرسال OTP عبر UltraMsg
     */
    public function sendOtp(string $phone, string $code): void
    {
        $mode = config('services.whatsapp.mode', 'log');

        if ($mode === 'log') {
            Log::info('🔐 WhatsApp OTP', [
                'phone' => $phone,
                'code'  => $code,
            ]);
            return;
        }

        $instanceId = config('services.whatsapp.instance_id');
        $token = config('services.whatsapp.token');

        if (! $instanceId || ! $token) {
            throw new RuntimeException('إعدادات UltraMsg غير مكتملة');
        }

        $message = "رمز التحقق الخاص بك هو: *{$code}*\n\n" .
                   "لا تشارك هذا الرمز مع أي شخص.\n" .
                   "صلاحيته 5 دقائق فقط.";

        $response = Http::asForm()->post(
            "https://api.ultramsg.com/{$instanceId}/messages/chat",
            [
                'token' => $token,
                'to'    => $phone,
                'body'  => $message,
            ]
        );

        if (! $response->successful()) {
            Log::error('WhatsApp OTP failed', [
                'phone'    => $phone,
                'response' => $response->json(),
            ]);

            throw new RuntimeException('فشل إرسال رمز التحقق عبر WhatsApp');
        }
    }
}