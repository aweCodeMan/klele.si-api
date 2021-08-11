<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_paginated_list_of_posts()
    {
        $links = Post::factory()->linkPost()->count(50)->create();
        $markdowns = Post::factory()->markdownPost()->count(50)->create();

        $response = $this->get(route('feed'))->assertStatus(200);

        $this->assertCount(50, $response->json('data'));
        $this->assertNotNull($response->json('meta'));
    }

    /** @test */
    public function it_returns_a_paginated_list_of_posts_inside_a_group()
    {
        $group = Group::factory()->create();
        $links = Post::factory(['group_uuid' => $group->uuid])->linkPost()->count(10)->create();
        $markdowns = Post::factory()->markdownPost()->count(20)->create();

        $response = $this->get(route('feed', ['groupUuid' => $group->uuid]))->assertStatus(200);

        $this->assertCount(10, $response->json('data'));

        foreach ($response->json(['data']) as $item) {
            $this->assertSame($group->uuid, $item['groupUuid']);
        }
    }

    /** @test */
    public function it_returns_a_user_with_the_post()
    {
        $group = Group::factory()->create();
        $post = Post::factory()->markdownPost()->create();

        $response = $this->get(route('feed'))->assertStatus(200);

        $this->assertSame($post->author->uuid, $response->json('data')[0]['author']['uuid']);
        $this->assertSame($post->author->full_name, $response->json('data')[0]['author']['fullName']);
    }

    /** @test */
    public function it_returns_a_paginated_list_of_posts_ordered_by_time()
    {
        $new = Post::factory(['created_at' => now()])->markdownPost()->create();
        $old = Post::factory(['created_at' => now()->subDays(2)])->markdownPost()->create();
        $mid = Post::factory(['created_at' => now()->subDays(1)])->markdownPost()->create();

        $response = $this->get(route('feed'))->assertStatus(200);

        $this->assertSame($new->uuid, $response->json('data')[0]['uuid']);
        $this->assertSame($mid->uuid, $response->json('data')[1]['uuid']);
        $this->assertSame($old->uuid, $response->json('data')[2]['uuid']);
    }
}
