<?php

namespace App\SpiderParsers\Components;

use Symfony\Component\DomCrawler\Crawler;

class SubjectLegend
{
    protected string $name;
    protected array $rows;

    public function __construct(
        protected Crawler $node
    ) {
        $this->name = $this->extractName();
        $this->rows = $this->extractRows();
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function extractName(): string
    {
        return $this->node->filter('tr:first-of-type > th')->text();
    }

    protected function extractRows(): array
    {
        $content = $this->node->filter('tr:not(:nth-child(2)) > td')->each(
            fn (Crawler $node) => $node->text()
        );

        $rows = [];
        foreach (array_chunk($content, 3) as $row) {
            $rows[] = $row;
        }

        return $rows;
    }
}
