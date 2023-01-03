<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Lecturer;
use Database\Seeders\LecturersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturersRetrievingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LecturersSeeder::class);
    }

    public function testAllLecturersCanBeRetrieved()
    {
        $route = route('lecturers.index');

        $response = $this->getJson($route);
        $response->assertOk();

        $expectedNumber = Lecturer::query()->count();
        $response->assertJsonCount($expectedNumber);
    }

    public function testLecturersFromSpecificFaultyCanBeRetrieved()
    {
        $selectedFaculty = Faculty::all()->random();

        $route = route('lecturers.index', [
            'faculty' => $selectedFaculty->id,
        ]);

        $response = $this->getJson($route);
        $response->assertOk();

        $expectedNumber = $selectedFaculty->lecturers()->count();
        $response->assertJsonCount($expectedNumber);
    }

    public function testRequestWithInvalidFacultyIdIsRejected()
    {
        Faculty::query()->delete();
        $this->assertDatabaseEmpty('faculties');

        $route = route('lecturers.index', [
            'faculty' => random_int(1, 100)
        ]);

        $response = $this->getJson($route);
        $response->assertUnprocessable();
    }
}
