<?php

namespace App\SpiderParsers\Components;

use Symfony\Component\DomCrawler\Crawler;

class ScheduleDay
{
    protected string $date;
    protected array $hours;
    protected array $rows;
    protected Crawler $hoursNode;

    public function __construct(
        protected Crawler $node,
        protected array $groups
    ) {
        $this->date = $this->extractDate();
        $this->hoursNode = $this->extractHoursNode();
        $this->hours = $this->extractHours();
        $this->rows = $this->extractRows();
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    protected function extractDate(): string
    {
        return $this->node->text();
    }

    protected function extractHoursNode(): Crawler
    {
        return $this->node->closest('tr')->siblings()->children('td.godzina');
    }

    protected function extractHours(): array
    {
        return $this->hoursNode->each(
            fn (Crawler $hour) => $hour->text()
        );
    }

    protected function extractRows(): array
    {
        $subjects = $this->hoursNode->each(
            fn (Crawler $hour) => $hour->nextAll()->each(
                fn (Crawler $tr) => $tr->text()
            )
        );

        foreach ($subjects as &$value) {
            $value = array_chunk($value, 3);
        }

        $result = [];
        $schedule = array_values($subjects);
        foreach ($this->groups as $index => $group) {
            $groupSchedule = array_column($schedule, $index);
            $result[$group] = $this->addHoursToSchedule($groupSchedule);
        }

        return $result;
    }

    private function addHoursToSchedule(array $schedule): array
    {
        $i = 0;
        return array_map(
            function ($item) use (&$i) {
                array_unshift($item, $this->hours[$i]);
                $i++;
                return $item;
            },
            $schedule
        );
    }
}
