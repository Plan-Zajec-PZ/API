<?php

namespace App\Actions;

use App\Models\Lecturer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RetrieveLecturersAction
{
    protected ?int $facultyId;

    public function execute(?int $facultyId = null): mixed
    {
        $this->facultyId = $facultyId;

        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'lecturers';
        $separator = '-';
        $selected = $this->facultyId ?? 'all';

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHour();
    }

    private function buildCallback(): \Closure
    {
        $callback = function ($query) {
            return $query->where('faculty_id', $this->facultyId);
        };

        return fn() => Lecturer::query()
            ->when($this->facultyId, $callback)
            ->pluck('name', 'id');
    }
}
