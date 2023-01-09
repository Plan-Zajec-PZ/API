<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\TrackNumber;
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
        $trackNumber = TrackNumber::factory();

        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory()->for($trackNumber))
            ->for($trackNumber)
            ->create();

        $trackNumber = TrackNumber::factory();

        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory()->for($trackNumber))
            ->for($trackNumber)
            ->create();
    }
}
