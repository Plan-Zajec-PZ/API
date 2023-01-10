<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class Schedule
{
    protected Crawler $tableNode;

    public function __construct(
        protected Response $response,
        protected string $tableSelector,
    ){
        $this->createTableNode();
    }

    protected function createTableNode(): void
    {
        $this->tableNode = $this->response
            ->filter($this->tableSelector)
            ->first();
    }

}
