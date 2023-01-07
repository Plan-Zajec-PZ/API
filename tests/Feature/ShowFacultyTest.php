<?php

namespace Tests\Feature;

use App\Models\Faculty;
use Database\Seeders\FacultiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowFacultyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FacultiesSeeder::class);

        $this->withHeader(
            'Authorization',
            'Bearer ' . config('security.remote_key')
        );
    }

    public function testSpecificFacultyCanBeRetrieved()
    {
        $faculty = Faculty::all()->random();

        $route = route('faculties.show', ['faculty' => $faculty->id]);

        $response = $this->getJson($route);
        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'majors'
            ]
        ]);
    }

    public function testRequestWithInvalidFacultyIdIsNotFound()
    {
        Faculty::query()->delete();
        $this->assertDatabaseEmpty('faculties');

        $route = route('faculties.show', [
            'faculty' => random_int(1, 100)
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
