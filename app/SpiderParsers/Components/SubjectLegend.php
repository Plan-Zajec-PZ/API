<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class SubjectLegend
{
    protected array $rows;

    public function __construct(
        protected string $name
    ){
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addRow($row):void{
        $this->rows[] = $row;
    }
}
