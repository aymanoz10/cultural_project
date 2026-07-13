<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCancelled extends Notification
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
            title: 'تم إلغاء الحجز',
            body: "تم إلغاء حجزك للنشاط \"{$activity}\".",
            type: 'reservation_cancelled',
            actionUrl: '/reservations/' . $this->reservation->id,
            icon: 'x-circle',
        );
    }
}
