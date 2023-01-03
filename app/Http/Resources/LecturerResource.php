<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LecturerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'faculty' => new FacultyResource($this->faculty),
            'schedule' => new ScheduleResource($this->schedule),
        ];
    }
}
