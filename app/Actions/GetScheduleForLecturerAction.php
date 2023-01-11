<?php

namespace App\Actions;

use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GetScheduleForLecturerAction
{
    protected Lecturer $lecturer;

    public function execute(Lecturer $lecturer): LecturerResource
    {
        $this->lecturer = $lecturer;

        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'lecturer';
        $separator = '-';
        $selected = $this->lecturer->id;

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHours(3);
    }

    private function buildCallback(): \Closure
    {
        return fn () => new LecturerResource(
            $this->lecturer->load(['faculty', 'schedule'])
        );
    }
}
