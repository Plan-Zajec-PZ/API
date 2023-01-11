<?php

namespace App\Actions;

use App\Http\Resources\SpecializationResource;
use App\Models\Specialization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GetScheduleForSpecializationAction
{
    protected Specialization $specialization;

    public function execute(Specialization $specialization): mixed
    {
        $this->specialization = $specialization;

        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'specialization';
        $separator = '-';
        $selected = $this->specialization->id;

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHours(3);
    }

    private function buildCallback(): \Closure
    {
        return fn () => new SpecializationResource(
            $this->specialization->load(['groups', 'abbreviationLegends', 'subjectLegends'])
        );
    }
}
