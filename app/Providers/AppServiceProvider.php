<?php

namespace App\Providers;

use App\Events\ReservationCancelled;
use App\Events\ReservationCreated;
use App\Events\SuggestionSubmitted;
use App\Events\VolunteeringStatusUpdated;
use App\Events\VolunteeringSubmitted;
use App\Events\WaitListPromoted;
use App\Listeners\NotifyAdminsOfSuggestion;
use App\Listeners\NotifyAdminsOfVolunteering;
use App\Listeners\SendReservationCancelledNotification;
use App\Listeners\SendReservationCreatedNotification;
use App\Listeners\SendVolunteeringStatusNotification;
use App\Listeners\SendWaitListPromotedNotification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->hasHeader('x-forwarded-proto')) {
        URL::forceScheme('https');
    }
        // خطوة صفر: تفعيل مراقبة وتسجيل الاستعلامات البطيئة (> 100ms) كخط أساس للقياس
        DB::listen(function ($query) {
            if ($query->time > 100) {
                Log::warning("⚠️ استعلام قاعدة بيانات بطيء ({$query->time}ms): {$query->sql}", [
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            }
        });

        Relation::morphMap([
            'venue'    => 'App\Models\Venue',
            'center'   => 'App\Models\CulturalCenter',
            'activity' => 'App\Models\Activity',
        ]);

        Event::listen(ReservationCreated::class, SendReservationCreatedNotification::class);
        Event::listen(ReservationCancelled::class, SendReservationCancelledNotification::class);
        Event::listen(WaitListPromoted::class, SendWaitListPromotedNotification::class);
        Event::listen(VolunteeringSubmitted::class, NotifyAdminsOfVolunteering::class);
        Event::listen(VolunteeringStatusUpdated::class, SendVolunteeringStatusNotification::class);
        Event::listen(SuggestionSubmitted::class, NotifyAdminsOfSuggestion::class);
    }
}