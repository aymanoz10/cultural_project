<?php

namespace App\Listeners;

use App\Events\VolunteeringSubmitted;
use App\Models\Admin;
use App\Notifications\VolunteeringSubmitted as VolunteeringSubmittedNotification;

class NotifyAdminsOfVolunteering
{
    public function handle(VolunteeringSubmitted $event): void
    {
        $volunteering = $event->volunteering->loadMissing('user', 'volunteeringActivity');

        Admin::query()->each(function (Admin $admin) use ($volunteering) {
            $admin->notify(new VolunteeringSubmittedNotification($volunteering));
        });
    }
}
