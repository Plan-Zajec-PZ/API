<?php

namespace App\Jobs;

use App\Spiders\LecturersSpider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RoachPHP\Roach;

class ScrapLecturers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $trackingNumberId;

    public function __construct(int $trackingNumberId)
    {
        $this->trackingNumberId = $trackingNumberId;
    }

    public function handle(): void
    {
        Roach::collectSpider(LecturersSpider::class, context: [
            'trackingNumberId' => $this->trackingNumberId,
        ]);
    }
}
