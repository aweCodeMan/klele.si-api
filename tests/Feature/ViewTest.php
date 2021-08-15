<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_a_view()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);

        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user->uuid, 'number_of_comments' => 0]);
    }

    /** @test */
    public function it_returns_views_for_logged_in_users()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();


        $response = $this->get(route('feed'))->assertStatus(200);
        $this->assertArrayNotHasKey('postView', $response->json('data')[0]);


        $response = $this->actingAs($user)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);
        $response = $this->actingAs($user)->get(route('feed'))->assertStatus(200);

        $this->assertArrayHasKey('postView', $response->json('data')[0]);
        $this->assertEquals(0, $response->json('data')[0]['postView']['numberOfComments']);
    }

    /** @test */
    public function it_handles_multiple_views_with_different_number_of_comments()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);

        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user->uuid, 'number_of_comments' => 0]);
        $this->assertDatabaseCount('post_views', 1);

        $response = $this->actingAs($user)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user->uuid, 'number_of_comments' => 0]);
        $this->assertDatabaseCount('post_views', 1);

        //  Update comments
        $post->number_of_comments = 6;
        $post->save();

        $response = $this->actingAs($user)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);

        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user->uuid, 'number_of_comments' => 6]);
        $this->assertDatabaseCount('post_views', 1);
    }

    /** @test */
    public function it_handles_multiple_views_from_different_users()
    {
        $post = Post::factory()->markdownPost()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);
        $response = $this->actingAs($user2)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);

        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user1->uuid, 'number_of_comments' => 0]);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user2->uuid, 'number_of_comments' => 0]);
        $this->assertDatabaseCount('post_views', 2);

        $post->number_of_comments = 6;
        $post->save();

        $response = $this->actingAs($user1)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user1->uuid, 'number_of_comments' => 6]);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user2->uuid, 'number_of_comments' => 0]);
        $this->assertDatabaseCount('post_views', 2);

        $response = $this->actingAs($user2)->post(route('views.store'), ['postUuid' => $post->uuid])->assertStatus(200);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user1->uuid, 'number_of_comments' => 6]);
        $this->assertDatabaseHas('post_views', ['post_uuid' => $post->uuid, 'user_uuid' => $user2->uuid, 'number_of_comments' => 6]);
        $this->assertDatabaseCount('post_views', 2);
    }
}
