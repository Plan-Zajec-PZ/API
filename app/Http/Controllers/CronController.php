<?php

namespace App\Http\Controllers;

use App\Exceptions\CommandFailedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Psy\Command\Command;

class CronController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $result = Artisan::call('schedule:run');

        if ($result === Command::FAILURE) {
            throw new CommandFailedException();
        }

        return response()->json('Success', 200);
    }
}
