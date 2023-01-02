<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LegendResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'abbreviation' => $this->abbreviation,
            'fullname' => $this->fullname,
        ];
    }
}
