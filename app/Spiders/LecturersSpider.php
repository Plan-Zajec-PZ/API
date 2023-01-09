<?php

namespace App\Spiders;

use App\ItemProcessors\LecturersPersister;
use App\Models\Faculty;
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

class LecturersSpider extends BasicSpider
{
    public int $concurrency = 2;
    public int $requestDelay = 1;

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        ResponseEncodingCorrection::class,
    ];

    public array $itemProcessors = [
        LecturersPersister::class,
    ];

    public array $extensions = [
        StatsCollectorExtension::class,
    ];

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $faculties = $response->filterXPath('//ul[@data-role="accordion"]/li');

        $results = $faculties->each(fn (Crawler $faculty) => [
            'facultyName' => $faculty->filterXPath('//a[@href="#"]')->text(),
            'lecturers' => $this->getLecturers($faculty),
        ]);

        yield $this->item($results);
    }

    protected function initialRequests(): array
    {
        $facultyModel = Faculty::query()
            ->where('name', 'Szukaj pracownika')
            ->firstOrFail();

        return [
            new Request('GET', $facultyModel->link, [$this, 'parse']),
        ];
    }

    private function getLecturers(Crawler $faculty): array
    {
        $lecturers = $faculty->filterXPath('//div/a[contains(@href, "checkNauczycielAll")]');

        return $lecturers->each(fn (Crawler $lecturer) => [
            'name' => $lecturer->text(),
            'link' => $lecturer->nextAll()->first()->link()->getUri(),
        ]);
    }
}
