<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
use App\Models\TrackingNumber;
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
        $trackingNumber = TrackingNumber::factory();
        $faculty = Faculty::factory()->for($trackingNumber);

        $major = Major::factory()
            ->for($faculty)
            ->for($trackingNumber);

        Specialization::factory()
            ->count(2)
            ->for($major)
            ->for($trackingNumber)
            ->create();
    }
}
