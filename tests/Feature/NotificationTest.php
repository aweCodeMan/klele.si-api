<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewReplyNotification;
use App\StorableEvents\CommentCreated;
use App\StorableEvents\Data\CommentCreatedData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_a_notification_to_the_author_when_they_get_a_reply_to_a_post()
    {
        Notification::fake();

        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);

        Notification::assertSentTo($post->author, NewReplyNotification::class);
    }

    /** @test */
    public function it_handles_the_number_of_unread_notifications()
    {
        $post = Post::factory()->markdownPost()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(0, $response->json('data.numberOfUnreadNotifications'));

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);
        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(1, $response->json('data.numberOfUnreadNotifications'));

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);
        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(2, $response->json('data.numberOfUnreadNotifications'));

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $post->uuid]), ['markdown' => '#Comment'])->assertStatus(200);
        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(3, $response->json('data.numberOfUnreadNotifications'));

        $notification = $post->author->notifications->first();
        $response = $this->actingAs($post->author)->put(route('notifications.read', ['uuid' => $notification->id]), [])->assertStatus(200);
        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(2, $response->json('data.numberOfUnreadNotifications'));

        $response = $this->actingAs($post->author)->post(route('notifications.all-read'), [])->assertStatus(200);
        $response = $this->actingAs($post->author)->get(route('users.stats'))->assertStatus(200);
        $this->assertSame(0, $response->json('data.numberOfUnreadNotifications'));
    }

    /** @test */
    public function it_sends_a_notification_to_the_author_when_they_get_a_reply_to_a_comment()
    {
        Notification::fake();

        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.comments.store', ['postUuid' => $comment->root_uuid]), ['markdown' => '#Comment', 'parentUuid' => $comment->uuid])->assertStatus(200);

        Notification::assertSentTo($comment->author, NewReplyNotification::class);
    }

    /** @test */
    public function it_does_not_send_a_notification_if_you_are_an_author()
    {
        Notification::fake();

        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->author)->post(route('posts.comments.store', ['postUuid' => $comment->root_uuid]), ['markdown' => '#Comment', 'parentUuid' => $comment->uuid])->assertStatus(200);

        Notification::assertNothingSent();
    }

    /** @test */
    public function it_returns_a_list_of_notifications()
    {
        $post = Post::factory()->markdownPost()->create();
        $comment = Comment::factory()->make(['root_uuid' => $post->uuid]);
        $user = User::factory()->create();

        $user->notify(new NewReplyNotification(new CommentCreated(new CommentCreatedData($user->uuid, $comment->root_uuid, $comment->parent_uuid, '')), $post));
        $user->notify(new NewReplyNotification(new CommentCreated(new CommentCreatedData($user->uuid, $comment->root_uuid, $comment->parent_uuid, '')), $post));

        $response = $this->actingAs($user)->get(route('notifications.index'))->assertStatus(200);

        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_set_as_read_a_notification()
    {
        $post = Post::factory()->markdownPost()->create();
        $comment = Comment::factory()->make(['root_uuid' => $post->uuid]);
        $user = User::factory()->create();

        $user->notify(new NewReplyNotification(new CommentCreated(new CommentCreatedData($user->uuid, $comment->root_uuid, $comment->parent_uuid, '')), $post));

        $notification = $user->notifications->first();

        $response = $this->actingAs($user)->put(route('notifications.read', ['uuid' => $notification->id]), [])->assertStatus(200);

        $response = $this->actingAs($user)->get(route('notifications.index'))->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertNotNull($response->json('data')['0']['readAt']);
    }

    /** @test */
    public function it_set_all_notifications_as_read()
    {
        $post = Post::factory()->markdownPost()->create();
        $comment = Comment::factory()->make(['root_uuid' => $post->uuid]);
        $user = User::factory()->create();

        $user->notify(new NewReplyNotification(new CommentCreated(new CommentCreatedData($user->uuid, $comment->root_uuid, $comment->parent_uuid, '')), $post));
        $user->notify(new NewReplyNotification(new CommentCreated(new CommentCreatedData($user->uuid, $comment->root_uuid, $comment->parent_uuid, '')), $post));

        $response = $this->actingAs($user)->post(route('notifications.all-read'), [])->assertStatus(200);

        $response = $this->actingAs($user)->get(route('notifications.index'))->assertStatus(200);

        $this->assertNotNull($response->json('data')['0']['readAt']);
        $this->assertNotNull($response->json('data')['1']['readAt']);
    }
}
