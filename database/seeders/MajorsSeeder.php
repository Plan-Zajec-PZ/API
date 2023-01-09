<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
use App\Models\TrackNumber;
use Illuminate\Database\Seeder;

class MajorsSeeder extends Seeder
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
        $specialization = Specialization::factory()->for($trackNumber);

        Major::factory()
            ->count(5)
            ->for($trackNumber)
            ->for($faculty)
            ->has($specialization)
            ->create();
    }
}
