<?php

namespace App\Listeners;

use App\Events\WaitListPromoted;
use App\Notifications\WaitListPromoted as WaitListPromotedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWaitListPromotedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 1;
    public $timeout = 120;

    public function handle(WaitListPromoted $event): void
    {
        $reservation = $event->reservation->loadMissing('activity', 'user');
        $reservation->user?->notify(new WaitListPromotedNotification($reservation));
    }
}
