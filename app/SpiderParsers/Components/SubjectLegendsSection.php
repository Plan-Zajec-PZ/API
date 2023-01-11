<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class SubjectLegendsSection
{
    protected array $legends = [];

    public function __construct(
        protected Crawler $node
    ) {
        $this->legends = $this->extractLegends();
    }

    public function getLegends(): array
    {
        return $this->legends;
    }

    protected function extractLegends(): array
    {
        $legends = [];

        $this->node->each(
            function (Crawler $node) use (&$legends) {
                $legends[] = new SubjectLegend($node);
            }
        );

        return $legends;
    }
}
