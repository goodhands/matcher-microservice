<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Property;
use App\Models\SearchProfile;

class SearchProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
    */
    protected $seed = true;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_property_can_be_created_and_queried()
    {
        $property = Property::factory()->count(10)->create();

        $response = $this->get("/api/match/{$property->first()->id}");

        $response->assertStatus(200);
    }
}
