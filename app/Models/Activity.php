<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'cultural_center_id',
        'activity_type_id',
        'venue_id',
        'title',
        'presenter_name',
        'presenter_avatar',
        'description',
        'ticket_price',
        'capacity',
        'start_time',
        'end_time',
        'image',
    ];

    protected $casts = [
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
        'ticket_price' => 'decimal:2',
    ];

    public function culturalCenter()
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function confirmedReservationsCount(): int
    {
        // ✅ إصلاح: كانت تقارن بـ 'confirmed' (حروف صغيرة) بينما القيمة الفعلية
        // بقاعدة البيانات 'CONFIRMED' — ما يجعلها تُرجع صفراً دائماً على
        // قواعد بيانات حسّاسة لحالة الأحرف مثل PostgreSQL.
        // كذلك أصبحت الحالة 'PENDING_PAYMENT' تشغل مقعداً فعلياً (بانتظار
        // الدفع) تماماً مثل CONFIRMED، فيجب احتسابها ضمن "المقاعد المشغولة".
        return $this->reservations()
            ->whereIn('status', \App\Models\Reservation::SEAT_OCCUPYING_STATUSES)
            ->sum('seats_count');
    }

    public function hasAvailableSeats(): bool
    {
        if ($this->capacity === null) {
            return true;
        }

        return $this->confirmedReservationsCount() < $this->capacity;
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('value') ?? 0;
    }


}
