<?php

namespace App\Events;

use App\Models\Volunteering;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VolunteeringStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Volunteering $volunteering
    ) {}
}
