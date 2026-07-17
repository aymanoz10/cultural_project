<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CulturalCenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'location'    => $this->location,
            'description' => $this->description,
            'photos'      => CulturalCenterPhotoResource::collection($this->whenLoaded('photos')),
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
