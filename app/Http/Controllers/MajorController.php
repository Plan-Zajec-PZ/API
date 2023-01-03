<?php

namespace App\Http\Controllers;

use App\Http\Resources\MajorResource;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MajorController extends Controller
{
    public function index(Faculty $faculty): ResourceCollection
    {
        return MajorResource::collection(Major::query()->whereBelongsTo($faculty)->with('specializations')->get());
    }

    public function show(Faculty $faculty, Major $major): MajorResource
    {
        return new MajorResource($major->load('specializations'));
    }
}
