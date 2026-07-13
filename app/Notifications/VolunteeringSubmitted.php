<?php

namespace App\Notifications;

use App\Models\Volunteering;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VolunteeringSubmitted extends Notification
{
    use FormatsNotificationPayload, Queueable;

    public function __construct(
        public Volunteering $volunteering
    ) {}

    public function via(object $notifiable): array
    {
        return $this->defaultChannels();
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->volunteering->volunteeringActivity?->title ?? 'فعالية تطوعية';
        $userName = $this->volunteering->user?->name ?? 'مستخدم';

        return $this->payload(
            title: 'طلب تطوع جديد',
            body: "{$userName} تقدّم للفعالية: {$title}",
            type: 'volunteering_submitted',
            actionUrl: '/admin/volunteerings/' . $this->volunteering->id,
            icon: 'hand-raised',
        );
    }
}
