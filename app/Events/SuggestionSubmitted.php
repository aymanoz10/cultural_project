<?php

namespace App\Events;

use App\Models\Suggestion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuggestionSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Suggestion $suggestion
    ) {}
}
