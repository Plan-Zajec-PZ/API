<?php

namespace App\ItemProcessors;

use App\Models\Faculty;

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
            $facultyModel = $this->getFacultyModel($faculty['facultyName']);

            $this->persistLecturers(
                $faculty['lecturers'],
                $facultyModel
            );
        }

        return $item;
    }

    private function getFacultyModel(string $facultyName): Faculty
    {
        return Faculty::query()
            ->where('name', $facultyName)
            ->firstOrFail();
    }

    private function persistLecturers(array $lecturers, Faculty $faculty): void
    {
        foreach ($lecturers as $lecturer) {
            $findByOrCreateWith = [
                'link' => $lecturer['link'],
            ];

            $faculty->lecturers()->updateOrCreate($findByOrCreateWith, $lecturer);
        }
    }
}
