<?php

namespace App\Spiders;

use App\ItemProcessors\MajorsPersister;
use App\Models\Faculty;
use App\SpiderMiddlewares\ResponseEncodingCorrection;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class MajorsSpider extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        ResponseEncodingCorrection::class,
    ];

    public array $itemProcessors = [
        MajorsPersister::class,
    ];

    public array $extensions = [
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /** @return Request[] */
    protected function initialRequests(): array
    {
        $requests = Faculty::query()
            ->whereNot('name', "Szukaj pracownika")
            ->get()
            ->map(
                fn ($faculty) => new Request(
                    'GET',
                    $faculty->link,
                    [$this, 'parse']
                )
            );

        return $requests->toArray();
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $majorNodes = $response->filterXPath('//ul[@data-role="accordion"]/li');

        $item = $majorNodes->each(
            fn (Crawler $majorNode) => [
                'major_name' => $majorNode->filterXPath('//a')->text(),
                'major_specializations' => $this->getSpecializationsFromFacultyPage($majorNode),
                'faculty_page_link' => $response->getUri(),
                'tracking_number_id' => $this->context['trackingNumberId'],
            ]
        );

        yield $this->item($item);
    }

    private function getSpecializationsFromFacultyPage(Crawler $major): array
    {
        $specializationNodes = $major->filterXPath('//div/a[not(contains(@href, "checkSpecjalnoscStac"))]');

        return $specializationNodes->each(
            fn (Crawler $node) => [
                'name' => $node->text(),
                'link' => $node->nextAll()->first()->link()->getUri(),
            ]
        );
    }
}
