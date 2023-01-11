<?php

namespace App\ItemProcessors;

use App\Models\Major;
use App\Models\Faculty;
use App\Models\Specialization;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class MajorsPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        foreach ($item->all() as $majorItem) {
            $faculty = $this->getFacultyByPageLink($majorItem['faculty_page_link']);
            $majorItemSpecializations = $majorItem['major_specializations'];

            $major = $this->persistMajor($majorItem, $faculty);
            $this->persistSpecializations($majorItemSpecializations, $major);
        }

        return $item;
    }

    private function getFacultyByPageLink(string $link): Faculty
    {
        return Faculty::query()->firstWhere('link', $link);
    }

    private function persistMajor(array $majorItem, Faculty $faculty): Major
    {
        $major = Major::query()->firstOrNew(['name' => $majorItem['major_name']]);
        $major->faculty()->associate($faculty);

        $major->tracking_number_id = $majorItem['tracking_number_id'];
        $major->save();

        return $major;
    }

    private function persistSpecializations(array $majorItemSpecializations, Major $major): void
    {
        foreach ($majorItemSpecializations as $majorItemSpecialization) {
            $specialization = Specialization::query()
                ->firstOrNew(
                    ['link' => $majorItemSpecialization['link']],
                    ['name' => $majorItemSpecialization['name']],
                );
            $specialization->major()->associate($major);

            $specialization->tracking_number_id = $major->tracking_number_id;
            $specialization->save();
        }
    }
}
