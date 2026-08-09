<?php

namespace App\Listeners;

use App\Events\SuggestionSubmitted;
use App\Models\Admin;
use App\Notifications\NewSuggestionReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsOfSuggestion implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SuggestionSubmitted $event): void
    {
        $suggestion = $event->suggestion->loadMissing('user');

        Admin::query()->each(function (Admin $admin) use ($suggestion) {
            $admin->notify(new NewSuggestionReceived($suggestion));
        });
    }
}
