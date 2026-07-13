<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WaitListPromoted extends Notification
{
    use FormatsNotificationPayload, Queueable;

    public function __construct(
        public Reservation $reservation
    ) {}

    public function via(object $notifiable): array
    {
        return $this->defaultChannels(withWhatsApp: true);
    }

    public function toArray(object $notifiable): array
    {
        $activity = $this->reservation->activity?->title ?? 'نشاط';

        return $this->payload(
            title: 'مقعد متاح!',
            body: "تم تأكيد حجزك للنشاط \"{$activity}\" من قائمة الانتظار. التذكرة: {$this->reservation->ticket_id}",
            type: 'wait_list_promoted',
            actionUrl: '/reservations/' . $this->reservation->id,
            icon: 'star',
        );
    }
}
