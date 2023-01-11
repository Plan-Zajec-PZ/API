<?php

namespace App\Actions;

use App\Http\Resources\MajorResource;
use App\Models\Major;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GetMajorAction
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
        $entity = 'major';
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
        return fn () => new MajorResource(
            $this->major->load('specializations')
        );
    }
}
