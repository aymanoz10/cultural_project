<?php

namespace App\Http\Resources;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'ticket_id'        => $this->ticket_id,
            // ✅ إصلاح: كانت الدالة تشير لحقل 'qr_code' غير موجود بالموديل
            // (الحقل الفعلي 'qr_payload')، فكانت تُرجع null دائماً.
            'qr_payload'       => $this->qr_payload,
            'status'           => $this->status,
            'status_label'     => $this->statusLabel(),
            // تُستخدم بالفلاتر لتحديد الأزرار المتاحة (إلغاء/QR) دون تكرار
            // منطق الحالات يدوياً بكل شاشة.
            'can_cancel'       => in_array($this->status, Reservation::SEAT_OCCUPYING_STATUSES, true),
            'can_show_qr'      => in_array($this->status, Reservation::SEAT_OCCUPYING_STATUSES, true),
            'is_paid_activity' => $this->activity && $this->activity->ticket_price !== null
                && (float) $this->activity->ticket_price > 0,
            'reservation_date' => $this->reservation_date?->format('Y-m-d'),
            'seats_count'      => $this->seats_count,
            'created_at'       => $this->created_at?->toIso8601String(),
            'user_id'          => $this->user_id,
            'activity_id'      => $this->activity_id,
            'venue_id'         => $this->venue_id,
            'library_id'       => $this->library_id,
            'activity'         => $this->whenLoaded('activity', fn () => new ActivityResource($this->activity)),
            'venue'            => $this->whenLoaded('venue', fn () => new VenueResource($this->venue)),
        ];
    }
}
