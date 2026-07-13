<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cultural_center_id' => $this->cultural_center_id,
            'name' => $this->name,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}