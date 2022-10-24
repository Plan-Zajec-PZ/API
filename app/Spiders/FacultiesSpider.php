<?php

namespace App\Spiders;

use App\ItemProcessors\FacultiesPersister;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class FacultiesSpider extends BasicSpider
{
    public array $startUrls = [
        'http://www.plan.pwsz.legnica.edu.pl',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $itemProcessors = [
        FacultiesPersister::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $faculties = $response->filterXPath('//div[@class="page-sidebar"]//li/a[text()!="Sprawd? obci??enie sali"]');

        $results = $faculties->each(fn (Crawler $node) => [
            'name' => $node->text(),
            'link' => $node->link()->getUri(),
        ]);

        yield $this->item($results);
    }
}
