<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'value',
        'comment',
        'rateable_id',
        'rateable_type',
    ];

    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = max(1, min(5, (int) $value));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }
}
