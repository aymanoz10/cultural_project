<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteeringActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'cultural_center_id' => $this->cultural_center_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'location'           => $this->location,
            'start_time'         => $this->start_time,
            'image'              => $this->image ? asset('storage/' . $this->image) : null,
            'volunteers_count'   => $this->volunteersCount(),
        ];
    }
}
