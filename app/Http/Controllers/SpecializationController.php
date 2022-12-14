<?php

namespace App\Http\Controllers;

use App\Actions\GetScheduleForSpecializationAction;
use App\Actions\RetrieveSpecializationsAction;
use App\Http\Resources\SpecializationResource;
use App\Models\Major;
use App\Models\Specialization;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SpecializationController extends Controller
{
    public function index(Major $major, RetrieveSpecializationsAction $action): ResourceCollection
    {
        return $action->execute($major);
    }

    public function show(Major $major, Specialization $specialization, GetScheduleForSpecializationAction $action): SpecializationResource
    {
        return $action->execute($specialization);
    }
}
