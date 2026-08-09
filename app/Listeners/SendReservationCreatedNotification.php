<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\Reservation;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationPendingPayment;
use App\Notifications\ReservationWaitListed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 1;
    public $timeout = 120;

    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $user = $reservation->user;

        if (! $user) {
            return;
        }

        match ($reservation->status) {
            Reservation::STATUS_CONFIRMED => $user->notify(new ReservationConfirmed($reservation)),
            Reservation::STATUS_PENDING_PAYMENT => $user->notify(new ReservationPendingPayment($reservation)),
            default => $user->notify(new ReservationWaitListed($reservation)),
        };
    }
}
