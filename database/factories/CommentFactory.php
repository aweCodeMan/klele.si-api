<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Markdown;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $uuid = Str::orderedUuid()->toString();

        Markdown::create([
            'uuid' => $uuid,
            'html' => $this->faker->randomHtml,
            'markdown' => $this->faker->paragraphs(6, true),
        ]);

        return [
            'uuid' => $uuid,
            'author_uuid' => User::factory()->create()->uuid,
            'root_uuid' => Post::factory()->markdownPost()->create()->uuid,
            'parent_uuid' => null,
        ];
    }


}
