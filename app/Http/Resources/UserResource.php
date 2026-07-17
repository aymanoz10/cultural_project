<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender'      => $this->gender,
            'avatar'      => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
