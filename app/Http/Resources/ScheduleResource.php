<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'content' => $this->content,
            'legend' => LegendResource::collection($this->legends),
        ];
    }
}
