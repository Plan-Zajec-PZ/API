<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
          'content' => json_decode($this->content)
        ];
    }
}
