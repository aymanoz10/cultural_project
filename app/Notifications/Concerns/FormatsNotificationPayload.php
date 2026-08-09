<?php

namespace App\Notifications\Concerns;

use App\Notifications\Channels\FcmChannel;
use App\Notifications\Channels\WhatsAppChannel;

trait FormatsNotificationPayload
{
    protected function defaultChannels(bool $withWhatsApp = false): array
    {
        $channels = ['database', FcmChannel::class];

        if ($withWhatsApp) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    protected function payload(string $title, string $body, string $type, ?string $actionUrl = null, ?string $icon = null): array
    {
        return [
            'title'      => $title,
            'body'       => $body,
            'type'       => $type,
            'icon'       => $icon ?? $type,
            'action_url' => $actionUrl,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        $data = $this->toArray($notifiable);

        return [
            'title' => $data['title'],
            'body'  => $data['body'],
            'data'  => [
                'type'       => $data['type'],
                'action_url' => $data['action_url'] ?? '',
            ],
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $data = $this->toArray($notifiable);

        return "{$data['title']}\n{$data['body']}";
    }
}
