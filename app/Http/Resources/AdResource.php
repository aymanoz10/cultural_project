<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'image'           => $this->image ? asset('storage/' . $this->image) : null,
            'advertable_type' => class_basename($this->advertable_type),
            'advertable_id'   => $this->advertable_id,
            'advertable'      => $this->whenLoaded('advertable'),
        ];
    }
}
