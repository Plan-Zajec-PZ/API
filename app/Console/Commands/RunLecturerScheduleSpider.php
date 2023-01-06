<?php

namespace App\Console\Commands;

use App\Models\Lecturer;
use App\Spiders\LecturerScheduleSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class RunLecturerScheduleSpider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:lecturer {lecturer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run spider to scrap specific lecturer with related schedule';

    /**
     * Execute the console command.
     *
     * @return int
     */
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
