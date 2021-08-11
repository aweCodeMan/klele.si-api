<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MarkdownTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transforms_markdown()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('markdown'), ['markdown' => '#Test'])->assertStatus(200);

        $this->assertSame('<h1>Test</h1>', $response->json('data'));
    }
}
