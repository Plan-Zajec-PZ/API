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
        $specialization = Specialization::firstWhere('link', $item['specialization_page_link']);

        $this->persistGroups($groups, $specialization);

        return $item;
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
}
