<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = [
        'cultural_center_id',
        'name',
        'capacity',
        'features',
        'image',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function culturalCenter()
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }
}
