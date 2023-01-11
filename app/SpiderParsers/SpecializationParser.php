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
        $this->abbreviationLegend = new AbbreviationLegend(
            $response->filter('#prtleg > table.TabPlan tr'
            ));
        $this->subjectLegends = new SubjectLegendsSection($response);
        $this->schedule = new SpecializationSchedule($response);
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
            $days[] = ['day' => $day->getDate()];
            $schedules[] = $day->getRows();
        }

        $groups = $this->schedule->getGroups();
        $result = [];

        foreach ($groups as $group) {
            $groupSchedule = array_column($schedules, $group);

            $groupScheduleWithKey = array_map(
                fn($item) => ['rows' => $item],
                $groupSchedule
            );

            $result[$group] = array_merge_recursive_distinct($days, $groupScheduleWithKey);
        }

        return $result;
    }
}
