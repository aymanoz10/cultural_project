<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    public const TYPES = [
        'workshop',
        'lecture',
        'show',
        'exhibition',
        'festival',
    ];

    protected $fillable = [
        'cultural_center_id',
        'type',
        'hall_id',
        'theater_id',
        'title',
        'host_name',
        'description',
        'start_time',
        'end_time',
        'capacity',
        'image',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function culturalCenter()
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function ads()
    {
        return $this->morphMany(Ad::class, 'advertable');
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('value') ?? 0;
    }

    public function confirmedReservationsCount(): int
    {
        return $this->reservations()->where('status', 'confirmed')->count();
    }

    public function hasAvailableSeats(): bool
    {
        if ($this->capacity === null) {
            return true;
        }

        return $this->confirmedReservationsCount() < $this->capacity;
    }
}
