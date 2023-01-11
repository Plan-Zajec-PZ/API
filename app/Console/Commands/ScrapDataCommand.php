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
        $this->line('');
        $this->line('Started scraping faculties...');

        Roach::collectSpider(FacultiesSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);

        $this->info('Success!');
        $this->line('');
    }

    private function runMajorsSpider(int $trackingNumberId): void
    {
        $this->line('Started scraping majors...');

        Roach::collectSpider(MajorsSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);

        $this->info('Success!');
        $this->line('');
    }

    private function runMajorSchedulesSpider(int $trackingNumberId): void
    {
        $this->line('Started scraping majors schedules...');

        Roach::collectSpider(MajorSchedulesSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);

        $this->info('Success!');
        $this->line('');
    }

    private function runLecturersSpider(int $trackingNumberId): void
    {
        $this->line('Started scraping lecturers...');

        Roach::collectSpider(LecturersSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
        ]);

        $this->info('Success!');
        $this->line('');
    }

    private function runLecturerScheduleSpider(int $trackingNumberId): void
    {
        $this->line('Started scraping lecturers schedules...');

        Roach::collectSpider(LecturerScheduleSpider::class, context: [
            'trackingNumberId' => $trackingNumberId,
            'models' => Lecturer::all(),
        ]);

        $this->info('Success!');
        $this->line('');
    }
}
