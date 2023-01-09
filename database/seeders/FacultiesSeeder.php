<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\TrackNumber;
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
        $trackNumber = TrackNumber::factory();

        Faculty::factory()
            ->count(5)
            ->for($trackNumber)
            ->create();
    }
}
