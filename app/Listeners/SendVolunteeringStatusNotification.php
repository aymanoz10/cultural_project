<?php

namespace App\Listeners;

use App\Events\VolunteeringStatusUpdated;
use App\Notifications\VolunteeringStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVolunteeringStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(VolunteeringStatusUpdated $event): void
    {
        $volunteering = $event->volunteering->loadMissing('user', 'volunteeringActivity');
        $volunteering->user?->notify(new VolunteeringStatusChanged($volunteering));
    }
}
