<?php

namespace App\Listeners;

use App\Events\SuggestionSubmitted;
use App\Models\Admin;
use App\Notifications\NewSuggestionReceived;

class NotifyAdminsOfSuggestion
{
    public function handle(SuggestionSubmitted $event): void
    {
        $suggestion = $event->suggestion->loadMissing('user');

        Admin::query()->each(function (Admin $admin) use ($suggestion) {
            $admin->notify(new NewSuggestionReceived($suggestion));
        });
    }
}
