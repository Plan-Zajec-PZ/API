<?php

namespace App\Spiders;

use App\Exceptions\ModelWithoutLink;
use App\ItemProcessors\LegendPersister;
use App\ItemProcessors\SchedulePersister;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class LecturerScheduleSpider extends BasicSpider
{
    public int $concurrency = 2;

    public int $requestDelay = 1;

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $itemProcessors = [
        SchedulePersister::class,
        LegendPersister::class,
    ];

    public array $extensions = [
    ];

    protected function initialRequests(): array
    {
        $models = $this->context['models'];
        $requests = [];

        foreach ($models as $model) {
            $url = $model->link ?? throw new ModelWithoutLink();
            $requests[] = new Request('GET', $url, [$this, 'parse']);
        }

        return $requests;
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $planTables = $this->getPlanTables($response);

        $schedules = $planTables->each(
            fn (Crawler $planTable) => $this->getSchedules($planTable)
        );

        yield $this->item([
            'schedule' => array_merge(...$schedules),
            'legend' => $this->getLegend($response),
            'initiatorUri' => $response->getUri(),
            'tracking_number_id' => $this->context['trackingNumberId'],
        ]);
    }

    private function getPlanTables(Response $response): Crawler
    {
        return $response->filterXPath('//table[@class="TabPlan"][position()!=last()]');
    }

    private function getSchedules(Crawler $planTable): array
    {
        $days = $this->getDays($planTable);

        $schedules = $days->each(
            fn (Crawler $day, int $index) => [
                'date' => $day->text(),
                'dailySchedule' => $this->getDailySchedule($planTable, $days->count(), $index)
            ]
        );

        return $schedules;
    }

    private function getDays(Crawler $table): Crawler
    {
        return $table->filterXPath('//tr[position()=1]//th[position()!=1 and position()!=7]');
    }

    private function getDailySchedule(Crawler $planTable, int $numberOfDays, int $index): array
    {
        $forWeekend = $index >= $numberOfDays-2;

        $hours = array_map(
            fn ($hours) => ['hours' => $hours],
            $this->extractHours($planTable, $forWeekend)
        );

        $lessons = $this->extractLessons($planTable, $index);

        return array_merge_recursive_distinct($hours, $lessons);
    }

    private function extractHours(Crawler $table, bool $forWeekend = false): array
    {
        $position = $forWeekend ? 2 : 1;

        $hours = $table->filterXPath("//tr[position()!=1]//th[@class='x' and position()=${position}]");

        return $hours->extract(['_text']);
    }

    private function extractLessons(Crawler $table, int $index): array
    {
        $lessons = $this->getLessons($table, $index);

        return $lessons->each(
            fn (Crawler $lesson) => $this->buildLessonsArray($lesson)
        );
    }

    private function getLessons(Crawler $table, int $index): Crawler
    {
        $adjustedIndex = ++$index;

        return $table->filterXPath("//tr[position()!=1]//td[@class='x' and position()=${adjustedIndex}]");
    }

    private function buildLessonsArray(Crawler $lesson): array
    {
        return [
            'class' => $this->getClass($lesson),
            'classroom' => $this->getClassroom($lesson),
            'subject' => $this->getSubject($lesson),
        ];
    }

    private function getClass(Crawler $lesson): string
    {
        return $lesson->filterXPath('//div[@class="blok"]//text()[1]')->text('');
    }

    private function getClassroom(Crawler $lesson): string
    {
        return $lesson->filterXPath('//div[@class="blok"]//div[@class="liniaPodzialowa"]')->text('');
    }

    private function getSubject(Crawler $lesson): string
    {
        return $lesson->filterXPath('//div[@class="blok"]//text()[2]')->text('');
    }

    private function getLegend(Response $response): array
    {
        $rows = $response->filterXPath('//table[@class="TabPlan"][position()=last()]//tr[position()>1]');

        return $rows->each(fn (Crawler $row) => [
            'abbreviation' => $row->filterXPath('//td[position()=1]')->text(),
            'fullname' => $row->filterXPath('//td[position()=2]')->text(),
        ]);
    }
}
