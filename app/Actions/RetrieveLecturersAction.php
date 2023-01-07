<?php

namespace App\Actions;

use App\Models\Lecturer;
use Illuminate\Support\Collection;

class RetrieveLecturersAction
{
    public function execute(?int $facultyId): Collection
    {
        $callback = fn ($query) => $query->where('faculty_id', $facultyId);

        return Lecturer::query()
            ->when($facultyId, $callback)
            ->get(['id', 'name']);
    }
}
