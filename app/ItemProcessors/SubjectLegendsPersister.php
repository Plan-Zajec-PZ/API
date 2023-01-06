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

    private function persistSubjectLegends(array $subjectLegends, string $specializationPageLink): void
    {
        if (!empty($subjectLegends)) {
            $specialization = Specialization::query()->firstWhere([
                'link' => $specializationPageLink,
            ]);
            foreach ($subjectLegends as $name => $legend) {
                $subjectLegend = SubjectLegend::query()
                    ->firstOrNew([
                        'name' => $name,
                        'content' => json_encode($legend),
                        'specialization_id' => $specialization->id,
                    ]);

                $subjectLegend->specialization()->associate($specialization);

                $subjectLegend->save();
            }
        }
    }
}
