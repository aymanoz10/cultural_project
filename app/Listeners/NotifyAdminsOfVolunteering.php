<?php

namespace App\Listeners;

use App\Events\VolunteeringSubmitted;
use App\Models\Admin;
use App\Notifications\VolunteeringSubmitted as VolunteeringSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsOfVolunteering implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(VolunteeringSubmitted $event): void
    {
        $volunteering = $event->volunteering->loadMissing('user', 'volunteeringActivity');

        Admin::query()->each(function (Admin $admin) use ($volunteering) {
            $admin->notify(new VolunteeringSubmittedNotification($volunteering));
        });
    }
}
