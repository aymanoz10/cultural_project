<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'venue_id'           => $this->venue_id,
            'requesting_party'   => $this->requesting_party,
            'applicant_name'     => $this->applicant_name,
            'national_id_number' => $this->national_id_number,
            'reservation_reason' => $this->reservation_reason,
            'event_description'  => $this->event_description,
            'is_public'          => $this->is_public,
            'start_time'         => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time'           => $this->end_time?->format('Y-m-d H:i:s'),
            'status'             => $this->status,
            'notes'              => $this->notes,
            'venue'              => $this->whenLoaded('venue', fn () => new VenueResource($this->venue)),
            'created_at'         => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
