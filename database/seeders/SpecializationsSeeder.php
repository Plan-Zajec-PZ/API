<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
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
        $major = Major::factory()
            ->for(Faculty::factory())
            ->create();

        Specialization::factory()
            ->count(2)
            ->for($major)
            ->create();
    }
}
