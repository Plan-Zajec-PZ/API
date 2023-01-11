<?php

namespace App\Actions;

use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RetrieveFacultiesAction
{
    public function execute(): mixed
    {
        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'faculties';
        $separator = '-';
        $selected = 'all';

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHours(3);
    }

    private function buildCallback(): \Closure
    {
        return fn () => FacultyResource::collection(
            Faculty::all()
        );
    }
}
