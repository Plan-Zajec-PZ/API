<?php

namespace App\Http\Controllers;

use App\Actions\RetrieveFacultiesAction;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FacultyController extends Controller
{
    public function index(RetrieveFacultiesAction $action): ResourceCollection
    {
        return $action->execute();
    }

    public function show(Faculty $faculty): FacultyResource
    {
        return new FacultyResource($faculty->load('majors'));
    }
}
