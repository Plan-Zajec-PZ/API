<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecializationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
            'abbreviationLegend' => AbbreviationLegendResource::collection($this->whenLoaded('abbreviationLegends')),
            'subjectLegends' => SubjectLegendResource::collection($this->whenLoaded('subjectLegends')),
        ];
    }
}
