<?php

namespace Tests\Feature;

use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tests\TestCase;

class PinnedPostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_pinned_posts()
    {
        Post::factory()->markdownPost()->create(['pinned_at' => now()]);

        $response = $this->get(route('pinned-posts.index'))->assertStatus(200);

        $this->assertCount(1, $response->json('data'));
    }

    /** @test */
    public function it_returns_pinned_posts_by_group()
    {
        $pin = Post::factory()->markdownPost()->create(['pinned_at' => now()]);
        Post::factory()->markdownPost()->create(['pinned_at' => now()]);

        $response = $this->get(route('pinned-posts.index', ['groupUuid' => $pin->group_uuid]))->assertStatus(200);

        $this->assertCount(1, $response->json('data'));
        $this->assertSame($pin->uuid, $response->json(['data'])[0]['uuid']);
    }

    /** @test */
    public function it_calculates_pinned_progress_in_post_resource()
    {
        $request = Request::capture();

        //  Normal post
        $pin = Post::factory()->markdownPost()->make();
        $resource = (new PostResource($pin))->toArray($request);

        $this->assertNull($resource['pinnedAt']);
        $this->assertNull($resource['pinnedUntil']);
        $this->assertSame(0, $resource['pinnedProgress']);

        //  Pinned forever
        $pin = Post::factory()->markdownPost()->make(['pinned_at' => now()]);
        $resource = (new PostResource($pin))->toArray($request);

        $this->assertNotNull($resource['pinnedAt']);
        $this->assertNull($resource['pinnedUntil']);
        $this->assertSame(0, $resource['pinnedProgress']);

        //  Pinned last day
        $pin = Post::factory()->markdownPost()->make(['pinned_at' => now()->subDay(), 'pinned_until' => now()]);
        $resource = (new PostResource($pin))->toArray($request);

        $this->assertNotNull($resource['pinnedAt']);
        $this->assertNotNull($resource['pinnedUntil']);
        $this->assertSame(100, $resource['pinnedProgress']);

        //  Pinned last day
        $pin = Post::factory()->markdownPost()->make(['pinned_at' => now(), 'pinned_until' => now()]);
        $resource = (new PostResource($pin))->toArray($request);

        $this->assertNotNull($resource['pinnedAt']);
        $this->assertNotNull($resource['pinnedUntil']);
        $this->assertSame(100, $resource['pinnedProgress']);

        //  Pinned start (it should start at 20%, as pinned post should never have 0%)
        $pin = Post::factory()->markdownPost()->make(['pinned_at' => now(), 'pinned_until' => now()->addDays(4)]);
        $resource = (new PostResource($pin))->toArray($request);

        $this->assertNotNull($resource['pinnedAt']);
        $this->assertNotNull($resource['pinnedUntil']);
        $this->assertSame(20, $resource['pinnedProgress']);
    }

    /** @test */
    public function the_reset_command_resets_pinned_columns_for_past_pinned_posts()
    {
        $normal = Post::factory()->markdownPost()->create();
        $foreverPinned = Post::factory()->markdownPost()->create(['pinned_at' => now()]);
        $shouldBeStillPinned = Post::factory()->markdownPost()->create(['pinned_at' => now()->subDays(1), 'pinned_until' => now()]); // until the end of the day!
        $shouldBeReset = Post::factory()->markdownPost()->create(['pinned_at' => now()->subDays(3), 'pinned_until' => now()->subDay()]);

        $this->artisan('klele:reset-pinned-posts');

        $this->assertDatabaseHas('posts', ['uuid' => $shouldBeReset->uuid, 'pinned_at' => null, 'pinned_until' => null]);
        $this->assertDatabaseHas('posts', ['uuid' => $shouldBeStillPinned->uuid, 'pinned_at' => now()->subDays(1), 'pinned_until' => now()]);
        $this->assertDatabaseHas('posts', ['uuid' => $foreverPinned->uuid, 'pinned_at' => now(), 'pinned_until' => null]);
        $this->assertDatabaseHas('posts', ['uuid' => $normal->uuid, 'pinned_at' => null, 'pinned_until' => null]);
    }

    /** @test */
    public function the_create_command_creates_weekly_pinned_posts()
    {
        User::factory()->create();
        Group::factory()->create(['slug' => 'programiranje']);

        $this->assertDatabaseCount('posts', 0);

        $this->artisan('klele:create-weekly-pinned-posts');

        $this->assertDatabaseCount('posts', 1);

        $currentDayOfWeek = now()->dayOfWeek;
        $lastMonday = now()->isMonday() ? now() : now()->subDays($currentDayOfWeek === 0 ? 6 : ($currentDayOfWeek - 1));

        $this->assertDatabaseHas('posts', ['pinned_at' => $lastMonday->toDateString(), 'pinned_until' => $lastMonday->addDays(6)->toDateString()]);
    }
}
