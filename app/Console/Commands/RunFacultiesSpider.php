<?php

namespace App\Console\Commands;

use App\Spiders\FacultiesSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class RunFacultiesSpider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:faculties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run spider to scrap faculties';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $overrides = new Overrides(startUrls: [
            config('roach.base_url'),
        ]);

        try {
            Roach::startSpider(FacultiesSpider::class, $overrides);
        } catch (\Throwable) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
