<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use Database\Seeders\LecturersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RoachPHP\Roach;
use Tests\TestCase;

class LecturerScheduleRetrievingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LecturersSeeder::class);

        Roach::fake();
    }

    public function testLecturerScheduleCanBeRetrieved()
    {
        $selectedLecturer = Lecturer::all()->random();

        $route = route('lecturers.show', [
            'lecturer' => $selectedLecturer->id
        ]);

        $response = $this->getJson($route);
        $response->assertOk();
    }

    public function testRequestWithInvalidLecturerIdIsNotFound()
    {
        Lecturer::query()->delete();
        $this->assertDatabaseEmpty('lecturers');

        $route = route('lecturers.show', [
            'lecturer' => random_int(1, 100)
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
