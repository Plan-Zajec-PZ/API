<?php

namespace App\Console\Commands;

use App\Spiders\LecturersSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class RunLecturersSpider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:lecturers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run spider to scrap lecturers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            Roach::startSpider(LecturersSpider::class);
        } catch (\Throwable) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
