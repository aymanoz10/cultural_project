<?php

namespace App\Notifications;

use App\Models\Volunteering;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VolunteeringStatusChanged extends Notification
{
    use FormatsNotificationPayload, Queueable;

    public function __construct(
        public Volunteering $volunteering
    ) {}

    public function via(object $notifiable): array
    {
        $withWhatsApp = in_array($this->volunteering->status, ['accepted', 'rejected'], true);

        return $this->defaultChannels(withWhatsApp: $withWhatsApp);
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->volunteering->volunteeringActivity?->title ?? 'فعالية تطوعية';
        $statusLabel = match ($this->volunteering->status) {
            'accepted' => 'قُبل',
            'rejected' => 'رُفض',
            default    => 'قيد المراجعة',
        };

        return $this->payload(
            title: 'تحديث طلب التطوع',
            body: "طلبك للفعالية \"{$title}\" {$statusLabel}.",
            type: 'volunteering_status_' . $this->volunteering->status,
            actionUrl: '/volunteerings/' . $this->volunteering->id,
            icon: 'user-group',
        );
    }
}
