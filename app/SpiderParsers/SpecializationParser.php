<?php

namespace App\SpiderParsers;

use App\SpiderParsers\Components\AbbreviationLegend;
use RoachPHP\Http\Response;

class SpecializationParser extends Parser
{
    public function parseAbbreviationLegend(): array
    {
        $abbreviationLegend = new AbbreviationLegend($this->response);

        return array_combine(
            $abbreviationLegend->getAbbreviations(),
            $abbreviationLegend->getNames()
        );
    }
}
