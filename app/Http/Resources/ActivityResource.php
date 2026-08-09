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
            'cultural_center'    => $this->whenLoaded('culturalCenter', fn () => $this->culturalCenter ? [
                'id'   => $this->culturalCenter->id,
                'name' => $this->culturalCenter->name,
            ] : null),
            'activity_type_id'   => $this->activity_type_id,
            'activity_type'      => $this->whenLoaded('activityType', fn () => new ActivityTypeResource($this->activityType)),
            'venue_id'           => $this->venue_id,
            'venue'              => $this->whenLoaded('venue', fn () => $this->venue ? [
                'id'   => $this->venue->id,
                'name' => $this->venue->name,
            ] : null),
            'title'              => $this->title,
            'presenter_name'     => $this->presenter_name,
            'presenter_avatar'   => $this->presenter_avatar ? asset('storage/' . $this->presenter_avatar) : null,
            'description'        => $this->description,
            'ticket_price'       => $this->ticket_price,
            'capacity'           => $this->capacity,
            'available_seats'    => $this->capacity !== null
                ? max(0, $this->capacity - $this->confirmedReservationsCount())
                : null,
            'start_time'         => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time'           => $this->end_time?->format('Y-m-d H:i:s'),
            'image'              => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}