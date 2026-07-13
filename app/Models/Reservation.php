<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_WAIT_LIST = 'wait_list';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'qr_code',
        'hall_id',
        'theater_id',
        'activity_id',
        'library_id',
        'reservation_date',
        'status',
    ];

    protected $casts = [
        'reservation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function getCulturalCenterAttribute()
    {
        return $this->hall?->culturalCenter
            ?? $this->theater?->culturalCenter
            ?? $this->activity?->culturalCenter
            ?? null;
    }

    public function isWaitListed(): bool
    {
        return $this->status === self::STATUS_WAIT_LIST;
    }
}
