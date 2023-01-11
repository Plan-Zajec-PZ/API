<?php

namespace App\Jobs;

use App\Spiders\MajorsSpider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RoachPHP\Roach;

class ScrapMajors implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $trackingNumberId;

    public function __construct(int $trackingNumberId)
    {
        $this->trackingNumberId = $trackingNumberId;
    }

    public function handle()
    {
        Roach::collectSpider(MajorsSpider::class, context: [
            'trackingNumberId' => $this->trackingNumberId,
        ]);
    }
}
