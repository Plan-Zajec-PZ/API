<?php

namespace App\Http\Resources;

use App\Models\Schedule;
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

    private function scrapSchedule(): Schedule
    {
        $context = [
            'models' => [$this->resource]
        ];

        Roach::collectSpider(
            LecturerScheduleSpider::class,
            context: $context
        );

        $this->resource->refresh();

        return $this->schedule;
    }
}
