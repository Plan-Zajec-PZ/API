<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class AbbreviationLegend
{
    protected Crawler $rowsNode;
    protected array $abbreviations;
    protected array $names;

    public function __construct(
        protected Response $response,
    ) {
        $this->setProperties();
    }

    public function getAbbreviations(): array
    {
        return $this->abbreviations;
    }

    public function getNames(): array
    {
        return $this->names;
    }

    protected function setProperties(): void
    {
        $this->setRowsNode();
        $this->setAbbreviations();
        $this->setNames();
    }

    protected function setRowsNode(): void
    {
        $this->rowsNode = $this->response
            ->filter('#prtleg > table.TabPlan tr');
    }

    protected function setAbbreviations(): void
    {
        $this->abbreviations = $this->rowsNode
            ->filter('td:nth-child(2n+1)')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    protected function setNames(): void
    {
        $this->names = $this->rowsNode
            ->filter('td:nth-child(2n)')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }
}
