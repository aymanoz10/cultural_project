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

    /** آلة الحالات: الانتقالات المسموحة من كل حالة */
    public const TRANSITIONS = [
        self::STATUS_PENDING  => [self::STATUS_ACCEPTED, self::STATUS_REJECTED],
        self::STATUS_ACCEPTED => [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CANCELLED],
        self::STATUS_APPROVED => [self::STATUS_CANCELLED],
    ];

    /** التسميات العربية للحالات */
    public const STATUS_LABELS = [
        self::STATUS_PENDING   => 'قيد الانتظار',
        self::STATUS_ACCEPTED  => 'مقبول',
        self::STATUS_APPROVED  => 'معتمد',
        self::STATUS_REJECTED  => 'مرفوض',
        self::STATUS_CANCELLED => 'ملغى',
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** الحالات المسموح الانتقال إليها من الحالة الحالية */
    public function allowedTransitions(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    /** هل الحالة نهائية (لا انتقال منها)؟ */
    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }

    /** التسمية العربية للحالة الحالية */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
