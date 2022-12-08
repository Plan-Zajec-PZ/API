<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLecturersRequest;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Cache;

class LecturerController extends Controller
{
    public function index(IndexLecturersRequest $request): mixed
    {
        $facultyId = $request->validated('faculty');

        $key = 'lecturers-' . $facultyId ?? 'all';
        $seconds = 60 * 60;
        $callback = $this->buildCallback($facultyId);

        return Cache::remember($key, $seconds, $callback);
    }

    private function buildCallback(?int $facultyId): \Closure
    {
        $whenCallback = fn ($query) => $query->where('faculty_id', $facultyId);

        return fn () => Lecturer::query()
            ->when($facultyId, $whenCallback)
            ->pluck('name', 'id');
    }

    public function show(Lecturer $lecturer): LecturerResource
    {
        return new LecturerResource($lecturer);
    }
}
