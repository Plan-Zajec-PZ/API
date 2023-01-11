<?php

namespace App\Actions;

use App\Http\Resources\MajorResource;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RetrieveMajorsAction
{
    protected Faculty $faculty;

    public function execute(Faculty $faculty): mixed
    {
        $this->faculty = $faculty;

        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'majors';
        $separator = '-';
        $selected = $this->faculty->id;

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHours(3);
    }

    private function buildCallback(): \Closure
    {
        $majors = Major::query()
            ->whereBelongsTo($this->faculty)
            ->with('specializations')
            ->get();

        return fn () => MajorResource::collection($majors);
    }
}
