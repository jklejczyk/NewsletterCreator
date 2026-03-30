<?php

namespace Database\Factories;

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Newsletter\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'preferences' => fake()->randomElements(
                array_column(ArticleCategory::cases(), 'value'),
                fake()->numberBetween(1, count(ArticleCategory::cases())),
            ),
            'is_active' => true,
            'confirmed_at' => now(),
        ];
    }

    public function unconfirmed(): static
    {
        return $this->state([
            'confirmed_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }
}
