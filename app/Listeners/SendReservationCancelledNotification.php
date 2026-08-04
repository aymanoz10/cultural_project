<?php

namespace App\Listeners;

use App\Events\ReservationCancelled;
use App\Notifications\ReservationCancelled as ReservationCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationCancelledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservationCancelled $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $reservation->user?->notify(new ReservationCancelledNotification($reservation));
    }
}
