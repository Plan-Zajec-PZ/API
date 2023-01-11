<?php

namespace App\ItemProcessors;

use App\Models\Group;
use App\Models\Specialization;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class GroupsPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $groups = $item['groups'];
        $specialization = Specialization::query()->firstWhere('link', $item['specialization_page_link']);

        $this->persistGroups($groups, $specialization);

        return $item;
    }

    private function persistGroups($itemGroups, $specialization)
    {
        foreach ($itemGroups as $itemGroup) {
            $group = Group::query()
                ->firstOrNew(['name' => $itemGroup]);

            $group->specialization()->associate($specialization);

            $group->tracking_number_id = $specialization->tracking_number_id;
            $group->save();

            $schedule = $group->groupSchedule()->firstOrNew();
            $schedule->tracking_number_id = $group->tracking_number_id;
        }
    }
}
