<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Markdown;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::orderedUuid()->toString(),
            'title' => $this->faker->name,
            'author_uuid' => User::factory()->create()->uuid,
            'group_uuid' => Group::factory()->create()->uuid,
        ];
    }

    public function markdownPost()
    {
        return $this->state(function (array $attributes){
            Markdown::create([
                'uuid' => $attributes['uuid'],
                'html' => $this->faker->randomHtml,
                'markdown' => $this->faker->paragraphs(6, true),
            ]);

           return [
               'post_type' => Post::TYPE_MARKDOWN,
           ] ;
        });

    }
}
