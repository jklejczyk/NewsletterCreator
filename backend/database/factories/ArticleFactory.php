<?php

namespace Database\Factories;

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Enums\ArticleSource;
use App\Domain\Article\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'summary' => fake()->optional()->paragraph(),
            'url' => fake()->unique()->url(),
            'source' => fake()->randomElement(ArticleSource::cases()),
            'category' => fake()->optional()->randomElement(ArticleCategory::cases()),
            'published_at' => fake()->dateTimeBetween('-1 month'),
            'imported_at' => now(),
            'is_processed' => false,
        ];
    }

    public function processed(): static
    {
        return $this->state([
            'is_processed' => true,
        ]);
    }
}
