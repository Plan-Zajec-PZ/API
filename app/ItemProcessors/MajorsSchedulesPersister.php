<?php

namespace App\ItemProcessors;

use App\Models\Group;
use App\Models\Specialization;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class MajorsSchedulesPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $groups = $item['groups'];
        $specialization = Specialization::firstWhere('link', $item['specialization_page_link']);
        $majorScheduleItems = $item['dailySchedules'];

        $majorScheduleItems = $this->getGroupSchedules($groups, $majorScheduleItems);

        $this->persistGroups($groups, $specialization);
        $this->persistGroupSchedules($majorScheduleItems);
        return $item;
    }

    public function getGroupSchedules($groups, $schedule): array
    {
        $result = [];

        foreach ($groups as $group) {
            $result[$group] = array_combine(
                array_column($schedule, 'day'),
                array_column(array_column($schedule, 'schedule'), $group)
            );
        }

        return $result;
    }

    public function persistGroups($itemGroups, $specialization)
    {
        foreach ($itemGroups as $itemGroup) {
            $group = Group::firstOrNew([
                'name' => $itemGroup,
            ]);
            $group->specialization()->associate($specialization);
            $group->save();

            $group->groupSchedule()->firstOrCreate();
        }
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
