<?php

namespace Tests\Feature;

use App\Models\Faculty;
use Database\Seeders\FacultiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultiesRetrievingTest extends TestCase
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

    public function testFacultiesCanBeRetrieved()
    {
        $route = route('faculties.index');

        $response = $this->getJson($route);
        $response->assertOk();

        $expectedNumber = Faculty::query()->count();
        $response->assertJsonCount($expectedNumber, 'data');
    }
}
