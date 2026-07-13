<?php

namespace App\Notifications;

use App\Models\Suggestion;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewSuggestionReceived extends Notification
{
    use FormatsNotificationPayload, Queueable;

    public function __construct(
        public Suggestion $suggestion
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', \App\Notifications\Channels\FcmChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = match ($this->suggestion->type) {
            'complaint'  => 'شكوى',
            'question'   => 'سؤال',
            default      => 'اقتراح',
        };

        return $this->payload(
            title: "{$typeLabel} جديد",
            body: Str::limit($this->suggestion->content, 120),
            type: 'new_suggestion',
            actionUrl: '/admin/suggestions/' . $this->suggestion->id,
            icon: 'chat',
        );
    }
}
