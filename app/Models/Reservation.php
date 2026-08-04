<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_WAIT_LIST = 'WAIT_LIST';
    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'qr_payload', // تم تعديل الاسم من qr_code إلى qr_payload
        'activity_id',
        'venue_id',
        'library_id',
        'reservation_date',
        'seats_count',
        'status',
    ];

    /**
     * Domain Event: توليد المعرف والـ Payload عند الإنشاء تلقائياً
     */
    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (! $reservation->ticket_id) {
                // استخدام ULID يضمن عدم التكرار والترتيب الزمني مع التردد العالي
                $reservation->ticket_id = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
            }

            if (! $reservation->qr_payload) {
                $reservation->qr_payload = json_encode([
                    'ticket_id'   => $reservation->ticket_id,
                    'user_id'     => $reservation->user_id,
                    'activity_id' => $reservation->activity_id,
                    'seats_count' => $reservation->seats_count,
                    'status'      => $reservation->status,
                ], JSON_UNESCAPED_UNICODE);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}