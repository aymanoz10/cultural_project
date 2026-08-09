<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationWaitListed extends Notification
{
    use FormatsNotificationPayload, Queueable;

    public function __construct(
        public Reservation $reservation
    ) {}

    public function via(object $notifiable): array
    {
        return $this->defaultChannels();
    }

    public function toArray(object $notifiable): array
    {
        $activity = $this->reservation->activity?->title ?? 'نشاط';

        return $this->payload(
            title: 'أُضفت لقائمة الانتظار',
            body: "النشاط \"{$activity}\" ممتلئ. أُضفت لقائمة الانتظار وسنُبلغك عند توفر مقعد.",
            type: 'reservation_wait_list',
            actionUrl: '/reservations/' . $this->reservation->id,
            icon: 'clock',
        );
    }
}
