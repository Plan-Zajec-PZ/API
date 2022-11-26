<?php

namespace App\ItemProcessors;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class SchedulePersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $rawSchedule = $item->get('schedule');
        $initiatorUri = $item->get('initiatorUri');

        $scheduleModel = $this->getScheduleModel($initiatorUri);

        $scheduleModel->update([
            'content' => json_encode($rawSchedule)
        ]);

        return $item;
    }

    private function getScheduleModel(string $initiatorUri): Model
    {
        $lecturer = Lecturer::query()
            ->where('link', $initiatorUri)
            ->with('schedule')
            ->firstOrFail();

        return $lecturer->schedule()->firstOrCreate();
    }
}
