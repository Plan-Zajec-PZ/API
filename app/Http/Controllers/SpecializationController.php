<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpecializationResource;
use App\Models\Major;
use App\Models\Specialization;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SpecializationController extends Controller
{
    public function index(Major $major): ResourceCollection
    {
        return SpecializationResource::collection(Specialization::query()->whereBelongsTo($major)->get());
    }

    public function show(Major $major, Specialization $specialization): SpecializationResource
    {
        return new SpecializationResource($specialization->load(['groups', 'abbreviationLegends', 'subjectLegends']));
    }
}
