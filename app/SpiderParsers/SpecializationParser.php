<?php

namespace App\SpiderParsers;

use App\SpiderParsers\Components\AbbreviationLegend;
use App\SpiderParsers\Components\SpecializationSchedule;
use App\SpiderParsers\Components\SubjectLegendsSection;
use Symfony\Component\DomCrawler\Crawler;

class SpecializationParser extends Parser
{
    public function parseAbbreviationLegend(): array
    {
        $abbreviationLegend = new AbbreviationLegend($this->response);

        return array_combine(
            $abbreviationLegend->getAbbreviations(),
            $abbreviationLegend->getNames()
        );
    }

    public function parseSubjectLegends(): array
    {
        $subjectLegends = new SubjectLegendsSection($this->response);

        return $subjectLegends->getLegends();
    }

    public function parseGroups()
    {
        $scheduleTableNode = $this->response
            ->filter('table.TabPlan')
            ->first();

        return $scheduleTableNode
            ->filter('tr:first-of-type > td.nazwaSpecjalnosci')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    public function parseSchedule()
    {
        $schedule = new SpecializationSchedule($this->response);

        $schedule->create();

        $days = [];
        foreach ($schedule->getDays() as $day){
            $days[] = [
                'day' => $day->getDate(),
                'schedule' => $day->getRows(),
            ];
        }

        return $this->createGroupScheduleFromDailySchedule($days, $schedule->getGroups());
    }

    private function createGroupScheduleFromDailySchedule(array $dailySchedules, array $groups): array
    {
        $result = [];
        $days = array_column($dailySchedules, 'day');
        $schedules = array_column($dailySchedules, 'schedule');

        foreach ($groups as $group) {
            $groupSchedule = array_column($schedules, $group);

            $daysWithKey = $this->addKeyToArray($days, 'day');
            $groupScheduleWithKey = $this->addKeyToArray($groupSchedule, 'rows');

            $result[$group] = (array_merge_recursive_distinct($daysWithKey, $groupScheduleWithKey));
        }

        return $result;
    }

    private function addKeyToArray(array $array, string $key): array
    {
        return array_map(fn ($item) => [$key => $item], $array);
    }
}
