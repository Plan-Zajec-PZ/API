<?php

namespace App\Console\Commands;

use App\Actions\GenerateTrackingNumberAction;
use App\Models\Lecturer;
use App\Spiders\FacultiesSpider;
use App\Spiders\LecturerScheduleSpider;
use App\Spiders\LecturersSpider;
use App\Spiders\MajorSchedulesSpider;
use App\Spiders\MajorsSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class ScrapDataCommand extends Command
{
    protected $signature = 'scrap:data';

    protected $description = 'Run spiders to scrap all data';

    public function handle(GenerateTrackingNumberAction $action): int
    {
        try {
            $trackingNumberId = $action->execute();

            $this->runFacultiesSpider($trackingNumberId);
            $this->runLecturersSpider($trackingNumberId);
            $this->runLecturerScheduleSpider($trackingNumberId);
            $this->runMajorsSpider($trackingNumberId);
            $this->runMajorSchedulesSpider($trackingNumberId);
        } catch (\Throwable $e) {
            $this->warn($e);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function runFacultiesSpider(int $trackingNumberId): void
    {
        Roach::collectSpider(FacultiesSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);
    }

    private function runMajorsSpider(int $trackingNumberId): void
    {
        Roach::collectSpider(MajorsSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);
    }

    private function runMajorSchedulesSpider(int $trackingNumberId): void
    {
        Roach::collectSpider(MajorSchedulesSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);
    }

    private function runLecturersSpider(int $trackingNumberId): void
    {
        Roach::collectSpider(LecturersSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);
    }

    private function runLecturerScheduleSpider(int $trackingNumberId): void
    {
        $callback = function ($chunk) use ($trackingNumberId) {
            $context = [
                'trackingNumberId' => $trackingNumberId,
                'models' => $chunk,
            ];

            Roach::collectSpider(LecturerScheduleSpider::class, context: $context);
        };

        Lecturer::query()->chunk(100, $callback);
    }
}
