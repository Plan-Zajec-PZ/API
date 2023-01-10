<?php

namespace App\SpiderParsers\Components;

use Symfony\Component\DomCrawler\Crawler;

class SpecializationSchedule extends Schedule
{
    public const TABLE_SELECTOR = 'table.TabPlan';

    private array $days;
    private array $groups;

    public function __construct($response)
    {
        parent::__construct($response, self::TABLE_SELECTOR);
    }

    public function create(): void
    {
        $this->extractGroups();
        $this->extractDays();
    }

    public function getDays()
    {
        return $this->days;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    protected function extractGroups(): void
    {
        $this->groups = $this->tableNode
            ->filter('tr:first-of-type > td.nazwaSpecjalnosci')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    protected function extractDays(): void
    {
        $this->days = $this->tableNode->filter('tr > td.nazwaDnia')->each(
            fn (Crawler $node) => new ScheduleDay($node, $this->groups)
        );
    }
}
