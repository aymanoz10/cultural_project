<?php

namespace App\Listeners;

use App\Events\WaitListPromoted;
use App\Notifications\WaitListPromoted as WaitListPromotedNotification;

class SendWaitListPromotedNotification
{
    public function handle(WaitListPromoted $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $reservation->user?->notify(new WaitListPromotedNotification($reservation));
    }
}
