<?php

namespace App\ItemProcessors;

use App\Models\Faculty;;

use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class LecturersPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $faculties = $item->all();

        foreach ($faculties as $faculty) {
            $faculty = $this->getFacultyModel($faculty);

            $this->persistLecturers(
                $faculty['lecturers'],
                $faculty
            );
        }

        return $item;
    }

    private function getFacultyModel(string $facultyName): Faculty
    {
        return Faculty::firstWhere('name', $facultyName);
    }

    private function persistLecturers(array $lecturers, Faculty $faculty): void
    {
        foreach ($lecturers as $lecturer) {
            $faculty->lecturers()->updateOrCreate($lecturer);
        }
    }
}
