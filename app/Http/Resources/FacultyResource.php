<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'majors' => MajorResource::collection($this->whenLoaded('majors')),
            'lecturers' => LecturerResource::collection($this->whenLoaded('lecturers')),
        ];
    }
}
