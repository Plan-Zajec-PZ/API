<?php

namespace App\SpiderParsers\Components;

use Symfony\Component\DomCrawler\Crawler;

class SubjectLegend
{
    protected array $rows;
    protected string $name;

    public function __construct(
        protected Crawler $node
    ) {
        $this->initName();
        $this->initRows();
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function initName(): void
    {
        $this->name = $this->node->filter('tr:first-of-type > th')->text();
    }

    protected function initRows(): void
    {
        $content = $this->node->filter('tr:not(:nth-child(2)) > td')->each(
            fn (Crawler $node) => $node->text()
        );

        foreach (array_chunk($content, 3) as $row) {
            $this->rows[] = $row;
        }
    }
}
