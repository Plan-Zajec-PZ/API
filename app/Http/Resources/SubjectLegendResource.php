<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectLegendResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'content' => json_decode($this->content)
        ];
    }
}
