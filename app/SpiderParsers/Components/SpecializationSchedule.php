<?php

namespace App\SpiderParsers\Components;

use Symfony\Component\DomCrawler\Crawler;

class SpecializationSchedule extends Schedule
{
    public const TABLE_SELECTOR = 'table.TabPlan';

    private array $groups;
    private array $days;

    public function __construct($response)
    {
        parent::__construct($response, self::TABLE_SELECTOR);

        $this->groups = $this->extractGroups();
        $this->days = $this->extractDays();
    }

    public function getDays(): array
    {
        return $this->days;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    protected function extractGroups(): array
    {
        return $this->tableNode
            ->filter('tr:first-of-type > td.nazwaSpecjalnosci')
            ->each(
                fn(Crawler $node) => $node->text()
            );
    }

    protected function extractDays(): array
    {
        return $this->tableNode->filter('tr > td.nazwaDnia')->each(
            fn(Crawler $node) => new ScheduleDay($node, $this->groups)
        );
    }
}
