<?php

namespace App\Console\Commands;

use App\Spiders\LecturersSpider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use RoachPHP\Roach;

class RunLecturersSpider extends Command implements Isolatable
{
    protected $signature = 'scrap:lecturers';

    protected $description = 'Run spider to scrap lecturers';

    public function isolationLockExpiresAt(): \DateTimeInterface|\DateInterval
    {
        return now()->addMinutes(5);
    }

    public function handle(): int
    {
        try {
            Roach::startSpider(LecturersSpider::class);
        } catch (\Throwable) {
            $this->error('Scraper failed!');

            return Command::FAILURE;
        }

        $this->info('Success!');

        return Command::SUCCESS;
    }
}
