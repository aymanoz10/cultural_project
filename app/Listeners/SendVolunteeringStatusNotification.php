<?php

namespace App\Listeners;

use App\Events\VolunteeringStatusUpdated;
use App\Notifications\VolunteeringStatusChanged;

class SendVolunteeringStatusNotification
{
    public function handle(VolunteeringStatusUpdated $event): void
    {
        $volunteering = $event->volunteering->loadMissing('user', 'volunteeringActivity');
        $volunteering->user?->notify(new VolunteeringStatusChanged($volunteering));
    }
}
