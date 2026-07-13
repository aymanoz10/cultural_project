<?php

namespace App\Listeners;

use App\Events\ReservationCancelled;
use App\Notifications\ReservationCancelled as ReservationCancelledNotification;

class SendReservationCancelledNotification
{
    public function handle(ReservationCancelled $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $reservation->user?->notify(new ReservationCancelledNotification($reservation));
    }
}
