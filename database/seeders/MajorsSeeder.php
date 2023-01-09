<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
use App\Models\TrackingNumber;
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
        $trackingNumber = TrackingNumber::factory();
        $faculty = Faculty::factory()->for($trackingNumber);
        $specialization = Specialization::factory()->for($trackingNumber);

        Major::factory()
            ->count(5)
            ->for($trackingNumber)
            ->for($faculty)
            ->has($specialization)
            ->create();
    }
}
