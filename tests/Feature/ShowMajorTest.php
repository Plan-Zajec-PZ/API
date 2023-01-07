<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Major;
use Database\Seeders\MajorsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMajorTest extends TestCase
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

    public function testSpecificMajorForSpecificFacultyCanBeRetrieved()
    {
        $faculty = Faculty::all()->random();
        $major = Major::all()->random();

        $route = route('majors.show', [
            'faculty' => $faculty->id,
            'major' => $major->id,
        ]);

        $response = $this->getJson($route);
        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'specializations' => [
                    ['id', 'name']
                ]
            ]
        ]);
    }

    public function testRequestWithInvalidFacultyIdAndMajorIdIsNotFound()
    {
        Faculty::query()->delete();
        Major::query()->delete();

        $this->assertDatabaseEmpty('faculties');
        $this->assertDatabaseEmpty('majors');

        $route = route('majors.show', [
            'faculty' => random_int(1, 100),
            'major' => random_int(1, 100)
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
