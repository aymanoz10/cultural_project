<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ad extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'advertable_id',
        'advertable_type',
    ];

    public function advertable(): MorphTo
    {
        return $this->morphTo();
    }
}
