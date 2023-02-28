<?php

namespace Iotronlab\FilamentMultiGuard\Tests\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Iotronlab\FilamentMultiGuard\Tests\app\Models\Post;


class PostFactory extends Factory
{

    protected $model = Post::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'body' => fake()->paragraphs(asText: true),
        ];
    }
}
