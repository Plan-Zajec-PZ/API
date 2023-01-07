<?php

namespace App\Http\Controllers;

use App\Actions\RetrieveLecturersAction;
use App\Http\Requests\IndexLecturersRequest;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;

class LecturerController extends Controller
{
    public function index(IndexLecturersRequest $request, RetrieveLecturersAction $action): mixed
    {
        $facultyId = $request->validated('faculty');

        return ['data' => $action->execute($facultyId)];
    }

    public function show(Lecturer $lecturer): LecturerResource
    {
        return new LecturerResource($lecturer);
    }
}
