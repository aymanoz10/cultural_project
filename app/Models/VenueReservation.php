<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueReservation extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACCEPTED  = 'accepted';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'venue_id',
        'requesting_party',
        'applicant_name',
        'national_id_number',
        'reservation_reason',
        'event_description',
        'is_public',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'is_public'  => 'boolean',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
