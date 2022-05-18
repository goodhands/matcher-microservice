<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_search_profile()
    {
        $response = $this->get('/api/match/2');

        $response->assertStatus(200);

        $this->assertDatabaseHas('properties', [
            'id' => '2',
        ]);

        $response->assertIsObject();
    }
}
