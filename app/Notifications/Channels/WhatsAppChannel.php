<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(
        private WhatsAppService $whatsApp
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phone = $notifiable->routeNotificationForWhatsApp();

        if (! $phone) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        $this->whatsApp->sendMessage($phone, $message);
    }
}
