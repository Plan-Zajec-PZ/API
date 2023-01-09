<?php

namespace App\ItemProcessors;

use App\Models\Faculty;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class FacultiesPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $faculties = $item->all();
        $findBy = ['name', 'link'];
        $columnsToUpdate = ['name', 'link', 'tracking_number_id'];
        
        Faculty::query()->upsert(
            $faculties,
            $findBy,
            $columnsToUpdate,
        );

        return $item;
    }
}
