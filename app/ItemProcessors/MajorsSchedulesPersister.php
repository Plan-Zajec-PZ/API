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

        $this->persistGroupSchedules($majorScheduleItems);

        return $item;
    }

    public function persistGroupSchedules($majorScheduleItems)
    {
        foreach ($majorScheduleItems as $group => $majorScheduleItem) {
            $group = Group::firstWhere([
                'name' => $group,
            ]);

            $groupSchedule = $group->groupSchedule();

            $groupSchedule->update([
                'content' => json_encode($majorScheduleItem)
            ]);
        }
    }
}
