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

            $lecturers = $this->attachForeignKeys(
                $faculty['lecturers'],
                $facultyModel->id,
                $faculty['tracking_number_id'],
            );

            $facultyModel->lecturers()->upsert(
                $lecturers,
                ['link'],
                ['name', 'faculty_id', 'tracking_number_id']
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

    private function attachForeignKeys(array $lecturers, int $facultyId, int $trackingNumberId): array
    {
        $callback = fn ($lecturer) => [
            ...$lecturer,
            'faculty_id' => $facultyId,
            'tracking_number_id' => $trackingNumberId,
        ];

        return array_map($callback, $lecturers);
    }
}
