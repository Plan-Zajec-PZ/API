<?php

namespace App\Spiders;

use App\ItemProcessors\GroupsPersister;
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
        GroupsPersister::class,
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
        $scheduleTableNode = $this->getScheduleTable($response);

        $dayNodes = $this->getDayNodesFromScheduleTableNode($scheduleTableNode);
        $groups = $this->getGroupsFromScheduleTableNode($scheduleTableNode);

        $dailySchedules = $dayNodes->each(
            fn (Crawler $node) => [
                'day' => $node->text(),
                'schedule' => $this->getDailySchedule($node, $groups),
            ]
        );

        yield $this->item([
            'specialization_page_link' => $response->getUri(),
            'groups' => $groups,
            'dailySchedules' => $this->createGroupScheduleFromDailySchedule($dailySchedules, $groups),
        ]);
    }

    private function getScheduleTable(Response $response): Crawler
    {
        return $response
            ->filter('table.TabPlan')
            ->first();
    }

    private function getDayNodesFromScheduleTableNode(Crawler $scheduleTableNode): Crawler
    {
        return $scheduleTableNode->filter('tr > td.nazwaDnia');
    }

    private function getGroupsFromScheduleTableNode(Crawler $scheduleTableNode): array
    {
        return $scheduleTableNode
            ->filter('tr:first-of-type > td.nazwaSpecjalnosci')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    private function getDailySchedule(Crawler $dayNode, $groups): array
    {
        $hoursNode = $dayNode->closest('tr')->siblings()->children('td.godzina');

        $hours = $hoursNode->each(
            fn (Crawler $hour) => $hour->text()
        );

        $subjects = $hoursNode->each(
            fn (Crawler $hour) => $hour->nextAll()->each(
                fn (Crawler $tr) => $tr->text()
            )
        );

        foreach ($subjects as &$value) {
            $value = array_chunk($value, 3);
        }

        $result = [];
        $schedule = array_values($subjects);

        foreach ($groups as $index => $group) {
            $groupSchedule = array_column($schedule, $index);
            $result[$group] = array_combine(
                $hours,
                $groupSchedule
            );
        }

        return $result;
    }

    private function createGroupScheduleFromDailySchedule($dailySchedules, $groups): array
    {
        $result = [];
        $days = array_column($dailySchedules, 'day');
        $schedules = array_column($dailySchedules, 'schedule');
        foreach ($groups as $group) {
            $groupSchedule = array_column($schedules, $group);
            $result[$group] = array_combine(
                $days,
                $groupSchedule,
            );
        }
        return $result;
    }
}
