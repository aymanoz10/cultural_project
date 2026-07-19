<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteeringResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'first_name'               => $this->first_name,
            'last_name'                => $this->last_name,
            'email'                    => $this->email,
            'whatsapp_number'          => $this->whatsapp_number,
            'birthday_date'            => $this->birthday_date,
            'address'                  => $this->address,
            'education_level'          => $this->education_level,
            'has_volunteered_before'   => $this->has_volunteered_before,
            'previous_experiences'     => $this->previous_experiences,
            'why_volunteer'            => $this->why_volunteer,
            'volunteering_interest'    => $this->volunteering_interest,
            'tools'                    => $this->tools,
            'center'                   => $this->center,
            'available_times'          => $this->available_times,
            'notes'                    => $this->notes,
            'status'                   => $this->status,
            'created_at'               => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
