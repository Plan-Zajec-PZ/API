<?php

namespace App\ItemProcessors;

use App\Models\Lecturer;
use App\Models\Schedule;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class SchedulePersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $rawSchedule = $item->get('schedule');
        $trackingNumberId= $item->get('tracking_number_id');

        $initiatorUri = $item->get('initiatorUri');

        $scheduleModel = $this->getScheduleModel($initiatorUri);

        $scheduleModel->content = json_encode($rawSchedule);
        $scheduleModel->tracking_number_id = $trackingNumberId;

        $scheduleModel->save();

        return $item;
    }

    private function getScheduleModel(string $initiatorUri): Schedule
    {
        $lecturer = Lecturer::query()
            ->where('link', $initiatorUri)
            ->with('schedule')
            ->firstOrFail();

        return $lecturer->schedule()->firstOrNew();
    }
}
