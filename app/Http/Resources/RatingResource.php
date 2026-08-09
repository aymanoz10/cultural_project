<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'value'         => $this->value,
            'comment'       => $this->comment,
            'rateable_type' => class_basename($this->rateable_type),
            'rateable_id'   => $this->rateable_id,
            'user'          => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at'    => $this->created_at,
        ];
    }
}
