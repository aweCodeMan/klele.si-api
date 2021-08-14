<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Score;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_verified_users_can_upvote()
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::DOWNVOTE]))->assertStatus(403);
    }

    /** @test */
    public function it_can_change_a_vote()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);

        $this->assertDatabaseHas('votes', ['user_uuid' => $user->uuid, 'type' => 'post', 'uuid' => $post->uuid, 'vote' => Vote::DOWNVOTE]);
    }

    /** @test */
    public function it_blocks_an_invalid_request()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => Str::orderedUuid()->toString(), 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(422);
    }

    /** @test */
    public function it_can_remove_a_vote()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::NEUTRAL]))->assertStatus(200);

        $this->assertDatabaseMissing('votes', ['user_uuid' => $user->uuid, 'type' => 'post', 'uuid' => $post->uuid]);
    }

    /** @test */
    public function it_can_upvote_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);

        $this->assertDatabaseHas('votes', ['user_uuid' => $user->uuid, 'type' => 'post', 'uuid' => $post->uuid, 'vote' => Vote::UPVOTE . ""]);
    }

    /** @test */
    public function it_can_downvote_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);

        $this->assertDatabaseHas('votes', ['user_uuid' => $user->uuid, 'type' => 'post', 'uuid' => $post->uuid, 'vote' => Vote::DOWNVOTE . ""]);
    }

    /** @test */
    public function it_can_upvote_a_comment()
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);

        $this->assertDatabaseHas('votes', ['user_uuid' => $user->uuid, 'type' => 'comment', 'uuid' => $comment->uuid, 'vote' => Vote::UPVOTE . ""]);
    }

    /** @test */
    public function it_only_stores_one_vote()
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);

        $this->assertDatabaseCount('votes', 1);
    }

    /** @test */
    public function it_can_downvote_a_comment()
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);

        $this->assertDatabaseHas('votes', ['user_uuid' => $user->uuid, 'type' => 'comment', 'uuid' => $comment->uuid, 'vote' => Vote::DOWNVOTE . ""]);
    }

    /** @test */
    public function it_updates_the_votes_on_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $user5 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 1]);

        $response = $this->actingAs($user2)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 2]);

        $response = $this->actingAs($user3)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 3]);

        $response = $this->actingAs($user3)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 1]);

        $response = $this->actingAs($user4)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 0]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => -1]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 1]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::NEUTRAL]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $post->uuid, 'votes' => 1]);
    }

    /** @test */
    public function it_updates_the_votes_on_a_comment()
    {
        $comment = Comment::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $user5 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 1]);

        $response = $this->actingAs($user2)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 2]);

        $response = $this->actingAs($user3)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 3]);

        $response = $this->actingAs($user3)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 1]);

        $response = $this->actingAs($user4)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 0]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::DOWNVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => -1]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 1]);

        $response = $this->actingAs($user5)->post(route('votes.store', ['uuid' => $comment->uuid, 'type' => 'comment', 'vote' => Vote::NEUTRAL]))->assertStatus(200);
        $this->assertDatabaseHas('scores', ['uuid' => $comment->uuid, 'votes' => 1]);
    }

    /** @test */
    public function it_returns_votes_on_a_comment()
    {
        $group = Group::factory()->create();
        $user = User::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];
        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);
        $post = Post::first();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => Comment::first()->uuid, 'type' => 'comment', 'vote' => Vote::UPVOTE]))->assertStatus(200);

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);

        $this->assertEquals(0, $response->json('data')['score']['votes']);
        $this->assertEquals(1, $response->json('data')['comments'][0]['score']['votes']);
    }

    /** @test */
    public function it_returns_votes_on_a_post()
    {
        $group = Group::factory()->create();
        $user = User::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];
        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);
        $post = Post::first();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $response = $this->actingAs($user)->post(route('votes.store', ['uuid' => $post->uuid, 'type' => 'post', 'vote' => Vote::UPVOTE]))->assertStatus(200);

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);

        $this->assertEquals(1, $response->json('data')['score']['votes']);
        $this->assertEquals(0, $response->json('data')['comments'][0]['score']['votes']);
    }
}
