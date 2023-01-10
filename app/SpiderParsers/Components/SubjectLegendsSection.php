<?php

namespace App\SpiderParsers\Components;

use RoachPHP\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class SubjectLegendsSection
{
    protected array $legends = [];

    public function __construct(
        protected Response $response,
    ) {
        $this->create();
    }

    protected function create(): void
    {
        $subjectLegendTables = $this->response->filter('#prtleg + table table');

        $subjectLegendTables->each(
            function (Crawler $node) use (&$subjectLegendTableNames) {
                $name = $node->filter('tr:first-of-type > th')->text();

                $legend = new SubjectLegend($name);

                $content = $node->filter('tr:not(:nth-child(2)) > td')->each(
                    fn (Crawler $node) => $node->text()
                );

                foreach (array_chunk($content, 3) as $row) {
                    $legend->addRow($row);
                }

                $this->legends[] = $legend;
            }
        );
    }

    public function getLegends(): array
    {
        return $this->legends;
    }
}
