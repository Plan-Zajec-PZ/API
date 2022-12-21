<?php

namespace App\ItemProcessors;

use App\Models\Specialization;
use App\Models\SubjectLegend;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class SubjectLegendsPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $subjectLegends = $item['subjectLegends'];
        $specializationPageLink = $item['specialization_page_link'];

        $this->persistSubjectLegends($subjectLegends, $specializationPageLink);
        return $item;
    }

    public function persistSubjectLegends(array $subjectLegends, string $specializationPageLink): void
    {
        if(!empty($subjectLegends)) {
            $specialization = Specialization::firstWhere([
                'link' => $specializationPageLink,
            ]);

            $subjectLegend = SubjectLegend::firstOrNew(['content' => json_encode($subjectLegends), 'specialization_id' => $specialization->id]);

            $subjectLegend->specialization()->associate($specialization);

            $subjectLegend->save();
        }
    }
}
