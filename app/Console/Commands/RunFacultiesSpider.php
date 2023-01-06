<?php

namespace App\Console\Commands;

use App\Spiders\FacultiesSpider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class RunFacultiesSpider extends Command implements Isolatable
{
    protected $signature = 'scrap:faculties';

    protected $description = 'Run spider to scrap faculties';

    public function isolationLockExpiresAt(): \DateTimeInterface|\DateInterval
    {
        return now()->addMinutes(5);
    }

    public function handle(): int
    {
        $overrides = new Overrides(startUrls: [
            config('roach.base_url'),
        ]);

        try {
            Roach::startSpider(FacultiesSpider::class, $overrides);
        } catch (\Throwable) {
            $this->error('Scraper failed!');

            return Command::FAILURE;
        }

        $this->info('Success!');

        return Command::SUCCESS;
    }
}
