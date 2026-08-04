<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'description' => $this->description,
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
