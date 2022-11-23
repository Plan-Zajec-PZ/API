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
            $facultyModel = $this->getFacultyModel(
                $faculty['facultyName']
            );

            $lecturers = $this->attachFacultyIdTo(
                $faculty['lecturers'],
                $facultyModel->id
            );

            $facultyModel->lecturers()->upsert(
                $lecturers,
                ['link'],
                ['name', 'faculty_id']
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

    private function attachFacultyIdTo(array $lecturers, int $facultyId): array
    {
        $callback = fn ($lecturer) => [
            ...$lecturer,
            'faculty_id' => $facultyId
        ];

        return array_map($callback, $lecturers);
    }
}
