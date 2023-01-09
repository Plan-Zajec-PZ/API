<?php

namespace App\SpiderParsers;

use App\SpiderParsers\Components\AbbreviationLegend;
use App\SpiderParsers\Components\SubjectLegendsSection;
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

    public function parseSubjectLegends(): array
    {
        $subjectLegends = new SubjectLegendsSection($this->response);

        return $subjectLegends->getLegends();
    }

}
