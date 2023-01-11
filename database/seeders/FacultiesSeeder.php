<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\TrackingNumber;
use Illuminate\Database\Seeder;

class FacultiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trackingNumber = TrackingNumber::factory();

        Faculty::factory()
            ->count(5)
            ->for($trackingNumber)
            ->create();
    }
}
