<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Lecturer;
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
        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory())
            ->create();

        Lecturer::factory()
            ->count(5)
            ->for(Faculty::factory())
            ->create();
    }
}
