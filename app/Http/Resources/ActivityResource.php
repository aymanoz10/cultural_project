<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'cultural_center_id' => $this->cultural_center_id,
            'type'               => $this->type,
            'hall_id'            => $this->hall_id,
            'theater_id'         => $this->theater_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'start_time'         => $this->start_time,
            'end_time'           => $this->end_time,
            'capacity'           => $this->capacity,
            'available_seats'    => $this->capacity !== null
                ? max(0, $this->capacity - $this->confirmedReservationsCount())
                : null,
            'image'             => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}
