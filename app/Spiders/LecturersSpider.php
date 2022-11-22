<?php

namespace App\Spiders;

use App\ItemProcessors\LecturersPersister;
use App\Models\Faculty;
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
    ];

    public array $itemProcessors = [
        LecturersPersister::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $faculties = $response->filterXPath('//ul[@data-role="accordion"]/li');

        $results = $faculties->each(fn (Crawler $faculty) => [
            'facultyName' => $this->getFacultyName($faculty),
            'lecturers' => $this->getLecturers($faculty),
        ]);

        yield $this->item($results);
    }

    protected function initialRequests(): array
    {
        $facultyModel = Faculty::query()->firstWhere('name', 'Szukaj pracownika');
        $url = $facultyModel->link ?? '';

        return [
            new Request('GET', $url, [$this, 'parse']),
        ];
    }

    private function getFacultyName(Crawler $faculty): string
    {
        return $faculty->filterXPath('//a[@href="#"]')->text();
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
