<?php

namespace App\Notifications;

use App\Models\VenueReservation;
use App\Notifications\Concerns\FormatsNotificationPayload;
use Illuminate\Notifications\Notification;

/**
 * إشعار لوحة التحكم للمشرفين عند وصول طلب حجز قاعة جديد.
 * غير مُصفّى (يُرسل فوراً) وعلى قناة database فقط ليظهر في جرس الإشعارات.
 */
class NewVenueReservation extends Notification
{
    use FormatsNotificationPayload;

    public function __construct(public VenueReservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload(
            title: 'طلب حجز قاعة جديد',
            body: ($this->reservation->applicant_name ?: 'مقدّم طلب') . ' — ' . ($this->reservation->venue->name ?? 'قاعة'),
            type: 'venue_reservation',
            actionUrl: '/admin/venue-reservations/' . $this->reservation->id,
            icon: 'calendar',
        );
    }
}
