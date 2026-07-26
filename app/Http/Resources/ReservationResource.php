<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'ticket_id'        => $this->ticket_id,
            'qr_code'          => $this->qr_code,
            'status'           => $this->status,
            'reservation_date' => $this->reservation_date?->format('Y-m-d'),
            'user_id'          => $this->user_id,
            'activity_id'      => $this->activity_id,
            'venue_id'         => $this->venue_id,
            'library_id'       => $this->library_id,
            'activity'         => $this->whenLoaded('activity', fn () => new ActivityResource($this->activity)),
            'venue'            => $this->whenLoaded('venue', fn () => new VenueResource($this->venue)),
        ];
    }
}
