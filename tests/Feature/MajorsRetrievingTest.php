<?php

namespace Tests\Feature;

use App\Models\Faculty;
use Database\Seeders\MajorsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MajorsRetrievingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MajorsSeeder::class);

        $this->withHeader(
            'Authorization',
            'Bearer ' . config('security.remote_key')
        );
    }

    public function testMajorsForSpecificFacultyCanBeRetrieved()
    {
        $faculty = Faculty::all()->random();

        $route = route('majors.index', [
            'faculty' => $faculty->id,
        ]);

        $response = $this->getJson($route);
        $response->assertOk();

        $response->assertJsonMissing([
            'data' => [
                'id',
                'name',
                'specializations' => [
                    'id',
                    'name'
                ]
            ]
        ]);
    }

    public function testRequestWithInvalidFacultyIdIsNotFound()
    {
        Faculty::query()->delete();
        $this->assertDatabaseEmpty('faculties');

        $route = route('majors.index', [
            'faculty' => random_int(1, 100),
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
