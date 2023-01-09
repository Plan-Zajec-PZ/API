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
        $trackingNumberId = $item['tracking_number_id'];
        $link = $item['specialization_page_link'];
        dump($link);
        $specialization = Specialization::query()
            ->where('link', $link)
            ->firstOrFail();

        $this->persistGroups(
            $groups,
            $specialization,
            $trackingNumberId,
        );

        return $item;
    }

    private function persistGroups(array $itemGroups, Specialization $specialization, int $trackingNumberId)
    {
        foreach ($itemGroups as $itemGroup) {
            $group = Group::query()
                ->firstOrNew(['name' => $itemGroup]);

            $group->specialization()->associate($specialization);
            $group->tracking_number_id = $trackingNumberId;

            $group->save();
        }
    }
}
