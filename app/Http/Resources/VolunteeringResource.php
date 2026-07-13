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
            'status'                   => $this->status,
            'form_data'                => $this->form_data,
            'volunteering_activity_id' => $this->volunteering_activity_id,
            'volunteering_activity'    => $this->whenLoaded(
                'volunteeringActivity',
                fn () => new VolunteeringActivityResource($this->volunteeringActivity)
            ),
            'user'                     => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at'               => $this->created_at,
        ];
    }
}
