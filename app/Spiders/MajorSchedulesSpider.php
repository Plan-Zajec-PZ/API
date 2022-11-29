<?php

namespace App\Spiders;

use App\ItemProcessors\MajorsSchedulesPersister;
use App\Models\Specialization;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class MajorSchedulesSpider extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $itemProcessors = [
        MajorsSchedulesPersister::class
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
        $requests = Specialization::all()
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
        $scheduleTable = $this->getScheduleTable($response);

        $groups = $this->getGroupsFromSchedule($scheduleTable);
        $days = $scheduleTable->filter('tr > td.nazwaDnia');

        $dailySchedules = $days->each(fn (Crawler $day) => [
            'day' => $day->text(),
            'schedule' => $this->getDailySchedule($day, $groups),
        ]);

        $result['specialization_page_link'] = $response->getUri();
        $result['groups'] = $groups;
        $result['dailySchedules'] = $dailySchedules;


        yield $this->item($result);
    }

    private function getScheduleTable(Response $response): Crawler
    {
        return $response
            ->filter('table.TabPlan:first-of-type ')
            ->first();
    }

    private function getGroupsFromSchedule(Crawler $scheduleTable): array
    {
        return $scheduleTable
            ->filter('tr:first-of-type > td.nazwaSpecjalnosci')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    private function getDailySchedule(Crawler $day, $groups): array
    {
        $hoursNode = $day->closest('tr')->siblings()->children('td.godzina');
        $hours = $hoursNode->each(fn (Crawler $node) => $node->text());
        $subjects = $hoursNode->each(fn (Crawler $hour) => $hour->nextAll()->each(fn (Crawler $tr) => $tr->text()));

        foreach ($subjects as &$value) {
            $value = array_chunk($value, 3);
            $value = array_combine($groups, $value);
        }

        return array_combine($hours, $subjects);
    }
}
