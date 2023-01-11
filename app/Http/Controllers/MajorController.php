<?php

namespace App\Http\Controllers;

use App\Actions\GetMajorAction;
use App\Actions\RetrieveMajorsAction;
use App\Http\Resources\MajorResource;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MajorController extends Controller
{
    public function index(Faculty $faculty, RetrieveMajorsAction $action): ResourceCollection
    {
        return $action->execute($faculty);
    }

    public function show(Faculty $faculty, Major $major, GetMajorAction $action): MajorResource
    {
        return $action->execute($major);
    }
}
