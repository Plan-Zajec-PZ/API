<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FacultyController extends Controller
{
    public function index(): ResourceCollection
    {
        return FacultyResource::collection(Faculty::all());
    }

    public function show(Faculty $faculty): FacultyResource
    {
        return new FacultyResource($faculty->load('majors'));
    }
}
