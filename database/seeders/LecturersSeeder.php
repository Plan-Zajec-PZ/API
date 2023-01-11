<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\TrackingNumber;
use Illuminate\Database\Seeder;

class LecturersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trackingNumber = TrackingNumber::factory();

        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory()->for($trackingNumber))
            ->for($trackingNumber)
            ->create();

        $trackingNumber = TrackingNumber::factory();

        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory()->for($trackingNumber))
            ->for($trackingNumber)
            ->create();
    }
}
