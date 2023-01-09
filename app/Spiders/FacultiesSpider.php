<?php

namespace App\Spiders;

use App\ItemProcessors\FacultiesPersister;
use App\SpiderMiddlewares\ResponseEncodingCorrection;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class FacultiesSpider extends BasicSpider
{
    public int $concurrency = 2;
    public int $requestDelay = 1;

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        ResponseEncodingCorrection::class,
    ];

    public array $itemProcessors = [
        FacultiesPersister::class,
    ];

    public array $extensions = [
        StatsCollectorExtension::class,
    ];

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $faculties = $response->filterXPath('//div[@class="page-sidebar"]//li/a[not(contains(@href, "show_sala"))]');

        $results = $faculties->each(fn (Crawler $node) => [
            'name' => $node->text(),
            'link' => $node->link()->getUri(),
        ]);

        yield $this->item($results);
    }

    protected function initialRequests(): array
    {
        $url = config('roach.base_url');

        return [
            new Request('GET', $url, [$this, 'parse']),
        ];
    }
}
