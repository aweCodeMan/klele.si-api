<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_list_of_groups()
    {
        $groups = Group::factory()->count(5)->create();

        $response = $this->get(route('groups.index'))->assertStatus(200);

        $this->assertCount(5, $response->json('data'));
    }
}
