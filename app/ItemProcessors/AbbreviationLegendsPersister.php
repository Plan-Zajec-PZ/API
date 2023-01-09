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

    private function persistAbbreviationLegend(array $abbreviationLegendItem, string $specializationPageLink): void
    {
        foreach ($abbreviationLegendItem as $abbreviation => $name) {
            $specialization = Specialization::query()
                ->firstWhere(['link' => $specializationPageLink]);

            $abbreviationLegend = AbbreviationLegend::query()
                ->firstOrNew([
                    'abbreviation' => $abbreviation,
                    'fullname' => $name,
                    'specialization_id' => $specialization->id,
                ]);

            $abbreviationLegend->specialization()->associate($specialization);

            $abbreviationLegend->save();
        }
    }
}
