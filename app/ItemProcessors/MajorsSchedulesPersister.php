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

        foreach ($majorScheduleItems as &$majorScheduleItem) {
            $majorScheduleItem['schedule'] = $this->getGroupDailySchedules($groups, $majorScheduleItem['schedule']);
        }

        $majorScheduleItems = $this->getGroupSchedules($groups, $majorScheduleItems);

        $this->persistGroups($groups, $specialization);
        $this->persistGroupSchedules($majorScheduleItems);
        return $item;
    }

    public function getGroupDailySchedules($groups, $dailySchedule): array
    {
        $new = [];
        foreach ($groups as $group) {
            $new[$group] = [];
            $hours = [];
            foreach ($dailySchedule as $hour => $value) {
                $hours[$hour] = $value[$group];
            }
            $new[$group] = $hours;
        }

        return $new;
    }

    public function getGroupSchedules($groups, $schedule): array
    {
        $new = [];
        foreach ($groups as $group) {
            $new[$group] = [];
            $days = [];
            foreach ($schedule as $day) {
                $days[$day['day']] = $day['schedule'][$group];
            }
            $new[$group] = $days;
        }

        return $new;
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
