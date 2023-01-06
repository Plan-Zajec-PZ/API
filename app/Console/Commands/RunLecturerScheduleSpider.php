<?php

namespace App\Console\Commands;

use App\Models\Lecturer;
use App\Spiders\LecturerScheduleSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class RunLecturerScheduleSpider extends Command
{
    protected $signature = 'scrap:lecturer {lecturer}';

    protected $description = 'Run spider to scrap specific lecturer with related schedule';

    public function isolationLockExpiresAt(): \DateTimeInterface|\DateInterval
    {
        return now()->addMinutes(5);
    }

    public function handle(): int
    {
        try {
            $lecturer = $this->findLecturer();

            Roach::startSpider(
                LecturerScheduleSpider::class,
                context: $this->buildContext($lecturer)
            );
        } catch (\Throwable) {
            $this->error('Scraper failed!');

            return Command::FAILURE;
        }

        $this->info('Success!');

        return Command::SUCCESS;
    }

    private function findLecturer(): Lecturer
    {
        $lecturerId = $this->argument('lecturer');

        return Lecturer::query()->findOrFail($lecturerId);
    }

    private function buildContext(Lecturer $lecturer): array
    {
        return [
            'models' => [$lecturer],
        ];
    }
}
