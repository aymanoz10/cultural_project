<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'cultural_center_id' => $this->cultural_center_id,
            'name'               => $this->name,
            'type'               => $this->type?->code,
            'capacity'           => $this->capacity,
            'features'           => $this->features,
            'image'              => $this->image ? asset('storage/' . $this->image) : null,
            'created_at'         => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}