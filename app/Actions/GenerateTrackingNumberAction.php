<?php

namespace App\Actions;

use App\Models\TrackingNumber;
use Illuminate\Support\Facades\Hash;

class GenerateTrackingNumberAction
{
    public function execute(): int
    {
        $model = TrackingNumber::query()->newModelInstance();
        $model->hash = Hash::make(now());

        $model->saveOrFail();

        return $model->id;
    }
}
