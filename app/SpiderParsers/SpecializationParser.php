<?php

namespace App\SpiderParsers;

use App\SpiderParsers\Components\AbbreviationLegend;
use App\SpiderParsers\Components\SpecializationSchedule;
use App\SpiderParsers\Components\SubjectLegendsSection;
use RoachPHP\Http\Response;

class SpecializationParser extends Parser
{
    protected AbbreviationLegend $abbreviationLegend;
    protected SubjectLegendsSection $subjectLegends;
    protected SpecializationSchedule $schedule;

    public function __construct(Response $response)
    {
        parent::__construct($response);
        $this->abbreviationLegend = new AbbreviationLegend($this->response);
        $this->subjectLegends = new SubjectLegendsSection($this->response);
        $this->schedule = new SpecializationSchedule($this->response);
    }

    public function parseAbbreviationLegend(): array
    {
        return array_combine(
            $this->abbreviationLegend->getAbbreviations(),
            $this->abbreviationLegend->getNames()
        );
    }

    public function parseSubjectLegends(): array
    {
        $results = [];

        foreach ($this->subjectLegends->getLegends() as $legend) {
            $results[$legend->getName()] = $legend->getRows();
        }

        return $results;
    }

    public function parseGroups(): array
    {
        return $this->schedule->getGroups();
    }

    public function parseSchedule(): array
    {
        $days = [];
        $schedules = [];
        foreach ($this->schedule->getDays() as $day) {
            $days[] = $day->getDate();
            $schedules[] = $day->getRows();
        }

        $groups = $this->schedule->getGroups();
        $result = [];

        foreach ($groups as $group) {
            $groupSchedule = array_column($schedules, $group);

            $daysWithKey = $this->addKeyToArray($days, 'day');
            $groupScheduleWithKey = $this->addKeyToArray($groupSchedule, 'rows');

            $result[$group] = array_merge_recursive_distinct($daysWithKey, $groupScheduleWithKey);
        }

        return $result;
    }

    private function addKeyToArray(array $array, string $key): array
    {
        return array_map(fn ($item) => [$key => $item], $array);
    }
}
