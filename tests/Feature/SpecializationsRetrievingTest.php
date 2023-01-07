<?php

namespace Tests\Feature;

use App\Models\Major;
use Database\Seeders\SpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecializationsRetrievingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SpecializationsSeeder::class);

        $this->withHeader(
            'Authorization',
            'Bearer ' . config('security.remote_key')
        );
    }

    public function testSpecializationsForSpecificMajorCanBeReturned()
    {
        $major = Major::all()->random();

        $route = route('specializations.index', [
            'major' => $major->id,
        ]);

        $response = $this->getJson($route);
        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                ['id', 'name']
            ]
        ]);
    }

    public function testRequestWithInvalidMajorIdIsNotFound()
    {
        Major::query()->delete();
        $this->assertDatabaseEmpty('majors');

        $route = route('specializations.index', [
            'major' => random_int(1, 100),
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
