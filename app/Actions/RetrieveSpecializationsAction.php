<?php

namespace App\Actions;

use App\Http\Resources\SpecializationResource;
use App\Models\Major;
use App\Models\Specialization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RetrieveSpecializationsAction
{
    protected Major $major;

    public function execute(Major $major): mixed
    {
        $this->major = $major;

        return Cache::remember(
            $this->buildKey(),
            $this->calculateExpirationTime(),
            $this->buildCallback()
        );
    }

    private function buildKey(): string
    {
        $entity = 'specializations';
        $separator = '-';
        $selected = $this->major->id;

        return $entity . $separator . $selected;
    }

    private function calculateExpirationTime(): Carbon
    {
        return now()->addHours(3);
    }

    private function buildCallback(): \Closure
    {
        return fn () => SpecializationResource::collection(
            Specialization::query()->whereBelongsTo($this->major)->get()
        );
    }
}
