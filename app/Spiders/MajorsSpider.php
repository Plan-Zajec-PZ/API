<?php

namespace App\Spiders;

use App\ItemProcessors\MajorsPersister;
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

class MajorsSpider extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $itemProcessors = [
        MajorsPersister::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
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
            ->map(function ($faculty) {
                return new Request(
                    'GET',
                    $faculty->link,
                    [$this, 'parse']
                );
            });

        return $requests->toArray();
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $majorNodes = $response->filterXPath('//ul[@data-role="accordion"]/li');

        $item = $majorNodes->each(
            fn (Crawler $majorNode) => $this->createItemFromMajorNode($response->getUri(), $majorNode)
        );

        yield $this->item($item);
    }

    private function createItemFromMajorNode(String $facultyPageLink, Crawler $majorNode): array
    {
        return [
            'major_name' => $majorNode->filterXPath('//a')->text(),
            'major_specializations' => $this->getSpecializationsFromFacultyPage($majorNode),
            'faculty_page_link' => $facultyPageLink,
        ];
    }

    private function getSpecializationsFromFacultyPage(Crawler $major): array
    {
        $specializations = $major->filterXPath('//div/a[not(contains(@href, "checkSpecjalnoscStac"))]');

        return $specializations->each(fn (Crawler $specializations) => [
            'name' => $specializations->text(),
            'link' => $specializations->nextAll()->first()->link()->getUri(),
        ]);
    }
}
