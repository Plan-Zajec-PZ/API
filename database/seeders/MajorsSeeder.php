<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Specialization;
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
        $specialization = Specialization::factory();

        Major::factory()
            ->count(5)
            ->for(Faculty::factory())
            ->has($specialization)
            ->create();
    }
}
