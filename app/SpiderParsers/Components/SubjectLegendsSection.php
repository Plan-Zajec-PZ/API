<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class SubjectLegendsSection
{
    protected array $legends = [];
    protected Crawler $node;

    public function __construct(
        protected Response $response,
    ) {
        $this->node = $response->filter('#prtleg + table table');
        $this->initLegends();
    }

    public function getLegends(): array
    {
        return $this->legends;
    }

    protected function initLegends(): void
    {
        $this->node->each(
            function (Crawler $node) {
                $this->legends[] = new SubjectLegend($node);
            }
        );
    }
}
