<?php

namespace App\ItemProcessors;

use App\Models\Lecturer;
use App\Models\Schedule;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class LegendPersister implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $legends = $item->get('legend');
        $initiatorUri = $item->get('initiatorUri');

        $schedule = $this->getSchedule($initiatorUri);

        foreach ($legends as $legend) {
            $findByOrCreateWith = [
                ...$legend,
                'schedule_id' => $schedule->id,
            ];

            $schedule->legends()->updateOrCreate($findByOrCreateWith, $legend);
        }

        return $item;
    }

    private function getSchedule(string $initiatorUri): Schedule
    {
        $lecturer = Lecturer::query()
            ->where('link', $initiatorUri)
            ->with(['schedule' => ['legends']])
            ->firstOrFail();

        return $lecturer->schedule()->firstOrCreate();
    }
}
