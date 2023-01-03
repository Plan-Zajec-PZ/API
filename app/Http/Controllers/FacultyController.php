<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacultyResource;
use App\Models\Faculty;

class FacultyController extends Controller
{
    public function index()
    {
        return FacultyResource::collection(Faculty::all());
    }
}
