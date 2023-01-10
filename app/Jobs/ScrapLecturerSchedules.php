<?php

namespace App\Jobs;

use App\Models\Lecturer;
use App\Spiders\LecturerScheduleSpider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RoachPHP\Roach;

class ScrapLecturerSchedules implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $trackingNumberId;

    public function __construct(int $trackingNumberId)
    {
        $this->trackingNumberId = $trackingNumberId;
    }

    public function handle(): void
    {
        Lecturer::query()->cursor()->chunk(10)->each(function ($chunk) {
            Roach::collectSpider(LecturerScheduleSpider::class, context: [
                'trackingNumberId' => $this->trackingNumberId,
                'models' => $chunk,
            ]);
        });
    }
}
