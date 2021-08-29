<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_a_comment_for_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $this->assertDatabaseHas('comments', ['author_uuid' => $user->uuid, 'parent_uuid' => null, 'root_uuid' => $post->uuid]);
        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Comment</h1>']);
    }

    /** @test */
    public function it_updates_a_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->author)->put(route('comments.update', ['uuid' => $comment->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Comment</h1>']);
    }

    /** @test */
    public function an_admin_can_update_a_comment()
    {
        $comment = Comment::factory()->create();
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->put(route('comments.update', ['uuid' => $comment->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Comment</h1>']);
    }

    /** @test */
    public function it_deletes_a_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->author)->delete(route('comments.delete', ['uuid' => $comment->uuid]))->assertStatus(200);

        $this->assertSoftDeleted(Comment::withTrashed()->first());
    }

    /** @test */
    public function only_admins_can_restore_a_comment()
    {
        $comment = Comment::factory()->create(['deleted_at' => now()]);
        $admin = $this->createAdminUser();

        $response = $this->actingAs($comment->author)->post(route('comments.restore', ['uuid' => $comment->uuid]))->assertStatus(403);
        $response = $this->actingAs($admin)->post(route('comments.restore', ['uuid' => $comment->uuid]))->assertStatus(200);

        $this->assertDatabaseHas('comments', ['uuid' => $comment->uuid, 'deleted_at' => null]);
    }

    /** @test */
    public function an_admin_can_delete_a_comment()
    {
        $comment = Comment::factory()->create();
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->delete(route('comments.delete', ['uuid' => $comment->uuid]))->assertStatus(200);

        $this->assertSoftDeleted(Comment::withTrashed()->first());
    }

    /** @test */
    public function it_only_allows_authors_to_delete_a_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('comments.delete', ['uuid' => $comment->uuid]))->assertStatus(403);
    }

    /** @test */
    public function it_only_allows_authors_to_update_a_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put(route('comments.update', ['uuid' => $comment->uuid]), ['markdown' => '#Comment'])->assertStatus(403);
    }

    /** @test */
    public function it_returns_comments_for_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $comments = Comment::factory(['root_uuid' => $post->uuid])->count(5)->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);

        $this->assertCount(5, $response->json('data')['comments']);
    }

    /** @test */
    public function it_increments_a_comment_count_for_a_post()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        $this->assertDatabaseHas('posts', ['number_of_comments' => 1, 'uuid' => $post->uuid]);
    }

    /** @test */
    public function a_deleted_comment_has_redacted_content()
    {
        $comment = Comment::factory()->create(['deleted_at' => now()]);

        $response = $this->get(route('posts.show', ['slug' => $comment->root_uuid]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($comment->uuid, $json['comments'][0]['uuid']);
        $this->assertSame(Comment::TEXT_DELETED, $json['comments'][0]['html']);
    }

    /** @test */
    public function it_stores_a_comment_for_a_comment()
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $comment->root_uuid]), ['markdown' => '#Comment', 'parentUuid' => $comment->uuid])->assertStatus(200);

        $this->assertDatabaseHas('comments', ['author_uuid' => $user->uuid, 'parent_uuid' => $comment->uuid, 'root_uuid' => $comment->root_uuid]);
        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Comment</h1>']);
    }

    /** @test */
    public function it_creates_a_tree_of_comments()
    {
        $post = Post::factory()->markdownPost()->create();
        $innerComments = [];
        $twiceInnerComments = [];
        $comments = Comment::factory()->count(3)->create(['root_uuid' => $post->uuid])->each(function ($item) use ($post, &$innerComments, &$twiceInnerComments) {
            $innerComment = Comment::factory()->create(['root_uuid' => $post->uuid, 'parent_uuid' => $item->uuid]);
            $innerComments[$item->uuid] = $innerComment->uuid;
            $twiceInnerComments[$item->uuid][$innerComment->uuid] = Comment::factory()->create(['root_uuid' => $post->uuid, 'parent_uuid' => $innerComment->uuid])->uuid;
        });

        $response = $this->get(route('posts.show', ['slug' => $post->uuid]))->assertStatus(200);
        $json = $response->json('data');

        foreach ($json['comments'] as $key => $comment) {
            $this->assertSame($comments[$key]->uuid, $comment['uuid']);
            $this->assertSame($innerComments[$comment['uuid']], $comment['comments'][0]['uuid']);
            $this->assertSame($twiceInnerComments[$comment['uuid']][$innerComments[$comment['uuid']]], $comment['comments'][0]['comments'][0]['uuid']);
        }
    }
}
