<?php

namespace App\Console\Commands;

use App\Spiders\MajorSchedulesSpider;
use App\Spiders\MajorsSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class RunMajorsSpider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:majors {--schedules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run spider to scrap majors or majors schedules';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $spiderClass = $this->option('schedules')
            ? MajorSchedulesSpider::class
            : MajorsSpider::class;

        try {
            Roach::startSpider($spiderClass);
        } catch (\Throwable) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
