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
        $this->rowsNode = $this->extractRowsNode();
        $this->abbreviations = $this->extractAbbreviations();
        $this->names = $this->extractNames();
    }

    public function getAbbreviations(): array
    {
        return $this->abbreviations;
    }

    public function getNames(): array
    {
        return $this->names;
    }

    protected function extractRowsNode(): Crawler
    {
        return $this->response
            ->filter('#prtleg > table.TabPlan tr');
    }

    protected function extractAbbreviations(): array
    {
        return $this->rowsNode
            ->filter('td:nth-child(2n+1)')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }

    protected function extractNames(): array
    {
        return $this->rowsNode
            ->filter('td:nth-child(2n)')
            ->each(
                fn (Crawler $node) => $node->text()
            );
    }
}
