<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\Reservation;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationWaitListed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $user = $reservation->user;

        if (! $user) {
            return;
        }

        if ($reservation->status === Reservation::STATUS_CONFIRMED) {
            $user->notify(new ReservationConfirmed($reservation));
            return;
        }

        $user->notify(new ReservationWaitListed($reservation));
    }
}
