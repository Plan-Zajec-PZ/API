<?php

namespace App\Actions;

use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GetMajorsForFacultyAction
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
        $entity = 'faculty';
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
        return fn () => new FacultyResource(
            $this->faculty->load('majors')
        );
    }
}
