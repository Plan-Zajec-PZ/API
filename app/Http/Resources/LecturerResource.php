<?php

namespace App\Http\Resources;

use App\Spiders\LecturerScheduleSpider;
use Illuminate\Http\Resources\Json\JsonResource;
use RoachPHP\Roach;

class LecturerResource extends JsonResource
{
    public function toArray($request): array
    {
        $schedule = $this->schedule ?? $this->scrapSchedule();

        return [
            'name' => $this->name,
            'faculty' => new FacultyResource($this->faculty),
            'schedule' => new ScheduleResource($schedule),
        ];
    }

    private function scrapSchedule(): array
    {
        $context = [
            'models' => [$this->resource]
        ];

        $collected = Roach::collectSpider(
            LecturerScheduleSpider::class,
            context: $context
        );

        return $collected[0]->get('schedule');
    }
}
