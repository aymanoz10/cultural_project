<?php

namespace App\Http\Resources;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender'      => $this->gender,
            'avatar'      => $this->avatar ? asset('storage/' . $this->avatar) : null,
            // عدد الفعاليات التي حضرها المستخدم فعلياً (تم مسح باركود تذكرتها)
            'completed_events_count' => Reservation::where('user_id', $this->id)
                ->where('status', Reservation::STATUS_COMPLETED)
                ->count(),
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}