<?php

namespace App\ItemProcessors;

use App\Models\AbbreviationLegend;
use App\Models\Specialization;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class AbbreviationLegendsPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $abbreviationLegendItem = $item['abbreviationLegend'];
        $specializationPageLink = $item['specialization_page_link'];

        $this->persistAbbreviationLegend($abbreviationLegendItem, $specializationPageLink);

        return $item;
    }

    public function persistAbbreviationLegend(array $abbreviationLegendItem, string $specializationPageLink): void
    {
        foreach ($abbreviationLegendItem as $abbreviation => $name) {
            $abbreviationLegend = AbbreviationLegend::firstOrNew(['abbreviation' => $abbreviation, 'fullname' => $name]);

            $specialization = Specialization::firstWhere([
                'link' => $specializationPageLink,
            ]);

            $abbreviationLegend->specialization()->associate($specialization);

            $abbreviationLegend->save();
        }
    }
}

