<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\Specialization;
use Database\Seeders\SpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowSpecializationTest extends TestCase
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

    public function testSpecificSpecializationForSpecificMajorCanBeReturned()
    {
        $major = Major::all()->random();
        $specialization = Specialization::all()->random();

        $route = route('specializations.show', [
            'major' => $major->id,
            'specialization' => $specialization->id
        ]);

        $response = $this->getJson($route);
        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'groups',
                'abbreviationLegend',
                'subjectLegends'
            ]
        ]);
    }

    public function testRequestWithInvalidMajorIdAndSpecializationIdIsNotFound()
    {
        Major::query()->delete();
        Specialization::query()->delete();

        $this->assertDatabaseEmpty('majors');
        $this->assertDatabaseEmpty('specializations');

        $route = route('specializations.show', [
            'major' => random_int(1, 100),
            'specialization' => random_int(1, 100)
        ]);

        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
