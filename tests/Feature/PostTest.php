<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_verified_users_can_create_a_post()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid])->assertStatus(403);
    }

    /** @test */
    public function it_stores_a_markdown_post()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);

        $this->assertDatabaseHas('posts', ['post_type' => Post::TYPE_MARKDOWN, 'title' => $data['title'], 'slug' => Str::slug($data['title']), 'group_uuid' => $group->uuid, 'author_uuid' => $user->uuid]);
        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Hello</h1>']);
    }

    /** @test */
    public function it_updates_a_markdown_post()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);

        $response = $this->actingAs($user)->put(route('posts.update', ['uuid' => Post::first()->uuid]), ['title' => 'Test', 'markdown' => '#Test'])->assertStatus(200);

        $this->assertDatabaseHas('posts', ['post_type' => Post::TYPE_MARKDOWN, 'title' => 'Test', 'slug' => Str::slug($data['title']), 'group_uuid' => $group->uuid, 'author_uuid' => $user->uuid]);
        $this->assertDatabaseHas('markdowns', ['html' => '<h1>Test</h1>']);
    }

    /** @test */
    public function only_the_author_can_update_a_post()
    {
        $user = User::factory()->create();
        $imposter = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);
        $response = $this->actingAs($imposter)->put(route('posts.update', ['uuid' => Post::first()->uuid]), ['title' => 'Test', 'markdown' => '#Test'])->assertStatus(403);
    }

    /** @test */
    public function it_deletes_a_post()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_MARKDOWN, 'title' => 'Example', 'markdown' => '#Hello', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);
        $response = $this->actingAs($user)->delete(route('posts.delete', Post::first()->uuid))->assertStatus(200);

        $this->assertSoftDeleted(Post::withTrashed()->first());
    }

    /** @test */
    public function it_returns_a_markdown_post()
    {
        $post = Post::factory()->markdownPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
        $this->assertEquals($post->post_type, $json['postType']);
        $this->assertSame($post->title, $json['title']);
        $this->assertSame($post->markdown->html, $json['content']['html']);
        $this->assertNotNull($json['createdAt']);
        $this->assertNull($json['deletedAt']);
    }

    /** @test */
    public function it_returns_a_markdown_post_by_uuid()
    {
        $post = Post::factory()->markdownPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->uuid]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
        $this->assertEquals($post->post_type, $json['postType']);
        $this->assertSame($post->title, $json['title']);
        $this->assertSame($post->markdown->html, $json['content']['html']);
        $this->assertNotNull($json['createdAt']);
        $this->assertNull($json['deletedAt']);
    }

    /** @test */
    public function it_returns_a_deleted_markdown_post()
    {
        $post = Post::factory(['deleted_at' => now()])->markdownPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
    }

    /** @test */
    public function a_deleted_markdown_post_has_redacted_content()
    {
        $post = Post::factory(['deleted_at' => now()])->markdownPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
        $this->assertSame(Post::TEXT_DELETED, $json['title']);
        $this->assertSame(Post::TEXT_DELETED, $json['content']['html']);
    }

    /** @test */
    public function it_stores_a_link_post()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_LINK, 'title' => 'Example', 'link' => 'https://example.com', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);

        $this->assertDatabaseHas('posts', ['post_type' => Post::TYPE_LINK, 'title' => $data['title'], 'slug' => Str::slug($data['title']), 'group_uuid' => $group->uuid, 'author_uuid' => $user->uuid]);
        $this->assertDatabaseHas('links', ['link' => $data['link']]);
    }

    /** @test */
    public function it_updates_a_link_post()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = ['postType' => Post::TYPE_LINK, 'title' => 'Example', 'link' => 'https://example.com', 'groupUuid' => $group->uuid];

        $response = $this->actingAs($user)->post(route('posts.store'), $data)->assertStatus(200);

        $response = $this->actingAs($user)->put(route('posts.update', ['uuid' => Post::first()->uuid]), ['title' => 'Test'])->assertStatus(200);

        $this->assertDatabaseHas('posts', ['post_type' => Post::TYPE_LINK, 'title' => 'Test', 'slug' => Str::slug($data['title']), 'group_uuid' => $group->uuid, 'author_uuid' => $user->uuid]);
    }

    /** @test */
    public function it_returns_a_deleted_link_post()
    {
        $post = Post::factory(['deleted_at' => now()])->linkPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
    }

    /** @test */
    public function a_deleted_link_post_has_redacted_content()
    {
        $post = Post::factory(['deleted_at' => now()])->linkPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
        $this->assertSame(Post::TEXT_DELETED, $json['title']);
        $this->assertNull($json['content']['link']);
    }

    /** @test */
    public function it_returns_a_link_post()
    {
        $post = Post::factory()->linkPost()->create();

        $response = $this->get(route('posts.show', ['slug' => $post->slug]))->assertStatus(200);
        $json = $response->json('data');

        $this->assertSame($post->uuid, $json['uuid']);
        $this->assertEquals($post->post_type, $json['postType']);
        $this->assertSame($post->title, $json['title']);
        $this->assertSame($post->link->link, $json['content']['link']);
        $this->assertNotNull($json['createdAt']);
        $this->assertNull($json['deletedAt']);
    }
}
