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

    private function getFacultyByPageLink($link): Faculty
    {
        return Faculty::firstWhere('link', $link);
    }

    private function persistMajor($majorItem, $faculty): Major
    {
        $major = Major::firstOrNew(['name' => $majorItem['major_name']]);
        $major->faculty()->associate($faculty);

        $major->save();

        return $major;
    }

    private function persistSpecializations($majorItemSpecializations, $major): void
    {
        foreach ($majorItemSpecializations as $majorItemSpecialization) {
            $specialization = Specialization::firstOrNew([
                'name' => $majorItemSpecialization['name'],
                'link' => $majorItemSpecialization['link'],
            ]);
            $specialization->major()->associate($major);

            $specialization->save();
        }
    }
}