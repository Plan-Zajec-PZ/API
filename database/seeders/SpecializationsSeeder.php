<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
use App\Models\TrackNumber;
use Illuminate\Database\Seeder;

class SpecializationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trackNumber = TrackNumber::factory();
        $faculty = Faculty::factory()->for($trackNumber);

        $major = Major::factory()
            ->for($faculty)
            ->for($trackNumber);

        Specialization::factory()
            ->count(2)
            ->for($major)
            ->for($trackNumber)
            ->create();
    }
}
