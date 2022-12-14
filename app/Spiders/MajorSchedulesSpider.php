<?php

namespace App\Spiders;

use App\ItemProcessors\AbbreviationLegendsPersister;
use App\ItemProcessors\GroupsPersister;
use App\ItemProcessors\MajorsSchedulesPersister;
use App\ItemProcessors\SubjectLegendsPersister;
use App\Models\Specialization;
use App\SpiderParsers\SpecializationParser;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class MajorSchedulesSpider extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $itemProcessors = [
        GroupsPersister::class,
        MajorsSchedulesPersister::class,
        AbbreviationLegendsPersister::class,
        SubjectLegendsPersister::class,
    ];

    public array $extensions = [
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /** @return Request[] */
    protected function initialRequests(): array
    {
        $requests = Specialization::all()
            ->map(
                fn ($specialization) => new Request(
                    'GET',
                    $specialization->link,
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
        $parser = new SpecializationParser($response);

        yield $this->item([
            'specialization_page_link' => $response->getUri(),
            'abbreviationLegend' => $parser->parseAbbreviationLegend(),
            'subjectLegends' => $parser->parseSubjectLegends(),
            'groups' => $parser->parseGroups(),
            'dailySchedules' => $parser->parseSchedule(),
            'tracking_number_id' => $this->context['trackingNumberId'],
        ]);
    }
}
