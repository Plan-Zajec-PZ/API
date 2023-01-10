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
        $this->create();
    }

    public function create(): void
    {
        $this->extractDate();
        $this->extractHours();
        $this->extractRows();
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getRows()
    {
        return $this->rows;
    }

    protected function extractDate()
    {
        $this->date = $this->node->text();
    }

    protected function extractHours()
    {
        $this->hoursNode = $this->node->closest('tr')->siblings()->children('td.godzina');

        $this->hours = $this->hoursNode->each(
            fn (Crawler $hour) => $hour->text()
        );
    }

    protected function extractRows()
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

        $this->rows = $result;
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
