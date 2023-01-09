<?php

namespace App\ItemProcessors;

use App\Models\Group;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class MajorsSchedulesPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $majorScheduleItems = $item['dailySchedules'];
        $trackingNumberId = $item['tracking_number_id'];

        $this->persistGroupSchedules($majorScheduleItems, $trackingNumberId);

        return $item;
    }

    private function persistGroupSchedules(array $majorScheduleItems, int $trackingNumberId): void
    {
        foreach ($majorScheduleItems as $group => $majorScheduleItem) {
            $group = Group::query()
                ->firstWhere(['name' => $group]);

            $groupSchedule = $group->groupSchedule()->firstOrNew();

            $groupSchedule->content = json_encode($majorScheduleItem);
            $groupSchedule->tracking_number_id = json_encode($trackingNumberId);

            $groupSchedule->save();
        }
    }
}
